<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminAction;
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
            $users = User::whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['resident']);
            })->get();

            $totalUsersCount = $users->count();
            $maleTotalCount = $users->where('gender', 'male')->count();
            $femaleTotalCount = $users->where('gender', 'female')->count();

            return view('admin.account.staff', compact('users', 'totalUsersCount', 'maleTotalCount', 'femaleTotalCount'));
        } catch (\Exception $e) {
            Log::error('Failed to load staff user page', [
                'error' => $e->getMessage(),
                'action' => 'show_staff_page',
                'admin_id' => auth()->id(),
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
            $validatedData = $request->validate([
                'first_name_en'     => 'required|string|max:255',
                'first_name_ar'     => 'required|string|max:255',
                'last_name_en'      => 'required|string|max:255',
                'last_name_ar'      => 'required|string|max:255',
                'email'             => 'required|email|unique:users,email',
                'password'          => 'required|string|min:8',
                'role'              => 'required|string|exists:roles,name',
                'technician_role'   => 'nullable|required_if:role,technician|exists:roles,name',
            ]);

            $user = User::create([
                'first_name_en' => $validatedData['first_name_en'],
                'first_name_ar' => $validatedData['first_name_ar'],
                'last_name'     => $validatedData['last_name_en'],
                'email'         => $validatedData['email'],
                'password'      => Hash::make($validatedData['password']),
            ]);

            $role = Role::where('name', $validatedData['role'])->first();
            if (!$role) {
                return response()->json(['success' => false, 'message' => __('Invalid role provided.')], 400);
            }

            $user->assignRole($role->name);

            if ($request->role == 'technician' && $request->technician_role) {
                $technicianRole = Role::where('name', $request->technician_role)->first();
                if ($technicianRole) {
                    $user->assignRole($technicianRole->name);
                } else {
                    return response()->json(['success' => false, 'message' => __('Invalid technician role.')], 400);
                }
            }

            AdminAction::create([
                'admin_id'    => auth()->id(),
                'action'      => 'create_staff',
                'description' => 'Created a new staff user',
                'changes'     => json_encode([
                    'user_id'          => $user->id,
                    'role'             => $validatedData['role'],
                    'technician_role'  => $validatedData['technician_role'] ?? null,
                ]),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            return response()->json(['success' => true, 'message' => __('User added successfully.')]);
        } catch (\Exception $e) {
            Log::error('Failed to create staff user', [
                'error'        => $e->getMessage(),
                'action'       => 'create_staff',
                'request_data' => $request->all(),
                'admin_id'     => auth()->id(),
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
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'first_name_en' => 'required|string|max:255',
                'first_name_ar' => 'required|string|max:255',
                'last_name_en' => 'required|string|max:255',
                'last_name_ar' => 'required|string|max:255',

                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'role' => 'required|string|in:admin,housing_manager,building_manager,technician',
            ]);

            $user = User::findOrFail($request->user_id);
            $user->update([
                'first_name_en' => $request->first_name_en,
                'first_name_ar' => $request->first_name_ar,
                'last_name_ar' => $request->last_name_ar,
                'last_name_en' => $request->last_name_en,
                'email' => $request->email,
            ]);

            $user->syncRoles([$request->role]);

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
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $user = User::findOrFail($request->user_id);
            $user->delete();

            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'delete_staff',
                'description' => 'Deleted a staff user',
                'changes' => json_encode(['user_id' => $user->id]),
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
            $oldEmail = $user->email;
            $user->email = $request->new_email;
            $user->save();

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
            $users = User::where('id', $user->id)->get();
            ResetAccountCredentials::dispatch($users);

            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'reset_password',
                'description' => 'Reset staff user password',
                'changes' => json_encode(['user_id' => $user->id]),
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