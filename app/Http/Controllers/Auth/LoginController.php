<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LoginService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function showLoginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $rateLimiterKey = 'login:' . $request->ip() . '|' . $request->input('identifier');

        if (RateLimiter::tooManyAttempts($rateLimiterKey, 5)) {
            Log::channel('security')->alert('Suspicious login activity detected', [
                'ip' => Crypt::encryptString($request->ip()),
                'identifier' => $request->input('identifier'),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'suspicious_activity',
            ]);

            return redirect()
                ->route('login')
                ->withErrors(['error' => trans('Too many login attempts. Please try again later.')]);
        }

        $credentials = $request->only('identifier', 'password');

        $user = $this->loginService->findUserByEmailOrNationalId($credentials['identifier']);

        if (!$user) {
            RateLimiter::hit($rateLimiterKey);
            Log::channel('security')->warning('User not found', [
                'ip' => Crypt::encryptString($request->ip()),
                'identifier' => $credentials['identifier'],
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'user_not_found',
            ]);

            return back()->withErrors(['credentials' => trans('User not found.')]);
        }

        if (Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            RateLimiter::clear($rateLimiterKey);

            Log::channel('security')->info('User logged in successfully', [
                'user_id' => $user->id,
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'session_id' => $request->session()->getId(),
                'action' => 'login_success',
            ]);

            if ($this->loginService->isAdmin($user)) {
                return redirect()->route('admin.home');
            }

            if ($this->loginService->isResident($user)) {
                $result = $this->loginService->handleStudentAfterLogin($user);
                
                if ($result['status'] === 'error') {
                    return back()->withErrors($result['checks']);
                }
                
                if ($user->profile_completed === '0') {
                    return redirect()->route('profile.complete');
                }
                
                return redirect()->route('student.home');
            }

            return redirect()->intended('home');
        }

        RateLimiter::hit($rateLimiterKey);
        Log::channel('security')->warning('Invalid credentials', [
            'ip' => Crypt::encryptString($request->ip()),
            'user_id' => $user->id,
            'user_agent' => Crypt::encryptString($request->header('User-Agent')),
            'action' => 'invalid_password',
        ]);

        return back()->withErrors(['credentials' => trans('Invalid credentials.')]);
    }
}