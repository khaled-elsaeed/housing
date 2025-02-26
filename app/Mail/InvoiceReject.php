<?php

namespace App\Mail;

use App\Models\Invoice; // Ensure this is the correct namespace
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InvoiceReject extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        
        $this->invoice = $invoice;

        // Ensure the reservation and user exist
        if (!$invoice->reservation) {
            Log::error('InvoiceReject Mail: Reservation not found for invoice', [
                'invoice_id' => $invoice->id,
            ]);
            throw new \Exception('Reservation not found for the invoice.');
        }

        if (!$invoice->reservation->user) {
            Log::error('InvoiceReject Mail: User not found for reservation', [
                'invoice_id' => $invoice->id,
                'reservation_id' => $invoice->reservation->id,
            ]);
            throw new \Exception('User not found for the reservation.');
        }

        $this->user = $invoice->reservation->user;

        // Log the user associated with the invoice
        Log::info('InvoiceReject Mail: User associated with invoice', [
            'invoice_id' => $invoice->id,
            'user_id' => $this->user->id,
            'user_email' => $this->user->email,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم رفض الدفع', 
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice_reject',
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