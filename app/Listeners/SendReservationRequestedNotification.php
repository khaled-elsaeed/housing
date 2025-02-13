<?php

namespace App\Listeners;

use App\Events\ReservationRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\ReservationRequested as ReservationRequestedNotification; // Renaming notification
use Illuminate\Support\Facades\Log;

class SendReservationRequestedNotification
{
    use InteractsWithQueue;


    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'reservation_request_notification'; 

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReservationRequested $event): void
    {
        try {
            $reservationRequest = $event->getReservationRequest();
            if ($reservationRequest && $reservationRequest->user) {
                $reservationRequest->user->notify(new ReservationRequestedNotification($reservationRequest));
            }

        } catch (\Exception $e) {
            Log::error('Failed to send reservation request notification', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservationRequest->id ?? null
            ]);
        }
    }
}
