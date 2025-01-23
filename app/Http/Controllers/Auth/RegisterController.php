<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RegisterService;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    protected $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function showRegisterPage()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'password' => 'required|string|min:8',
                'national_id' => [
                    'required',
                    'digits:14',
                    'unique:user_national_link,national_id'
                ],
            ]);

            $this->registerService->registerUser([
                'password' => $validated['password'],
                'national_id' => $validated['national_id'],
            ]);

            session()->flash('success', __('auth.register.user_registered_successfully'));
            return redirect()
                ->route('login')
                ->withInput();

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }
}