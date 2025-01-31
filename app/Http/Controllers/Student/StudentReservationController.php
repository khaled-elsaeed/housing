<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ReservationService;
use App\Models\AcademicTerm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\UserActivity;

class StudentReservationController extends Controller
{
    protected $reservationService;

    /**
     * Constructor to inject ReservationService.
     *
     * @param ReservationService $reservationService
     */
    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
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
            // Validate the request data
            $validator = $this->validateReservationRequest($request);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('Validation failed'),
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Prepare reservation data
            $reservationData = $this->prepareReservationData($request);

            // Handle long-term and short-term reservations separately
            if ($reservationData['reservation_period_type'] === 'long') {
                $result = $this->handleLongTermReservation($reservationData);
            } elseif ($reservationData['reservation_period_type'] === 'short') {
                $result = $this->handleShortTermReservation($reservationData);
            }

            // Return the appropriate response
            return $this->handleReservationResult($result);
        } catch (\Exception $e) {
            Log::error('Reservation creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => trans('Failed to create reservation'),
            ], 500);
        }
    }

  /**
 * Handle a long-term reservation request.
 *
 * @param array $reservationData
 * @return array
 */
private function handleLongTermReservation(array $reservationData)
{
    return $this->reservationService->requestReservation(
        reservationRequester: auth()->user(), 
        reservationPeriodType: $reservationData['reservation_period_type'], 
        academicTermId: $reservationData['reservation_academic_term_id']
    );
}

/**
 * Handle a short-term reservation request.
 *
 * @param array $reservationData
 * @return array
 */
private function handleShortTermReservation(array $reservationData)
{
    // Log the reservation data for debugging

    return $this->reservationService->requestReservation(
        reservationRequester: auth()->user(), // Correct parameter name
        reservationPeriodType: $reservationData['reservation_period_type'], // Correct parameter name
        shortTermDuration: $reservationData['short_period_duration'], // Correct parameter name
        startDate: $reservationData['start_date'], // Correct parameter name
        endDate: $reservationData['end_date'] // Correct parameter name
    );
}

    /**
     * Validate the reservation request.
     *
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    private function validateReservationRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'reservation_period_type' => 'required|in:long,short',
            'reservation_academic_term_id' => 'required_if:reservation_period_type,long|exists:academic_terms,id',
            'short_period_duration' => 'required_if:reservation_period_type,short|in:day,week,month',
            'start_date' => 'nullable|required_if:reservation_period_type,short|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);
    }

    /**
     * Prepare reservation data from the request.
     *
     * @param Request $request
     * @return array
     */
    private function prepareReservationData(Request $request)
    {
        return [
            'reservation_period_type' => $request->reservation_period_type,
            'reservation_academic_term_id' => $request->reservation_academic_term_id,
            'short_period_duration' => $request->short_period_duration,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];
    }

    /**
     * Handle the result of the reservation request.
     *
     * @param array $result
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleReservationResult(array $result)
    {
        if ($result['success']) {
            UserActivity::create([
                'user_id' => auth()->id(),
                'activity_type' => 'Reservation Created',
                'description' => 'Reservation created successfully'
            ]);
            return response()->json([
                'success' => true,
                'message' => trans('Reservation created successfully!'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['reason'],
        ], 400);
    }
}