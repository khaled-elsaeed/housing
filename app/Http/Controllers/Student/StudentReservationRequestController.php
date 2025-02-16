<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ReservationRequestService;
use App\Models\{ReservationRequest, User};
use Illuminate\Support\Facades\Log;
use App\Models\UserActivity;

use App\Events\ReservationRequested;
use App\Exceptions\BusinessRuleException;
use App\Http\Requests\ReservationRequest as ReservationRequestValidation; // Import the custom Request

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
     * @param ReservationRequestValidation $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ReservationRequestValidation $request)
    {
        try {
            
            $validatedData = $request->validated();

            // Get authenticated student
            $student = auth()->user();

            // Convert checkbox values (if necessary)
            $validatedData['stay_in_last_old_room'] = $request->input('stay_in_last_old_room') !== null
                ? $this->normalizeCheckboxValue($request->input('stay_in_last_old_room'))
                : null;

            $validatedData['share_with_sibling'] = $request->input('share_with_sibling') !== null
                ? $this->normalizeCheckboxValue($request->input('share_with_sibling'))
                : null;

            // Create reservation request
            $reservationRequest = $this->reservationRequestService->createReservationRequest($student, $validatedData);
            event(new ReservationRequested($reservationRequest));

            UserActivity::create([
                'user_id' => auth()->id(),
                'activity_type' => 'reservation_request',
                'description' => 'Reservation request submitted, awaiting approval',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => trans('Reservation request submitted successfully!'),
            ], 201);
        } catch (BusinessRuleException $e) {
            // Handle business rule violations
            Log::error('Business rule violation in reservation request', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'action' => 'create_reservation_request',
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422); // 422 Unprocessable Entity for validation failures
        } catch (\Exception $e) {
            // Log and handle unexpected system errors
            Log::error('Unexpected error in reservation request', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'action' => 'create_reservation_request',
            ]);

            return response()->json([
                'success' => false,
                'message' => trans('An unexpected error occurred. Please try again later.'),
            ], 500); // 500 Internal Server Error
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
}
