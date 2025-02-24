<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{UserActivity, Reservation, AcademicTerm, User};
use Throwable;

class StudentHomeController extends Controller
{
    /**
     * Show the student dashboard with reservation details, activities, roommates, and academic terms.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $reservation = $user->activeReservation();
            $activities = $user->activities()->latest()->take(5)->get();
            $roommates = $user->getRoommates();
            $availableTerms = AcademicTerm::whereIn('status', ['active', 'planned'])->get();
            $sibling = $user->getEligibleSibling();

            return view('student.home', compact(
                'user', 'roommates', 'reservation', 'activities', 'availableTerms', 'sibling'
            ));
        } catch (Throwable $e) {
            logError('Failed to load user profile page', 'show_user_profile_page', $e);
            return view('errors.500');
        }
    }
}
