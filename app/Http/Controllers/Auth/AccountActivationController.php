<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;

class AccountActivationController extends Controller
{
    public function activate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()
                ->route('login')
                ->withErrors(['activation' => __('auth.invalid_activation_token')]);
        }

        if ($user->activation_expires_at && $user->activation_expires_at->isPast()) {
            return redirect()
                ->route('login')
                ->withErrors(['token' => __('auth.activation_link_expired')]);
        }

        $user->is_verified = 1;
        $user->activation_token = null;
        $user->activation_expires_at = null;
        $user->save();

        return redirect()
            ->route('login')
            ->with('success', __('auth.account_activated'));
    }
}
