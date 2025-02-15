<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use App\Models\Reservation;

use App\Models\AcademicTerm;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Exceptions\BusinessRuleException;
use App\Models\User;

class StudentHomeController extends Controller
{
    /**
     * Display student dashboard with their reservation status, activities and available terms
     *
     * @return \Illuminate\View\View
     */
    public function index()
{
    try {
        $user = auth()->user();
        
        $reservation = null;

        // Fetch latest active reservation
        $reservation = $user->reservations()
            ->where('status', 'active')
            ->latest()
            ->first();

        // Fetch activities
        $activities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->get();

        // Get roommates if user has an active reservation
$roommates = collect(); // Default empty collection

if ($reservation && optional($reservation->room)->apartment) {
    $apartment = $reservation->room->apartment;
    $roomIds = $apartment->rooms->pluck('id');

    $roommates = Reservation::whereIn('room_id', $roomIds)
        ->where('status', 'active')
        ->where('user_id', '!=', $user->id)
        ->with(['user', 'room'])
        ->get();
}


        // Fetch available academic terms
        $availableTerms = AcademicTerm::whereIn('status', ['active', 'planned'])->get();

        // Get sibling information
        $sibling = $this->getEligibleSibling($user);

        return view('student.home', compact('user', 'roommates', 'reservation', 'activities', 'availableTerms', 'sibling'));

    } catch (Throwable $e) {
        Log::error('Failed to load user profile page', [
            'error' => $e->getMessage(),
            'action' => 'show_user_profile_page',
            'user_id' => auth()->id(),
        ]);

        return view('errors.500');
    }
}


    /**
     * Determine if user has an eligible sibling for room sharing based on gender
     *
     * @param User $user
     * @return User|null The eligible sibling or null if none found
     */
    private function getEligibleSibling(User $user)
    {
        if (!$user->sibling || !$user->sibling->gender) {
            return null;
        }

        $siblingGender = $user->sibling->gender;

        $userGender = $user->gender;

        if ($siblingGender === 'brother' && $userGender === 'male') {
            return $user->sibling;
        }

        if ($siblingGender === 'sister' && $userGender === 'female') {
            return $user->sibling;
        }

        return null;
    }
}
