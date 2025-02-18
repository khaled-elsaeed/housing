<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use App\Models\Room;
use App\Mail\ReservationRoomChanged as ReservationRoomChangedMailable;

class ReservationRoomChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public Room $room;

    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): Mailable
    {
        return (new ReservationRoomChangedMailable($this->room))
            ->to($notifiable->email);
    }


    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Room Reservation Changed', 
            'reservation_id' => $this->room->reservation->id, 
            'room_id' => $this->room->id, 
            'message' => 'Your room reservation has been updated. Please check your new room details.', 
            'status' => $this->room->reservation->status, 
            'created_at' => now(),
        ];
    }

}