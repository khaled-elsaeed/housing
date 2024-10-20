<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LoginService;
use App\Models\UserNationalLink;
use Illuminate\Support\Facades\RateLimiter;

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
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (RateLimiter::tooManyAttempts('login:' . $request->ip(), 5)) {
            return redirect()
                ->route('login')
                ->withErrors(['error' => __('auth.too_many_login_attempts')]);

        }

        $credentials = $request->only('email', 'password');

        $inputType = $this->loginService->isEmailOrNationalId($credentials['email']);

        if ($inputType === false) {
            return back()->withErrors(['email' => 'The input must be a valid email or a 14-digit national ID.']);
        }

        $user = null;
        if ($inputType === 'email') {
            $user = User::where('email', $credentials['email'])->first();
        } else {
            $userNationalLink = UserNationalLink::where('national_id', $credentials['email'])->first();
            $user = $userNationalLink ? $userNationalLink->user : null;
        }

        if (!$user) {
            RateLimiter::hit('login:' . $request->ip());
            return back()->withErrors(['credentials' => 'No user found with the provided credentials.']);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            RateLimiter::clear('login:' . $request->ip());

            if ($this->loginService->isAdmin($user)) {
                return redirect()->intended('welcome');
            }

            if ($this->loginService->isResident($user)) {
                $studentChecks = $this->loginService->handleStudentAfterLogin($user);

                if (is_array($studentChecks)) {
                    return back()->withErrors($studentChecks);
                }

                return redirect()->intended('welcome');
            }
        }

        RateLimiter::hit('login:' . $request->ip());

        return back()->withErrors(['credentials' => __('auth.invalid_credentials')]);
    }
}
