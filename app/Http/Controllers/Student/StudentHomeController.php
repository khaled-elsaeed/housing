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
            // Get the authenticated student
            $user = auth()->user();

            // Fetch the active reservation
            $activeReservation = $user->reservation()
                ->where('status', 'active') // Filter for active reservations
                ->latest() // Get the most recent active reservation
                ->first();

            // Pass the user and reservation data to the view
            return view('student.home', compact('user', 'activeReservation'));
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error loading student home: ' . $e->getMessage());

            // Redirect with an error message
            return redirect()->route('home')->with('error', 'Something went wrong while loading the page.');
        }
    }
}
