<?php

namespace App\Listeners;

use App\Events\ReservationRequested;
use App\Models\Reservation;
use App\Models\Room; // Ensure Room model is imported
use App\Services\ReservationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AutoAcceptRequest implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The reservation service instance.
     *
     * @var ReservationService
     */
    protected $reservationService;

    /**
     * Create the event listener.
     *
     * @param ReservationService $reservationService
     */
    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Handle the event.
     *
     * @param ReservationRequested $event
     * @return void
     */
    public function handle(ReservationRequested $event): void
    {
        
        // Get the reservation request from the event
        $reservationRequest = $event->getReservationRequest();

        // Ensure the reservation request and user exist
        if (!$reservationRequest || !$reservationRequest->user) {
            Log::warning('Reservation request or user not found in AutoAcceptRequest listener.');
            return;
        }

        // Check if the request has the flag `stay_in_last_old_room` set to 1 and `old_room_id` is valid
        if ($reservationRequest->stay_in_last_old_room != 1 || !$reservationRequest->old_room_id) {
           
            return;
        }

        // Fetch the old room
        $oldRoom = Room::find($reservationRequest->old_room_id);

        // Ensure the old room exists
        if (!$oldRoom) {
            Log::warning('Old room not found for reservation request.', [
                'reservation_request_id' => $reservationRequest->id,
                'old_room_id' => $reservationRequest->old_room_id,
            ]);
            return;
        }

        $user = $reservationRequest->user;
        $academicYear = $reservationRequest->academicTerm->academic_year;


        $lastReservation = Reservation::join('academic_terms', 'reservations.academic_term_id', '=', 'academic_terms.id')
            ->where('reservations.user_id', $user->id)
            ->where('reservations.room_id', $oldRoom->id)
            ->where('reservations.status', 'completed')
            ->where('academic_terms.academic_year', $academicYear)
            ->orderBy('reservations.created_at', 'desc')
            ->first();
        // If the user has a completed reservation in the same academic year and old room, auto-accept the request
        if ($lastReservation) {
            try {
                // Automatically approve the reservation request
                $reservationRequest->status = 'accepted';
                $reservationRequest->save();

                // Create a new reservation using the reservation service
                $this->reservationService->newReservation($reservationRequest, $oldRoom);

            } catch (\Exception $e) {
                Log::error('Error auto-accepting reservation request: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'reservation_request_id' => $reservationRequest->id,
                    'old_room_id' => $oldRoom->id,
                ]);
            }
        } 
    }
}