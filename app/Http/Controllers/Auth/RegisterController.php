<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RegisterService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;


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
            // Validate the request
            $validated = $request->validate([
                'password' => 'required|string|min:8',
                'national_id' => [
                    'required',
                    'digits:14',
                    'unique:user_national_link,national_id'
                ],
            ]);

            // Log registration attempt
            Log::channel('security')->info('User registration attempt', [
                'national_id' => Crypt::encryptString($validated['national_id']), 
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'registration_attempt',
            ]);

            // Register the user
            $user = $this->registerService->registerUser([
                'password' => $validated['password'],
                'national_id' => $validated['national_id'],
            ]);

            // Log successful registration
            Log::channel('security')->info('User registered successfully', [
                'national_id' => Crypt::encryptString($validated['national_id']),
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'registration_success',
            ]);

            Auth::login($user);
            // Flash success message and redirect
            return redirect()->route('profile.complete');

        } catch (ValidationException $e) {
            // Log validation errors
            Log::channel('security')->warning('User registration validation failed', [
                'errors' => $e->errors(),
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'registration_validation_failed',
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }
}