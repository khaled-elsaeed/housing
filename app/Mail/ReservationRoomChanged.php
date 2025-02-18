<?php

namespace App\Mail;

use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationRoomChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $room;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param Room $room
     */
    public function __construct(Room $room)
    {
        $this->room = $room;
        $this->user = $room->reservation->user; 
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تغيير في حجز الغرفة', // Arabic: Room Reservation Changed
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_room_changed', // Use a more appropriate email template
            with: [
                'room' => $this->room,
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