<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use App\Models\Invoice; // Ensure this is the correct namespace
use App\Mail\InvoicePaid as InvoicePaidMailable;
use Illuminate\Support\Facades\Log;

class InvoicePaid extends Notification
{
    use Queueable;

    public $invoice;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {   
        $this->invoice = $invoice;
        // Log the invoice being passed to the notification
        Log::info('InvoicePaid Notification: Invoice object received', [
            'invoice_id' => $this->invoice->id,
            'invoice_type' => get_class($this->invoice), // Log the class name of the invoice
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        // Log before creating the InvoicePaidMailable
        Log::info('InvoicePaid Notification: Preparing to send email', [
            'invoice_id' => $this->invoice->id,
            'notifiable_email' => $notifiable->email,
        ]);

        $mailable = new InvoicePaidMailable($this->invoice);
        return $mailable->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم رفع الفاتورة',
            'invoice_id' => $this->invoice->id,
            'message' => 'تم رفع الفاتورة بنجاح وهي قيد المراجعة من قبل إدارة السكن.',
            'status' => $this->invoice->status,
            'created_at' => now(),
        ];
    }
}