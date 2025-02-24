<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use App\Services\ReservationSwapService;
use Illuminate\Http\Request;
use App\Exceptions\BusinessRuleException;
use Exception;

class ReservationSwapController extends Controller
{
    protected $reservationSwapService;

    /**
     * Constructor for ReservationSwapController.
     *
     * @param ReservationSwapService $reservationSwapService
     */
    public function __construct(ReservationSwapService $reservationSwapService)
    {
        $this->reservationSwapService = $reservationSwapService;
    }

    /**
     * Display the relocation view.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            return view('admin.reservation.relocation');
        } catch (Exception $e) {
            logError('Failed to load relocation page', 'load_relocation_page', $e);
            return redirect()->back()->with('error', 'Failed to load relocation page.');
        }
    }

    /**
     * Show user and reservation details by national ID.
     *
     * @param string $nationalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $nationalId)
    {
        try {
            $user = $this->reservationSwapService->getUserByNationalId($nationalId);
            $reservation = $this->reservationSwapService->getActiveReservation($user);
            $reservation->load('room.apartment.building');
            $student = $user->student;

            return successResponse('User and reservation details retrieved successfully', null, [
                'reservation' => [
                    'id' => $reservation->id,
                    'room_number' => $reservation->room->number ?? null,
                    'apartment_number' => $reservation->room->apartment->number ?? null,
                    'building_number' => $reservation->room->apartment->building->number ?? null,
                ],
                'student' => [
                    'name_en' => $student->name ?? 'N/A',
                    'faculty' => $student->faculty->name ?? 'N/A',
                ],
            ]);
        } catch (BusinessRuleException $e) {
            return errorResponse(trans($e->getMessage()), 404);
        } catch (Exception $e) {
            logError('Failed to show user and reservation details', 'show_user_reservation_details', $e);
            return errorResponse('Failed to retrieve user and reservation details', 500);
        }
    }

    /**
     * Swap reservation locations for two reservations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function swapReservationLocation(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'reservation_id_1' => 'required|exists:reservations,id',
                'reservation_id_2' => 'required|exists:reservations,id',
            ]);

            $result = $this->reservationSwapService->swapReservationLocations(
                $validatedData['reservation_id_1'],
                $validatedData['reservation_id_2']
            );

            return successResponse('Reservation locations swapped successfully', null, [
                'reservation1' => $result['reservation1'],
                'reservation2' => $result['reservation2'],
            ]);
        } catch (BusinessRuleException $e) {
            return errorResponse(trans($e->getMessage()), 400);
        } catch (Exception $e) {
            logError('Failed to swap reservation locations', 'swap_reservation_locations', $e);
            return errorResponse('Failed to swap reservation locations. Please try again.', 500);
        }
    }

    /**
     * Reallocate a reservation to a new room.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reallocateReservation(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'reservation_id' => 'required|exists:reservations,id',
                'room_id' => 'required|exists:rooms,id',
            ]);

            $result = $this->reservationSwapService->reallocateReservation(
                $validatedData['reservation_id'],
                $validatedData['room_id']
            );

            return successResponse('Reservation reallocated successfully', null, [
                'reservation' => $result['reservation'],
                'new_room_details' => $result['new_room_details'],
            ]);
        } catch (BusinessRuleException $e) {
            return errorResponse(trans($e->getMessage()), 400);
        } catch (Exception $e) {
            logError('Failed to reallocate reservation', 'reallocate_reservation', $e);
            return errorResponse('Failed to reallocate reservation. Please try again.', 500);
        }
    }
}