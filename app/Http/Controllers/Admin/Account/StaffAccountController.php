<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminAction; // Assuming you have a model for admin action logs
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Jobs\ResetAccountCredentials;
use Spatie\Permission\Models\Role;

class StaffAccountController extends Controller
{
    /**
     * Display the list of staff users.
     */
    public function index()
    {
        try {
            // Retrieve all users except those with the 'resident' role
            $users = User::whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['resident']);
            })->get();

            // Count total, male, and female users
            $totalUsersCount = $users->count();
            $maleTotalCount = $users->where('gender', 'male')->count();
            $femaleTotalCount = $users->where('gender', 'female')->count();

            // Pass the data to the view
            return view('admin.account.staff', compact('users', 'totalUsersCount', 'maleTotalCount', 'femaleTotalCount'));
        } catch (\Exception $e) {
            // Log the error and return a 500 error page
            Log::error('Failed to load staff user page', [
                'error' => $e->getMessage(),
                'action' => 'show_staff_page',
                'admin_id' => auth()->id(), // Log the admin performing the action
            ]);
            return response()->view('errors.500');
        }
    }

    /**
     * Add a new staff user.
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

            // Log the admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'create_staff',
                'description' => 'Created a new staff user',
                'changes' => json_encode([
                    'user_id' => $user->id,
                    'role' => $request->role,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json(['success' => true, 'message' => __('User added successfully.')]);
        } catch (\Exception $e) {
            Log::error('Failed to create staff user', [
                'error' => $e->getMessage(),
                'action' => 'create_staff',
                'request_data' => $request->all(),
                'admin_id' => auth()->id(),
            ]);
            return response()->json(['success' => false, 'message' => __('Unable to add user.')], 500);
        }
    }

    /**
     * Edit staff user details.
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

            // Log the admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'update_staff',
                'description' => 'Updated staff user details',
                'changes' => json_encode($request->all()),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json(['success' => true, 'message' => __('User updated successfully.')]);
        } catch (\Exception $e) {
            Log::error('Failed to update staff user', [
                'error' => $e->getMessage(),
                'action' => 'update_staff',
                'user_id' => $request->user_id,
                'request_data' => $request->all(),
                'admin_id' => auth()->id(),
            ]);
            return response()->json(['success' => false, 'message' => __('Unable to update user.')], 500);
        }
    }

    /**
     * Delete a staff user.
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

            // Log the admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'delete_staff',
                'description' => 'Deleted a staff user',
                'changes' => json_encode([
                    'user_id' => $user->id,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json(['success' => true, 'message' => __('User deleted successfully.')]);
        } catch (\Exception $e) {
            Log::error('Failed to delete staff user', [
                'error' => $e->getMessage(),
                'action' => 'delete_staff',
                'user_id' => $request->user_id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json(['success' => false, 'message' => __('Unable to delete user.')], 500);
        }
    }

    /**
     * Edit staff user email.
     */
    public function editEmail(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'new_email' => 'required|email|unique:users,email',
            ]);

            $user = User::findOrFail($request->user_id);

            // Log the old email
            $oldEmail = $user->email;

            // Update the email
            $user->email = $request->new_email;
            $user->save();

            // Log the admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'update_email',
                'description' => 'Updated staff user email',
                'changes' => json_encode([
                    'user_id' => $user->id,
                    'old_email' => $oldEmail,
                    'new_email' => $request->new_email,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json(['success' => true, 'message' => __('Email updated successfully.')]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User not found', [
                'error' => $e->getMessage(),
                'action' => 'edit_email',
                'user_id' => $request->user_id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json(['success' => false, 'message' => __('User not found.')], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update staff email', [
                'error' => $e->getMessage(),
                'action' => 'edit_email',
                'user_id' => $request->user_id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json(['success' => false, 'message' => __('Unable to update email.')], 500);
        }
    }

    /**
     * Reset staff user password.
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

            // Dispatch the job to reset credentials
            ResetAccountCredentials::dispatch($users);

            // Log the admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'reset_password',
                'description' => 'Reset staff user password',
                'changes' => json_encode([
                    'user_id' => $user->id,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json(['success' => true, 'message' => __('Password reset successfully.')]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User not found', [
                'error' => $e->getMessage(),
                'action' => 'reset_password',
                'user_id' => $request->user_id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json(['success' => false, 'message' => __('User not found.')], 404);
        } catch (\Exception $e) {
            Log::error('Failed to reset staff password', [
                'error' => $e->getMessage(),
                'action' => 'reset_password',
                'user_id' => $request->user_id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json(['success' => false, 'message' => __('Unable to reset password.')], 500);
        }
    }
}