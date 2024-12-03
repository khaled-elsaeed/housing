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
    public function showStudentPage()
    {
        // Fetch all users with the role 'student'
        $students = User::role('resident')->get(); 

        // Count total students and gender-based counts
        $totalStudentsCount = $students->count();
        $maleTotalCount = $students->where('gender', 'male')->count();
        $femaleTotalCount = $students->where('gender', 'female')->count();

        // Return the view with students data and counts
        return view('admin.account.student', compact('students', 'totalStudentsCount', 'maleTotalCount', 'femaleTotalCount'));
    }

    /**
     * Edit the student's email.
     */
    public function editEmail(Request $request)
    {
        // Validate email input
        $request->validate([
            'new_email' => 'required|email|unique:users,email',
        ]);

        // Find the student by ID
        $student = User::findOrFail($request->student_id);
        $student->email = $request->new_email;
        $student->save();

        return redirect()->route('admin.account.student.index')->with('success', 'Email updated successfully!');
    }

    /**
     * Reset the student's password to default.
     */
    public function resetPassword(Request $request)
    {
        // Define a default password
        $defaultPassword = 'defaultpassword'; // You can make this more secure

        // Find the student by ID
        $student = User::findOrFail($request->student_id);
        $student->password = bcrypt($defaultPassword); // Hash the default password
        $student->save();

        return redirect()->route('admin.account.student.index')->with('success', 'Password has been reset to default!');
    }
}
