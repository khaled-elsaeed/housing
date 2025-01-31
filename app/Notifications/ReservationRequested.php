<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use App\Models\ReservationRequest;
use App\Mail\ReservationRequested as ReservationRequestedMailable;

class ReservationRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public ReservationRequest $reservationRequest;

    public function __construct(ReservationRequest $reservationRequest)
    {
        $this->reservationRequest = $reservationRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): Mailable
    {
        return (new ReservationRequestedMailable($this->reservationRequest))
            ->to($notifiable->email);
    }


    public function toArray(object $notifiable): array
    {
        
        return [

            'title' => 'Reservation Request',
            'reservation_id' => $this->reservationRequest->id,
            'message' => 'Your reservation has been created successfully.',
            'status' => $this->reservationRequest->status,
            'created_at' => now(),

        ];
    }
}