<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class AccountActivationController extends Controller
{
    public function activate(Request $request, $token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            Log::channel('security')->warning('Invalid activation token attempt', [
                'ip_hash' => hash('sha256', $request->ip()),
                'action' => 'invalid_activation_token',
            ]);
            return redirect()
                ->route('login')
                ->withErrors(['activation' => trans('This activation token is not valid')]);
        }

        if ($user->activation_expires_at && $user->activation_expires_at->isPast()) {
            Log::channel('security')->warning('Expired activation token attempt', [
                'ip_hash' => hash('sha256', $request->ip()),
                'action' => 'expired_activation_token',
            ]);
            return redirect()
                ->route('login')
                ->withErrors(['activation' => trans('This activation link has expired')]);
        }

        try {
            DB::transaction(function () use ($user) {
                $user->is_verified = 1;
                $user->activation_token = null;
                $user->activation_expires_at = null;
                $user->save();
            });
        } catch (\Exception $e) {
            Log::channel('security')->error('Failed to save user activation', [
                'user_id' => $user->id,
                'ip_hash' => hash('sha256', $request->ip()),
                'exception' => $e->getMessage(),
            ]);
            return redirect()
                ->route('login')
                ->withErrors(['activation' => trans('An error occurred during activation')]);
        }

        return redirect()
            ->route('login')
            ->with('success', trans('Your account has been activated successfully'));
    }
}