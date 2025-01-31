<?php

namespace App\Mail;

use App\Models\ReservationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationRequested extends Mailable
{
    use Queueable, SerializesModels;

    public $reservationRequest;
    public $user;

    public function __construct(ReservationRequest $reservationRequest)
    {
        $this->reservationRequest = $reservationRequest;
        $this->user = $reservationRequest->user; 
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'طلب حجز جديد', // Arabic: New Reservation Request
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_requested', // Use an Arabic-specific email template
            with: [
                'reservationRequest' => $this->reservationRequest,
                'user' => $this->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}