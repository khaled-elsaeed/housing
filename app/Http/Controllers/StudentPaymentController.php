<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Contracts\UploadServiceContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentPaymentController extends Controller
{
    public function __construct(private UploadServiceContract $uploadService) {}

    /**
     * Handle invoice payment.
     */
    public function payInvoice(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method' => 'required|string|in:bank_transfer,instapay',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // Check ownership and invoice status
        if ($invoice->reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Invoice already paid'], 400);
        }

        DB::beginTransaction();

        try {
            // Upload receipt image
            $paymentImage = $this->uploadService->upload(
                $request->file('invoiceReceipt'),
                'payments'
            );

            // Update payment details
            $invoice->update([
                'receipt_image' => $paymentImage->id,
                'status' => 'paid',
                'payment_method' => $validated['payment_method'],
            ]);



            DB::commit();

            return response()->json(['message' => 'Invoice paid successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice payment failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to pay invoice'], 500);
        }
    }

    /**
     * Get invoice details.
     */
    public function getInvoiceDetails(Request $request)
    {
        $invoiceId = $request->input('invoice_id');

        $invoice = Invoice::with(['details', 'reservation.user'])
            ->find($invoiceId);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $user = $invoice->reservation->user;

        $paymentDetails = $invoice->details->map(fn($detail) => [
            'category' => $detail->category,
            'amount' => $detail->amount,
        ]);

        $totalAmount = $paymentDetails->sum('amount');

        $response = [
            'reservation' => [
                'id' => $invoice->reservation->id,
                'customerName' => $user->first_name . ' ' . $user->last_name,
                'location' => $invoice->reservation->room->getLocation(),
                'term' => $invoice->reservation->term,
            ],
            'paymentDetails' => $paymentDetails,
            'totalAmount' => $totalAmount,
        ];

        return response()->json($response);
    }
}
