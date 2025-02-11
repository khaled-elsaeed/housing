<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\ResetAccountCredentials;

class ResidentAccountController extends Controller
{
    /**
     * Display the list of Users.
     */
    public function showUserPage()
{
    try {
        // Retrieve all Users with the 'resident' role
        $users = User::with('student')->role('resident')->get();

        // Count total, male, and female Users
        $totalUsersCount = $users->count();
        $maleTotalCount = $users->where('gender', 'male')->count();
        $femaleTotalCount = $users->where('gender', 'female')->count();

        // Pass the data to the view
        return view('admin.account.resident', compact('users', 'totalUsersCount', 'maleTotalCount', 'femaleTotalCount'));
    } catch (\Exception $e) {
        // Log the error and return a 500 error page
        Log::error('Error loading User page: ' . $e->getMessage());
        return response()->view('errors.500');
    }
}


    /**
     * Edit User email.
     */
    public function editEmail(Request $request)
    {
        try {
            $request->validate([
                'new_email' => 'required|email|unique:users,email',
            ]);

            $User = User::findOrFail($request->user_id);

            $User->email = $request->new_email;
            $User->save();

            return redirect()->route('admin.account.index')->with('success', trans('messages.email_updated_successfully'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User not found for email update: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.User_not_found'));
        } catch (\Exception $e) {
            Log::error('Error updating email: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.unable_to_update_email'));
        }
    }

    /**
     * Reset User password.
     */
    public function resetPassword(Request $request)
    {
        try {
           
            $user = User::findOrFail($request->user_id);

            // Ensure it's an Eloquent Collection
            $users = User::where('id', $user->id)->get();

            ResetAccountCredentials::dispatch($users);

            return redirect()->route('admin.account.index')->with('success', trans('messages.password_reset_successfully'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User not found for password reset: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.User_not_found'));
        } catch (\Exception $e) {
            Log::error('Error resetting password: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.unable_to_reset_password'));
        }
    }

    public function resetAllUsersPasswords()
{
    try{
    // Find all resident users
    $residents = User::role('resident')->get();

    // Use a job to process password resets
    ResetAccountCredentials::dispatch($residents);

    return redirect()->back()->with('success', __('All resident passwords have been reset.'));
    } catch (\Exception $e) {
        Log::error('Error resetting for all users: ' . $e->getMessage());
        return redirect()->back()->with('error', trans('messages.unable_to_reset_password'));
    }
}
}
