<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\PasswordResetTokens;
use App\Notifications\PasswordReset;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class PasswordResetController extends Controller
{
    public function showResetRequestForm()
    {
        return view('auth.password.reset');
    }

    public function showUpdatePasswordPage($token)
    {
        return view('auth.password.update', ['token' => $token]);
    }

    public function requestResetPassword(Request $request)
    {
        try {
            // Validate the email input
            $validator = validator(['email' => $request->email], [
                'email' => 'required|email|exists:users,email'
            ]);

            if ($validator->fails()) {
                // Log non-existent email attempt
                Log::channel('security')->warning('Password reset attempted with non-existent email', [
                    'email' => $request->email,
                    'ip' => Crypt::encryptString($request->ip()),
                    'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                    'action' => 'password_reset_invalid_email',
                ]);

                return redirect()
                    ->route('login')
                    ->withErrors(['email' => trans('We could not find a user with that email address.')]);
            }

            $email = $request->email;

            // Throttle password reset attempts
            $throttleTime = 60; // Throttle time in seconds
            $key = 'password_reset_attempts:' . $email;

            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                $minutes = ceil($seconds / 60); // Calculate remaining minutes

                // Log throttling event
                Log::channel('security')->warning('Password reset throttling triggered', [
                    'email' => $email,
                    'ip' => Crypt::encryptString($request->ip()),
                    'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                    'action' => 'password_reset_throttle',
                ]);

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' => trans('Too many password reset attempts. Please try again in :minutes minutes.', ['minutes' => $minutes]),
                    ]);
            }

            // Increment the rate limiter
            RateLimiter::hit($key, $throttleTime * 60);

            // Generate a token and hash it
            $token = Str::random(60);
            $hashedToken = hash('sha256', $token);

            // Store or update the token in the database
            PasswordResetTokens::updateOrCreate(
                ['email' => $email],
                ['token' => $hashedToken, 'token_expires_at' => Carbon::now()->addHours(2)]
            );

            // Find the user and send the password reset notification
            $user = User::where('email', $email)->first();
            $user->notify(new PasswordReset($user, $token));

            // Log password reset request
            Log::channel('security')->info('Password reset requested', [
                'user_id' => $user->id,
                'email' => $email,
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'password_reset_request',
            ]);

            // Flash success message and redirect
            session()->flash('success', trans('Password reset link has been sent to your email'));
            return redirect()->route('login')->withInput();
        } catch (\Exception $e) {
            Log::channel('security')->error('Password reset request failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'action' => 'password_reset_error'
            ]);

            return redirect()
                ->route('login')
                ->withErrors(['email' => trans('An error occurred while processing your request.')]);
        }
    }

    public function resetPassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        // Hash the token for comparison
        $hashedToken = hash('sha256', $request->token);

        // Find the token in the database
        $tokenData = PasswordResetTokens::where('token', $hashedToken)->first();

        if (!$tokenData) {
            // Log invalid token attempt
            Log::channel('security')->warning('Invalid password reset token', [
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'invalid_password_reset_token',
            ]);

            return back()->withErrors(['token' => trans('Invalid or expired password reset token')]);
        }

        // Check if the token has expired
        if (Carbon::now()->isAfter($tokenData->token_expires_at)) {
            // Log expired token attempt
            Log::channel('security')->warning('Expired password reset token', [
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'expired_password_reset_token',
            ]);

            return back()->withErrors(['token' => trans('Password reset token has expired')]);
        }

        // Find the user associated with the token
        $user = User::where('email', $tokenData->email)->first();

        if (!$user) {
            // Log user not found
            Log::channel('security')->warning('User not found during password reset', [
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'user_not_found',
            ]);

            return back()->withErrors(['email' => trans('User not found')]);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the used token
        $tokenData->delete();

        // Clear the rate limiter for the user's email
        RateLimiter::clear('password_reset_attempts:' . $tokenData->email);

        // Log successful password reset
        Log::channel('security')->info('Password reset successful', [
            'user_id' => $user->id,
            'ip' => Crypt::encryptString($request->ip()),
            'user_agent' => Crypt::encryptString($request->header('User-Agent')),
            'action' => 'password_reset_success',
        ]);

        // Redirect with success message
        return redirect()
            ->route('login')
            ->with('success', trans('Password has been reset successfully'));
    }
}