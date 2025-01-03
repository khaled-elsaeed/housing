<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LoginService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;

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
            return redirect()
                ->route('login')
                ->withErrors(['error' => __('auth.too_many_login_attempts')]);
        }

        $credentials = $request->only('identifier', 'password');

        $user = $this->loginService->findUserByEmailOrNationalId($credentials['identifier']);

        if (!$user) {
            RateLimiter::hit($rateLimiterKey);
            return back()->withErrors(['credentials' => __('auth.login.user_not_found')]);
        }

        if (Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            RateLimiter::clear($rateLimiterKey);

            if ($this->loginService->isAdmin($user)) {
                return redirect()->route('admin.home');
            }

              if ($this->loginService->isResident($user)) {
                $studentChecks = $this->loginService->handleStudentAfterLogin($user);
               
                if (is_array($studentChecks)) {
                    return back()->withErrors($studentChecks);
                }
                return redirect()->route('student.home');
            }

           

            return redirect()->intended('home');
        }

        RateLimiter::hit($rateLimiterKey);
        return back()->withErrors(['credentials' => __('auth.login.invalid_credentials')]);
    }
}
