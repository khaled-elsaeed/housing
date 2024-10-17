<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
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

        $credentials = $request->only('email', 'password');

        $inputType = $this->authService->isEmailOrNationalId($credentials['email']);

        if ($inputType === false) {
            return back()->withErrors(['email' => 'The input must be a valid email or a 14-digit national ID.']);
        }

        $user = $inputType === 'email' ? User::where('email', $credentials['email'])->first() : User::where('national_id', $credentials['email'])->first();

        if (!$user) {
            return back()->withErrors(['credentials' => 'No user found with the provided credentials.']);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if ($this->authService->isAdmin($user)) {
                return redirect()->intended('welcome');
            }

            if ($this->authService->isResident($user)) {
                $studentChecks = $this->authService->handleStudentAfterLogin($user);

                if ($studentChecks) {
                    return back()->withErrors($studentChecks);
                }

                return redirect()->intended('welcome');
            }
        }

        return back()->withErrors(['credentials' => __('auth.invalid_credentials')]);
    }
}
