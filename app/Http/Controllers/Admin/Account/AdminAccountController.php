<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAccountController extends Controller
{
    /**
     * Display the list of all admins.
     */
    public function index()
    {
        // Fetch all admins
        $admins = User::all();

        // Return the view with admins data
        return view('admin.account.admin.index', compact('admins'));
    }

    /**
     * Edit the admin's email.
     */
    public function editEmail(Request $request, $id)
    {
        // Validate email input
        $request->validate([
            'newEmail' => 'required|email|unique:users,email',
        ]);

        // Find the admin by ID
        $admin = User::findOrFail($id);
        $admin->email = $request->newEmail;
        $admin->save();

        return redirect()->route('admin.account.admin.index')->with('success', 'Email updated successfully!');
    }

    /**
     * Reset the admin's password to default.
     */
    public function resetPassword($id)
    {
        // Define a default password
        $defaultPassword = 'defaultpassword'; // You can make this more secure

        // Find the admin by ID
        $admin = User::findOrFail($id);
        $admin->password = bcrypt($defaultPassword); // Hash the default password
        $admin->save();

        return redirect()->route('admin.account.admin.index')->with('success', 'Password has been reset to default!');
    }
}
