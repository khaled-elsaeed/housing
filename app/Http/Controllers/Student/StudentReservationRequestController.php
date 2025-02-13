<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ReservationRequestService;
use App\Models\{ReservationRequest, User};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StudentReservationRequestController extends Controller
{
    protected $reservationRequestService;

    public function __construct(ReservationRequestService $reservationRequestService)
    {
        $this->reservationRequestService = $reservationRequestService;
    }

    /**
     * Store a newly created reservation request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
{
    try {
        // Validate the request data using Laravel's built-in validation
        $validatedData = $request->validate([
            'reservation_period_type' => 'required|in:long,short',
            'reservation_academic_term_id' => 'required_if:reservation_period_type,long|exists:academic_terms,id',
            'stay_in_last_old_room' => 'nullable|in:on',
            'share_with_sibling' => 'nullable|in:on',
            'old_room_id' => 'nullable|exists:rooms,id',
            'sibling_id' => 'required_if:share_with_sibling,true|exists:users,id',
            'short_period_duration' => 'required_if:reservation_period_type,short|in:day,week,month',
            'start_date' => 'nullable|required_if:reservation_period_type,short|date',
            'end_date' => 'nullable|required_if:reservation_period_type,short|date|after:start_date',
        ]);

        // Convert boolean values (for checkbox fields)
        $validatedData['stay_in_last_old_room'] = $request->input('stay_in_last_old_room') !== null 
        ? $this->normalizeCheckboxValue($request->input('stay_in_last_old_room')) 
        : null;
    
    $validatedData['share_with_sibling'] = $request->input('share_with_sibling') !== null 
        ? $this->normalizeCheckboxValue($request->input('share_with_sibling')) 
        : null;
    
        // Get the authenticated student
        $student = auth()->user();

        // Create reservation request
        $newReservationRequest = $this->reservationRequestService->createReservationRequest(
            $student, 
            $validatedData
        );

        return response()->json([
            'success' => true,
            'message' => 'Reservation request submitted successfully!',
        ], 201);

    } catch (\Exception $e) {
        $this->logError($e);

        // Return a generic error message
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while submitting your reservation request. Please try again.'
        ], 500);
    }
}

    /**
 * Normalize checkbox value to boolean.
 *
 * @param mixed $value
 * @return bool
 */
private function normalizeCheckboxValue($value): bool
{
    return in_array(strtolower((string) $value), ['yes', '1', 'on'], true);
}


    /**
     * Log successful reservation request.
     *
     * @param User $student
     * @param ReservationRequest $reservationRequest
     * @return void
     */
    private function logSuccess(User $student, ReservationRequest $reservationRequest): void
    {
        Log::info('Reservation request created successfully', [
            'student_id' => $student->id,
            'reservation_request_id' => $reservationRequest->id,
            'reservation_type' => $reservationRequest->period_type
        ]);
    }

    /**
     * Log error during reservation request.
     *
     * @param \Exception $exception
     * @return void
     */
    private function logError(\Exception $exception): void
    {
        Log::error('Error creating reservation request', [
            'student_id' => auth()->id(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}