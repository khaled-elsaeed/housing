<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use App\Services\ReservationSwapService;
use Illuminate\Http\Request;
use Exception;
use Log;
use App\Exceptions\BusinessRuleException;


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
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            return view('admin.reservation.relocation');
        } catch (Exception $e) {
            Log::error('Failed to load relocation page', [
                'error' => $e->getMessage(),
                'action' => 'load_relocation_page',
                'admin_id' => auth()->id(),
            ]);
            return redirect()->back()->with('error', 'Failed to load relocation page.');
        }
    }

    /**
     * Show user and reservation details by national ID.
     *
     * @param string $nationalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($nationalId)
    {
        try {
            // Get the user by national ID
            $user = $this->reservationSwapService->getUserByNationalId($nationalId);
    
            // Get the active reservation for the user
            $reservation = $this->reservationSwapService->getActiveReservation($user);
    
            // Load the necessary relationships (room, apartment, building)
            $reservation->load('room.apartment.building');
    
            // Get the student details
            $student = $user->student;
    
            return response()->json([
                'success' => true,
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
        } catch (Exception $e) {

            // Log the error
            Log::error('Failed to show user and reservation details', [
                'error' => $e->getMessage(),
                'action' => 'show_user_reservation_details',
                'admin_id' => auth()->id(),
            ]);
    
            // Return an error response
            return response()->json(['error' => $e->getMessage()], 404);
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

            return response()->json([
                'success' => true,
                'message' => 'Reservation locations swapped successfully',
                'reservation1' => $result['reservation1'],
                'reservation2' => $result['reservation2'],
            ]);
        } catch (BusinessRuleException $e) {
            
            return response()->json(['error' => trans($e->getMessage())], 400);
        
        } catch (Exception $e) {
            // Handle all other exceptions
            Log::error('Failed to swap reservation locations', [
                'error' => $e->getMessage(),
                'action' => 'swap_reservation_locations',
                'admin_id' => auth()->id(),
            ]);
        
            return response()->json(['error' => 'Failed to swap reservation locations. Please try again.'], 400);
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

            $reservation = $this->reservationSwapService->reallocateReservation(
                $validatedData['reservation_id'],
                $validatedData['room_id']
            );

            // Return the success response with reservation and new room details
            return response()->json([
                'success' => true,
                'message' => 'Reservation reallocated successfully',
                'reservation' => $reservation['reservation'],
                'new_room_details' => $reservation['new_room_details'],
            ]);
        } 
        catch (BusinessRuleException $e) {
            
            return response()->json(['error' => trans($e->getMessage())], 400);
        
        } catch (Exception $e) {
            // Handle all other exceptions
            Log::error('Failed to reallocate reservation', [
                'error' => $e->getMessage(),
                'action' => 'reallocate_reservation',
                'admin_id' => auth()->id(),
            ]);
        
            return response()->json(['error' => 'Failed to swap reservation locations. Please try again.'], 400);
        }
    }
}