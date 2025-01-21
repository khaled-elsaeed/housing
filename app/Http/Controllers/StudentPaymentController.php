<?php

// app/Http/Controllers/StudentPaymentController.php
namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Student;
use App\Contracts\UploadServiceContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InvoiceDetail;

class StudentPaymentController extends Controller
{
    public function __construct(private UploadServiceContract $uploadService) {}

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
                $request->file('invoice-receipt'),
                'payments'
            );

            Log::info('Payment image uploaded', [
                'image_data' => $paymentImage->toArray(),
                'id' => $paymentImage->id
            ]);

            // Update payment details
            $invoice->update([
                'media_id' => $paymentImage->id,
                'status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'paid_at' => now()
            ]);

            DB::commit();

            return response()->json(['message' => 'Invoice paid successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice payment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed to pay invoice'], 500);
        }
    }

    public function addInvoice(Request $request)
{
    $validated = $request->validate([
        'term' => 'required|in:first_term,second_term',
        'payment_method' => 'required|string|in:bank_transfer,instapay',
    ]);

    $user = auth()->user();

    // Get the user's last room reservation
    $lastRoom = $user->reservation()->with('room')->latest()->first()?->room;

    if (!$lastRoom) {
        return response()->json(['message' => 'No previous room reservation found'], 400);
    }

    DB::beginTransaction();

    try {
        // Create new reservation
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'room_id' => $lastRoom->id,
            'year' => '2024-2025',
            'term' => $validated['term'],
            'status' => 'upcoming',
        ]);

        // Upload receipt image
        $paymentImage = $this->uploadService->upload(
            $request->file('invoice-receipt'),
            'payments'
        );

        Log::info('Payment image uploaded', [
            'image_data' => $paymentImage->toArray(),
            'id' => $paymentImage->id,
        ]);

        // Create new invoice
        $invoice = Invoice::create([
            'reservation_id' => $reservation->id,
            'media_id' => $paymentImage->id,
            'status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'term' => $validated['term'],
        ]);

        // Add invoice details
        InvoiceDetail::create([
            'invoice_id' => $invoice->id,
            'category' => 'fee',
            'amount' => 10000,
        ]);

        InvoiceDetail::create([
            'invoice_id' => $invoice->id,
            'category' => 'insurance',
            'amount' => 5000,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Invoice created and paid successfully',
            'invoice_id' => $invoice->id,
            'reservation_id' => $reservation->id,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Invoice creation failed', [
            'error' => $e->getMessage(),
            'user_id' => $user->id,
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'message' => 'Failed to create and pay invoice',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}


    public function getInvoiceDetails(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id'
        ]);

        $invoice = Invoice::with(['details', 'reservation.user', 'reservation.room'])
            ->findOrFail($validated['invoice_id']);

        // Check ownership
        if ($invoice->reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = $invoice->reservation->user;

        $paymentDetails = $invoice->details->map(fn($detail) => [
            'category' => $detail->category,
            'amount' => $detail->amount,
            'description' => $detail->description
        ]);

        $totalAmount = $paymentDetails->sum('amount');

        $response = [
            'invoice_id' => $invoice->id,
            'reservation' => [
                'id' => $invoice->reservation->id,
                'customer_name' => $user->name,
                'room_number' => $invoice->reservation->room->number,
                'building' => $invoice->reservation->room->building->name,
                'term' => $invoice->reservation->term,
                'year' => $invoice->reservation->year
            ],
            'payment_details' => $paymentDetails,
            'total_amount' => $totalAmount,
            'status' => $invoice->status,
            'payment_method' => $invoice->payment_method,
            'due_date' => $invoice->due_date,
            'paid_at' => $invoice->paid_at,
            'created_at' => $invoice->created_at
        ];

        return response()->json($response);
    }
}