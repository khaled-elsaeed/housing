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
        $request->validate(['email' => 'required|email|exists:users,email']);
        $email = $request->email;

        $token = Str::random(60);
        $hashedToken = hash('sha256', $token);

        PasswordResetTokens::updateOrCreate(
            ['email' => $email],
            ['token' => $hashedToken, 'token_expires_at' => Carbon::now()->addHours(2)]
        );

        $user = User::where('email', $email)->first();
        $user->notify(new PasswordReset($user, $token));

        session()->flash('success', __('auth.password_reset_link_sent'));
        return redirect()->route('login')->withInput();
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['token' => 'required', 'password' => 'required|confirmed|min:8']);
        $hashedToken = hash('sha256', $request->token);

        $tokenData = PasswordResetTokens::where('token', $hashedToken)->first();
        if (!$tokenData) {
            return back()->withErrors(['token' => __('auth.invalid_or_expired_token')]);
        }

        if (Carbon::now()->isAfter($tokenData->token_expires_at)) {
            return back()->withErrors(['token' => __('auth.token_expired')]);
        }

        $user = User::where('email', $tokenData->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => __('auth.user_not_found')]);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $tokenData->delete();
    
        return redirect()->route('login')->with('success', __('auth.password_reset_success'));
    }
}
