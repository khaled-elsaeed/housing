<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentAccountController extends Controller
{
    /**
     * Display the list of students.
     */
    public function showStudentPage()
    {
        // Fetch all users with the role 'resident'
        $students = User::role('resident')->get(); 

        // Count total students and gender-based counts
        $totalStudentsCount = $students->count();
        $maleTotalCount = $students->where('gender', 'male')->count();
        $femaleTotalCount = $students->where('gender', 'female')->count();

        // Return the view with students data and counts
        return view('admin.account.student', compact('students', 'totalStudentsCount', 'maleTotalCount', 'femaleTotalCount'));
    }

    public function editEmail(Request $request)
    {
        // Validate email input
        $request->validate([
            'new_email' => 'required|email|unique:users,email',
        ]);
    
        // Find the student by ID
        $student = User::findOrFail($request->student_id);
    
        // Update the student's email
        $student->email = $request->new_email;
        $student->save();
    
        // Redirect back with success message
        return redirect()->route('admin.account.student.index')->with('success', 'Email updated successfully!');
    }
    
    public function resetPassword(Request $request)
    {
        // Validate the password input (ensure both passwords match)
        $request->validate([
            'new_password' => 'required|min:8|confirmed',  // 'confirmed' ensures new_password and confirm_password match
        ]);
    
        // Find the student by ID
        $student = User::findOrFail($request->student_id);
    
        // Update the student's password
        $student->password = bcrypt($request->new_password);
        $student->save();
    
        // Redirect back with success message
        return redirect()->route('admin.account.student.index')->with('success', 'Password reset successfully!');
    }
    
}
