<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use App\Models\AcademicTerm;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Log;
use Throwable;
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

            Log::channel('access')->info('Resident accessed home page', [
                'user_id' => $user->id,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

            $reservation = $user->reservations()
                ->where('status', 'active')
                ->latest()
                ->first();

            $activities = $user->activities()
                ->orderBy('created_at', 'desc')
                ->get();

            $availableTerms = AcademicTerm::whereIn('status', ['active', 'planned'])
                ->get();

            $sibling = $this->getEligibleSibling($user);

            return view('student.home', compact('user', 'reservation', 'activities', 'availableTerms', 'sibling'));
        } catch (Throwable $e) {
            Log::error('Resident home page load failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'location' => $e->getFile() . ':' . $e->getLine()
            ]);

            return view('errors.500');
        }
    }

    /**
     * Process a new room reservation request
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestReservation(Request $request)
    {
        try {
            $user = auth()->user();

            Log::channel('access')->info('New reservation requested', [
                'user_id' => $user->id,
                'term_id' => $request->input('reservationTermId'),
                'period_type' => $request->input('reservationPeriodType'),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

            $request->validate([
                'reservationTermId' => 'required|string',
                'reservationPeriodType' => 'required|in:short,long',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            $reservationService = new ReservationService();

            $result = $reservationService->requestReservation(
                $user,
                $request->input('reservationTermId'),
                $request->input('reservationPeriodType'),
                $request->input('start_date'),
                $request->input('end_date')
            );

            if ($result['success']) {
                Log::channel('access')->info('Reservation created successfully', [
                    'user_id' => $user->id,
                    'reservation_id' => $result['reservation']->id ?? null,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Reservation requested successfully!',
                    'reservation' => $result['reservation'],
                ]);
            }

            Log::channel('access')->info('Reservation request failed', [
                'user_id' => $user->id,
                'reason' => $result['reason'],
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['reason'],
            ], 400);

        } catch (Throwable $e) {
            Log::error('Reservation request failed with exception', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'location' => $e->getFile() . ':' . $e->getLine(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process reservation request: ' . $e->getMessage(),
            ], 500);
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