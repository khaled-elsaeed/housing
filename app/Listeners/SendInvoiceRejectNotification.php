<?php

namespace App\Listeners;

use App\Events\InvoiceReject;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\InvoiceReject as InvoiceRejectNotification;
use Illuminate\Support\Facades\Log;

class SendInvoiceRejectNotification implements ShouldQueue
{
    use InteractsWithQueue;


    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'invoice_notification'; // Example: 'invoices', 'notifications', etc.


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
    public function handle(InvoiceReject $event): void
    {
        try {
            $invoice = $event->invoice;
            
            if (!$invoice) {
                throw new \Exception('Invoice not found');
            }

            if (!$invoice->reservation?->user) {
                throw new \Exception('User not found for this invoice');
            }

            $invoice->reservation->user->notify(new InvoiceRejectNotification($invoice));
            
        } catch (\Exception $e) {
            Log::error('Failed to send invoice reject notification: ' . $e->getMessage(), [
                'invoice_id' => $event->invoice->id ?? null
            ]);
            
            $this->fail($e);
        }
    }
}
