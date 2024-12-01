<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log; // Ensure Log is imported

class StudentHomeController extends Controller
{
    /**
     * Show the student's home page.
     */
    public function index()
    {
        try {
            $user = auth()->user();

            $Reservation = $user->reservation()
                ->where('status', 'confirmed') 
                ->latest()
                ->first();

            return view('student.home', compact('user', 'Reservation'));
        } catch (\Exception $e) {
            Log::error('Error loading student home: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Something went wrong while loading the page.');
        }
    }
}
