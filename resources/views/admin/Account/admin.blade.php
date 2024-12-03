<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentAccountController extends Controller
{
    /**
     * Display the list of students.
     */
    public function index()
    {
        // Fetch all users with the role 'student'
        $students = User::where('role', 'student')->get(); // Assuming you have a 'role' field

        // Return the view with students data
        return view('admin.account.student.index', compact('students'));
    }

    /**
     * Edit the student's email.
     */
    public function editEmail(Request $request, $id)
    {
        // Validate email input
        $request->validate([
            'newEmail' => 'required|email|unique:users,email',
        ]);

        // Find the student by ID
        $student = User::findOrFail($id);
        $student->email = $request->newEmail;
        $student->save();

        return redirect()->route('admin.account.student.index')->with('success', 'Email updated successfully!');
    }

    /**
     * Reset the student's password to default.
     */
    public function resetPassword($id)
    {
        // Define a default password
        $defaultPassword = 'defaultpassword'; // You can make this more secure

        // Find the student by ID
        $student = User::findOrFail($id);
        $student->password = bcrypt($defaultPassword); // Hash the default password
        $student->save();

        return redirect()->route('admin.account.student.index')->with('success', 'Password has been reset to default!');
    }
}
