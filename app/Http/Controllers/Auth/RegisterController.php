<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RegisterService;
use App\Models\User;
use App\Models\UserNationalLink;
use Illuminate\Support\Facades\Hash;
use App\Notifications\AccountActivation;
use Illuminate\Support\Str;
use Carbon\Carbon;


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
        
        $request->validate([
            'email'      => 'required|email',
            'password'   => 'required|string|min:8',
            'nationalId' => 'required|digits:14',
        ]);


        $credentials = $request->only('email', 'password', 'nationalId');

       
        if (!$this->registerService->isUniversityStudent($credentials['nationalId'])) {
            return back()->withErrors(['national_id' => 'Student is not registered in our university.'])->withInput();
        }

        
        if ($this->registerService->isNationalIdRegistered($credentials['nationalId'])) {
            return back()->withErrors(['national_id' => 'National ID is already registered.'])->withInput();
        }

       
        if ($this->registerService->isEmailRegistered($credentials['email'])) {
            return back()->withErrors(['email' => 'Email is already registered.'])->withInput();
        }

        
        $studentData = $this->registerService->getStudentData($credentials['nationalId']);

        
        $user = User::create([
            'username_ar' => $studentData['updated_name_ar'],
            'username_en' => $studentData['updated_name_en'],
            'password'    => Hash::make($credentials['password']),
            'email'       => $credentials['email'],
            'activation_token' => Str::random(60),
            'activation_expires_at' => Carbon::now()->addHours(2), 
        ]);

        
        $user->assignRole('resident');

        
        UserNationalLink::create([
            'user_id'    => $user->id,
            'national_id' => $credentials['nationalId'],
        ]);

        
            $user->notify(new AccountActivation($user));              
        session()->flash('success', 'User registered successfully! A confirmation email has been sent to your email address.');
        return redirect()->route('login')->withInput();
    }
}
