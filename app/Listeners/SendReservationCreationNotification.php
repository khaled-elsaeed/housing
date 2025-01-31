<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\ReservationCreated as ReservationCreatedNotification; // Renaming notification
use Illuminate\Support\Facades\Log;

class SendReservationCreationNotification implements ShouldQueue
{
    use InteractsWithQueue;


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
    public function handle(ReservationCreated $event):void
    {
        try {
            $reservation = $event->getReservation();
            if ($reservation && $reservation->user) {
                $reservation->user->notify(new ReservationCreatedNotification($reservation));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send reservation notification', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id ?? null
            ]);
        }
    }
}
