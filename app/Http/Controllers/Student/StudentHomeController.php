<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\UserActivity;
use App\Models\AcademicTerm;
use App\Services\ReservationService;

class StudentHomeController extends Controller
{
    /**
     * Show the student's home page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $user = auth()->user();

            $reservation = $user->reservations()
                ->where('status', 'active')
                ->latest()
                ->first();

            $activities = $user->activities()
                ->recent(10) // Ensure this is a custom scope in your UserActivity model
                ->get();

            $availableTerms = AcademicTerm::whereIn('status', ['active', 'planned'])
                ->get();

            return view('student.home', compact('user', 'reservation', 'activities', 'availableTerms'));
        } catch (\Exception $e) {
            Log::error('Error loading student home: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('home')->with('error', 'Something went wrong while loading the page.');
        }
    }

    /**
     * Handle a student's request to create a new reservation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestReservation(Request $request)
    {
        try {
            $user = auth()->user();

            // Validate the request data
            $request->validate([
                'reservationTermId' => 'required|string',
                'reservationPeriodType' => 'required|in:short_term,long_term',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            // Instantiate the ReservationService directly
            $reservationService = new ReservationService();

            // Call the ReservationService to handle the reservation request
            $result = $reservationService->requestReservation(
                $user,
                $request->input('reservation_period'),
                $request->input('period_type'),
                $request->input('start_date'),
                $request->input('end_date')
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reservation requested successfully!',
                    'reservation' => $result['reservation'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['reason'],
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Reservation request failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to process reservation request: ' . $e->getMessage(),
            ], 500);
        }
    }
}