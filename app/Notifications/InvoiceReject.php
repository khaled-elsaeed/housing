<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use App\Models\Invoice; // Ensure this is the correct namespace
use App\Mail\InvoiceReject as InvoiceRejectMailable;
use Illuminate\Support\Facades\Log;

class InvoiceReject extends Notification
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
        $mailable = new InvoiceRejectMailable($this->invoice);
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
            'title' => 'تم رفض الفاتورة',
            'invoice_id' => $this->invoice->id,
            'message' => 'تم رفض الفاتورة من قبل إدارة السكن، يرجى مراجعة التفاصيل وإعادة التقديم.',
            'status' => $this->invoice->status,
            'created_at' => now(),
        ];
    }
}