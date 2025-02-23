<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminAction; // Assuming you have a model for admin action logs
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\ResetAccountCredentials;

class ResidentAccountController extends Controller
{
    /**
     * Display the list of resident users.
     *
     * @return \Illuminate\View\View
     */
    public function showUserPage()
    {
        try {
            $users = User::with('student')->role('resident')->get();

            return view('admin.account.resident', [
                'users' => $users,
                'totalUsersCount' => $users->count(),
                'maleTotalCount' => $users->where('gender', 'male')->count(),
                'femaleTotalCount' => $users->where('gender', 'female')->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load resident user page', [
                'error' => $e->getMessage(),
                'action' => 'show_user_page',
                'admin_id' => auth()->id(), // Log the admin performing the action
            ]);
            return response()->view('errors.500');
        }
    }

    /**
     * Edit a resident user's email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editEmail(Request $request)
    {
        try {
            $request->validate([
                'new_email' => 'required|email|unique:users,email',
            ]);

            $user = User::findOrFail($request->user_id);
            $oldEmail = $user->email;
            $user->update(['email' => $request->new_email]);

            // Log admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'update_email',
                'description' => 'Updated resident user email',
                'changes' => json_encode([
                    'user_id' => $user->id,
                    'old_email' => $oldEmail,
                    'new_email' => $request->new_email,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => trans('messages.email_updated_successfully'),
                'data' => [
                    'user_id' => $user->id,
                    'new_email' => $request->new_email,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update resident email', [
                'error' => $e->getMessage(),
                'action' => 'edit_email',
                'user_id' => $request->user_id,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => trans('messages.unable_to_update_email'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset a resident user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
{
    try {
        
        $user = User::findOrFail($request->user_id);

        // Use Eloquent query to get an Eloquent Collection
        ResetAccountCredentials::dispatch(User::whereIn('id', [$request->user_id])->get());

        // Log admin action
        AdminAction::create([
            'admin_id' => auth()->id(),
            'action' => 'reset_password',
            'description' => 'Reset resident user password',
            'changes' => json_encode([
                'user_id' => $user->id,
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => trans('messages.password_reset_successfully'),
            'data' => [
                'user_id' => $user->id,
            ],
        ]);
    } catch (\Exception $e) {
        Log::error('Failed to reset resident password', [
            'error' => $e->getMessage(),
            'action' => 'reset_password',
            'user_id' => $request->user_id,
            'admin_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => false,
            'message' => trans('messages.unable_to_reset_password'),
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Reset passwords for all resident users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetAllUsersPasswords()
    {
        try {

            $residents = User::role('resident')->get();
            ResetAccountCredentials::dispatch($residents);

            // Log admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'reset_all_users_password',
                'description' => 'Reset all resident user passwords',
                'changes' => json_encode([
                    'total_residents' => $residents->count(),
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('All resident passwords have been reset.'),
                'data' => [
                    'total_residents' => $residents->count(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to reset all resident passwords', [
                'error' => $e->getMessage(),
                'action' => 'reset_all_users_password',
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => trans('messages.unable_to_reset_password'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}