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
        $students = User::role('resident')->get(); 

        $totalStudentsCount = $students->count();
        $maleTotalCount = $students->where('gender', 'male')->count();
        $femaleTotalCount = $students->where('gender', 'female')->count();

        return view('admin.account.student', compact('students', 'totalStudentsCount', 'maleTotalCount', 'femaleTotalCount'));
    }

    public function editEmail(Request $request)
    {
        $request->validate([
            'new_email' => 'required|email|unique:users,email',
        ]);
    
        $student = User::findOrFail($request->student_id);
    
        $student->email = $request->new_email;
        $student->save();
    
        return redirect()->route('admin.account.student.index')->with('success', 'Email updated successfully!');
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',  
        ]);
    
        $student = User::findOrFail($request->student_id);
    
        $student->password = bcrypt($request->new_password);
        $student->save();
    
        return redirect()->route('admin.account.student.index')->with('success', 'Password reset successfully!');
    }
    
}
