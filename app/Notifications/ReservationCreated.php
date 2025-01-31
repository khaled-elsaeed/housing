<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use App\Models\Reservation;
use App\Mail\ReservationCreated as ReservationCreatedMailable;

class ReservationCreated extends Notification implements ShouldQueue
{
    use Queueable;


    public function __construct(public Reservation $reservation)
    {
        
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): Mailable
    {
        return (new ReservationCreatedMailable($this->reservation))
        ->to($notifiable->email);
    }

    public function toArray(object $notifiable): array
    {
        
        return [

            'title' => 'Reservation Request',
            'reservation_id' => $this->reservation->id,
            'message' => 'Your reservation has been created successfully.',
            'status' => $this->reservation->status,
            'created_at' => now(),

        ];
    }

}