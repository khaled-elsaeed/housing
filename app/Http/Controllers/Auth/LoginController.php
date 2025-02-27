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

        $ipKey = 'login:ip:' . $request->ip();
        $userKey = 'login:' . $request->ip() . '|' . $request->input('identifier');

        // IP-wide rate limiting (50 attempts per minute)
        if (RateLimiter::tooManyAttempts($ipKey, 15)) {
            Log::channel('security')->alert('Too many login attempts from IP', [
                'ip_hash' => hash('sha256', $request->ip()),
                'user_agent_hash' => hash('sha256', $request->header('User-Agent')),
                'action' => 'ip_rate_limit_exceeded',
            ]);
            return redirect()
                ->route('login')
                ->withErrors(['error' => trans('too_many_attempts')]);
        }

        // User-specific rate limiting (5 attempts per minute)
        if (RateLimiter::tooManyAttempts($userKey, 5)) {
            Log::channel('security')->alert('Suspicious login activity detected', [
                'ip_hash' => hash('sha256', $request->ip()),
                'identifier_hash' => hash('sha256', $request->input('identifier')),
                'user_agent_hash' => hash('sha256', $request->header('User-Agent')),
                'action' => 'user_rate_limit_exceeded',
            ]);
            RateLimiter::hit($ipKey); // Increment IP limiter too
            return redirect()
                ->route('login')
                ->withErrors(['error' => trans('too_many_attempts')]);
        }

        $credentials = $request->only('identifier', 'password');
        $user = $this->loginService->findUserByEmailOrNationalId($credentials['identifier']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($userKey);
            RateLimiter::hit($ipKey);
            Log::channel('security')->warning('Login failed', [
                'ip_hash' => hash('sha256', $request->ip()),
                'identifier_hash' => hash('sha256', $credentials['identifier']),
                'user_agent_hash' => hash('sha256', $request->header('User-Agent')),
                'action' => $user ? 'invalid_password' : 'user_not_found',
            ]);
            return back()->withErrors(['credentials' => trans('invalid_credentials')]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        RateLimiter::clear($userKey);
        RateLimiter::clear($ipKey);

        Log::channel('security')->info('User logged in successfully', [
            'user_id' => $user->id,
            'ip_hash' => hash('sha256', $request->ip()),
            'user_agent_hash' => hash('sha256', $request->header('User-Agent')),
            'session_id' => $request->session()->getId(),
            'action' => 'login_success',
        ]);

        if ($this->loginService->isAdmin($user)) {
            return redirect()->route('admin.home');
        }

        if ($this->loginService->isTechnician($user)) { // Fixed typo
            return redirect()->route('staff.maintenance.index');
        }

        if ($this->loginService->isResident($user)) {
            if ($user->profile_completed === 0) {
                return redirect()->route('profile.complete');
            }

            $result = $this->loginService->handleStudentAfterLogin($user);
            if ($result['status'] === 'error') {
                return back()->withErrors($result['checks']);
            }

            return redirect()->route('student.home');
        }

        return redirect()->intended('home');
    }
}