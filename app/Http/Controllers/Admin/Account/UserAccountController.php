<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserAccountController extends Controller
{
    /**
     * Display the list of Users.
     */
    public function showUserPage()
{
    try {
        // Retrieve all Users with the 'resident' role
        $Users = User::with('student')->role('resident')->get();

        // Count total, male, and female Users
        $totalUsersCount = $Users->count();
        $maleTotalCount = $Users->where('gender', 'male')->count();
        $femaleTotalCount = $Users->where('gender', 'female')->count();

        // Pass the data to the view
        return view('admin.account.index', compact('users', 'totalUsersCount', 'maleTotalCount', 'femaleTotalCount'));
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

            $User = User::findOrFail($request->User_id);

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
            $request->validate([
                'new_password' => 'required|min:8|confirmed',
            ]);

            $User = User::findOrFail($request->User_id);

            $User->password = bcrypt($request->new_password);
            $User->save();

            return redirect()->route('admin.account.index')->with('success', trans('messages.password_reset_successfully'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User not found for password reset: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.User_not_found'));
        } catch (\Exception $e) {
            Log::error('Error resetting password: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.unable_to_reset_password'));
        }
    }
}
