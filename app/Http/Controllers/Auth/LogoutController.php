<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        Log::channel('security')->info('User logged out', [
            'user_id' => $user ? $user->id : null,
            'ip' => Crypt::encryptString($request->ip()),
            'user_agent' => Crypt::encryptString($request->header('User-Agent')),
            'session_id' => $request->session()->getId(),
            'action' => 'logout',
        ]);

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}