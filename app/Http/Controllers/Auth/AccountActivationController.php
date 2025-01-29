<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Models\User;

class AccountActivationController extends Controller
{
    public function activate(Request $request, $token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            Log::channel('security')->warning('Invalid activation token attempt', [
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'invalid_activation_token',
            ]);
            return redirect()
                ->route('login')
                ->withErrors(['activation' => trans('This activation token is not valid')]);
        }

        if ($user->activation_expires_at && $user->activation_expires_at->isPast()) {

            Log::channel('security')->warning('Expired activation token attempt', [
                'ip' => Crypt::encryptString($request->ip()),
                'user_agent' => Crypt::encryptString($request->header('User-Agent')),
                'action' => 'expired_activation_token',
            ]);
            return redirect()
                ->route('login')
                ->withErrors(['activation' => trans('This activation link has expired')]);
        }

        $user->is_verified = 1;
        $user->activation_token = null;
        $user->activation_expires_at = null;
        $user->save();

        return redirect()
            ->route('login')
            ->with('success', trans('Your account has been activated successfully'));
    }
}
