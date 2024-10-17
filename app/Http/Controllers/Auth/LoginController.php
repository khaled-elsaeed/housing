<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginPage(){
        return view('auth.login');
    }

    public function login(Request $request){
        $credintials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt($credintials)){
            $request->session()->regenerate();
            return redirect()->intended('welcome');
        }
        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }
}
