<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserNationalLink;
use App\Models\UniversityArchive;
use Illuminate\Support\Facades\Hash;
use App\Notifications\AccountActivation;
use Illuminate\Support\Str;
use App\Models\Setting;
use Carbon\Carbon;

class RegisterController extends Controller
{
  

    public function showRegisterPage()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if($this->registerIsopen() == false){
            return back()->withErrors( __('auth.register.registration_closed'));
        };
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'nationalId' => 'required|digits:14',
        ]);

        $credentials = $request->only('email', 'password', 'nationalId');

        if (!UniversityArchive::isUniversityStudent($credentials['nationalId'])) {
            return back()
                ->withErrors(['national_id' => __('auth.register.student_not_registered')])
                ->withInput();
        }

        if (UserNationalLink::where('national_id', $credentials['nationalId'])->exists()) {
            return back()
                ->withErrors(['national_id' => __('auth.register.national_id_registered')])
                ->withInput();
        }

        if (User::where('email', $credentials['email'])->exists()) {
            return back()
                ->withErrors(['email' => __('auth.register.email_registered')])
                ->withInput();
        }

        $studentUsername = UniversityArchive::getStudentUsername($credentials['nationalId']);

        $user = User::create([
            'username_ar' => $studentUsername['username_ar'],
            'username_en' => $studentUsername['username_en'],
            'password' => Hash::make($credentials['password']),
            'email' => $credentials['email'],
            'activation_token' => Str::random(60),
            'is_active' => 1,
            'activation_expires_at' => Carbon::now()->addHours(2),
        ]);

        $user->assignRole('resident');

        UserNationalLink::create([
            'user_id' => $user->id,
            'national_id' => $credentials['nationalId'],
            'university_Archive_id' => UniversityArchive::where('national_id', $credentials['nationalId'])->first()->id, // Fixed: should retrieve the ID from the model
        ]);

        $user->notify(new AccountActivation($user));

        session()->flash('success', __('auth.register.user_registered_successfully'));
        return redirect()
            ->route('login')
            ->withInput();
    }

    public function registerIsopen()
    {
        $isOpen = Setting::where('key', 'registration_open')->first();
        if ($isOpen->value == 1) {
            return true;
        }else{
            return false;
        }
    }
}
