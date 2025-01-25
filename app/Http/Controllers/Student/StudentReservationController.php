<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ReservationService;
use App\Models\AcademicTerm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Prepare reservation data
            $reservationData = [
                'reservation_period_type' => $request->reservation_period_type,
                'reservation_academic_term_id' => $request->reservation_academic_term_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ];;

            // Handle long-term and short-term reservations separately
            if ($reservationData['reservation_period_type'] === 'long_term') {
                $result = $this->handleLongTermReservation($reservationData);
            } else {
                $result = $this->handleShortTermReservation($reservationData);
            }

            // Return the appropriate response
            return $this->handleReservationResult($result);
        } catch (\Exception $e) {
            Log::error('Reservation creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create reservation',
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
            auth()->user(),
            $reservationData['reservation_period_type'],
            $reservationData['reservation_academic_term_id'],
            
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
        return $this->reservationService->requestReservation(
            auth()->user(),
            $reservationData['reservation_period_type'],
            $reservationData['start_date'],
            $reservationData['end_date']
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
            'reservation_period_type' => 'required|in:long_term,short_term',
            'reservation_academic_term_id' => 'required_if:reservation_period_type,long_term|exists:academic_terms,id',
            'short_term_duration_type' => 'required_if:reservation_period_type,short_term|in:day,week,month',
            'start_date' => 'nullable|required_if:reservation_period_type,short_term|date',
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
            'short_term_duration_type' => $request->short_term_duration_type,
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
            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully!',
                'reservation' => $result['reservation'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['reason'],
        ], 400);
    }

  

}