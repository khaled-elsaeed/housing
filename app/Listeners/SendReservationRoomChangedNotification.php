<?php

namespace App\Listeners;

use App\Events\ReservationRoomChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\ReservationRoomChanged as ReservationRoomChangedNotification;
use Illuminate\Support\Facades\Log;

class SendReservationRoomChangedNotification
{
    use InteractsWithQueue;


    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'reservation_room_change_notification'; 

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
    public function handle(ReservationRoomChanged $event): void
    {
        try {
            $room = $event->room;

            // Ensure the room, reservation, and user exist
            if ($room && $room->reservation && $room->reservation->user) {
                $room->reservation->user->notify(new ReservationRoomChangedNotification($room));
            } else {
                Log::warning('Failed to send reservation room change notification: Missing room, reservation, or user.', [
                    'room_id' => $room->id ?? null,
                    'reservation_id' => $room->reservation->id ?? null,
                    'user_id' => $room->reservation->user->id ?? null,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send reservation room change notification', [
                'error' => $e->getMessage(),
                'room_id' => $room->id ?? null,
                'action' =>'room_change_listener', 
            ]);
        }
    }
}