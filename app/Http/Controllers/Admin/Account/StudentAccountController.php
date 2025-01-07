<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentAccountController extends Controller
{
    /**
     * Display the list of students.
     */
    public function showStudentPage()
    {
        try {
            $students = User::role('resident')->get();

            $totalStudentsCount = $students->count();
            $maleTotalCount = $students->where('gender', 'male')->count();
            $femaleTotalCount = $students->where('gender', 'female')->count();

            return view('admin.account.student', compact('students', 'totalStudentsCount', 'maleTotalCount', 'femaleTotalCount'));
        } catch (\Exception $e) {
            Log::error('Error loading student page: ' . $e->getMessage());
            return response()->view('errors.500');
        }
    }

    /**
     * Edit student email.
     */
    public function editEmail(Request $request)
    {
        try {
            $request->validate([
                'new_email' => 'required|email|unique:users,email',
            ]);

            $student = User::findOrFail($request->student_id);

            $student->email = $request->new_email;
            $student->save();

            return redirect()->route('admin.account.student.index')->with('success', trans('messages.email_updated_successfully'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Student not found for email update: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.student_not_found'));
        } catch (\Exception $e) {
            Log::error('Error updating email: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.unable_to_update_email'));
        }
    }

    /**
     * Reset student password.
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'new_password' => 'required|min:8|confirmed',
            ]);

            $student = User::findOrFail($request->student_id);

            $student->password = bcrypt($request->new_password);
            $student->save();

            return redirect()->route('admin.account.student.index')->with('success', trans('messages.password_reset_successfully'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Student not found for password reset: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.student_not_found'));
        } catch (\Exception $e) {
            Log::error('Error resetting password: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('messages.unable_to_reset_password'));
        }
    }
}
