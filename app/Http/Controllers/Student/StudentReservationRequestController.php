<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ReservationRequestService;
use App\Http\Requests\ReservationRequest as ReservationRequestValidation;
use App\Events\ReservationRequested;
use App\Exceptions\BusinessRuleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

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
            $student = Auth::user();

            // Normalize checkbox values
            $validatedData['stay_in_last_old_room'] = $request->input('stay_in_last_old_room') !== null
                ? $this->normalizeCheckboxValue($request->input('stay_in_last_old_room'))
                : null;

            $validatedData['share_with_sibling'] = $request->input('share_with_sibling') !== null
                ? $this->normalizeCheckboxValue($request->input('share_with_sibling'))
                : null;

            // Create reservation request via service
            $reservationRequest = $this->reservationRequestService->createReservationRequest($student, $validatedData);

            return successResponse(trans('Reservation request submitted successfully!'));
        } catch (BusinessRuleException $e) {
            logError('Business rule violation in reservation request', 'create_reservation_request', $e);
            return errorResponse($e->getMessage(), 422); 
        } catch (Exception $e) {
            logError('Unexpected error in reservation request', 'create_reservation_request', $e);
            return errorResponse(trans('An unexpected error occurred. Please try again later.'), 500); 
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