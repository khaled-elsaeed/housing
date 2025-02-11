<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Jobs\ResetAccountCredentials;
use Spatie\Permission\Models\Role;

class StaffAccountController extends Controller
{
    public function index(){
        try {

            $users = User::whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['resident']);
            })->get();
                
            // Count total, male, and female Users
            $totalUsersCount = $users->count();
            $maleTotalCount = $users->where('gender', 'male')->count();
            $femaleTotalCount = $users->where('gender', 'female')->count();
    
            // Pass the data to the view
            return view('admin.account.staff', compact('users', 'totalUsersCount', 'maleTotalCount', 'femaleTotalCount'));
        } catch (\Exception $e) {
            // Log the error and return a 500 error page
            Log::error('Error loading User page: ' . $e->getMessage());
            return response()->view('errors.500');
        }
    }
    /**
     * Add a new User.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'first_name_en' => 'required|string|max:255',
                'first_name_ar' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:admin,housing_manager,building_manager,technician',
            ]);

            // Create the user
            $user = User::create([
                'first_name_en' => $request->first_name_en,
                'first_name_ar' => $request->first_name_ar,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign the role to the user
            $user->assignRole($request->role);

            return response()->json(['success' => true, 'message' => __('User added successfully.')]);
        } catch (\Exception $e) {
            Log::error('Error adding user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Unable to add user.')], 500);
        }
    }

    /**
     * Edit User details.
     */
    public function update(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'first_name_en' => 'required|string|max:255',
                'first_name_ar' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'role' => 'required|string|in:admin,housing_manager,building_manager,technician',
            ]);

            // Find the user
            $user = User::findOrFail($request->user_id);

            // Update user details
            $user->update([
                'first_name_en' => $request->first_name_en,
                'first_name_ar' => $request->first_name_ar,
                'last_name' => $request->last_name,
                'email' => $request->email,
            ]);

            // Sync the user's role
            $user->syncRoles([$request->role]);

            return response()->json(['success' => true, 'message' => __('User updated successfully.')]);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Unable to update user.')], 500);
        }
    }

    /**
     * Delete a User.
     */
    public function destroy(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            // Find and delete the user
            $user = User::findOrFail($request->user_id);
            $user->delete();

            return response()->json(['success' => true, 'message' => __('User deleted successfully.')]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Unable to delete user.')], 500);
        }
    }

    /**
     * Edit User email.
     */
    public function editEmail(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'new_email' => 'required|email|unique:users,email',
            ]);

            $user = User::findOrFail($request->user_id);

            $user->email = $request->new_email;
            $user->save();

            return response()->json(['success' => true, 'message' => __('Email updated successfully.')]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User not found for email update: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('User not found.')], 404);
        } catch (\Exception $e) {
            Log::error('Error updating email: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Unable to update email.')], 500);
        }
    }

    /**
     * Reset User password.
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $user = User::findOrFail($request->user_id);

            // Ensure it's an Eloquent Collection
            $users = User::where('id', $user->id)->get();

            ResetAccountCredentials::dispatch($users);

            return response()->json(['success' => true, 'message' => __('Password reset successfully.')]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User not found for password reset: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('User not found.')], 404);
        } catch (\Exception $e) {
            Log::error('Error resetting password: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Unable to reset password.')], 500);
        }
    }

}