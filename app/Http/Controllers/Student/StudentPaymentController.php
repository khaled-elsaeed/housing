<?php

namespace App\Http\Controllers\Student;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\UserActivity;
use App\Contracts\UploadServiceContract;
use App\Events\InvoicePaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentPaymentController extends Controller
{
    public function __construct(private UploadServiceContract $uploadService)
    {
    }

    /**
     * Pay an invoice.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payInvoice(Request $request)
    {
        $validated = $request->validate([
            "invoice_id" => "required|exists:invoices,id",
            "payment_method" => "required|string|in:bank_transfer,instapay",
            "photos" => 'nullable|array',
            "photos.*" => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB max per file
        ]);

        $invoice = Invoice::findOrFail($validated["invoice_id"]);

        // Check if the invoice belongs to the authenticated user
        if ($invoice->reservation->user_id !== Auth::id()) {
            Log::warning('Unauthorized attempt to pay invoice', [
                'invoice_id' => $validated["invoice_id"],
                'user_id' => Auth::id(),
                'action' => 'pay_invoice',
            ]);
            return response()->json(["message" => "Unauthorized"], 403);
        }

        // Check if the invoice is already paid
        if ($invoice->status === "paid") {
            Log::warning('Attempt to pay an already paid invoice', [
                'invoice_id' => $validated["invoice_id"],
                'user_id' => Auth::id(),
                'action' => 'pay_invoice',
            ]);
            return response()->json(["message" => "Invoice already paid"], 400);
        }

        DB::beginTransaction();

        try {
            
            // Update the invoice status and payment details
            $invoice->update([
                "status" => "paid",
                "payment_method" => $validated["payment_method"],
                "paid_at" => now(),
            ]);

           // Handle file uploads using UploadService
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Use UploadService to handle the file upload
                $this->uploadService->upload($photo, 'payments', $invoice);
            }
        }

            DB::commit();

            // Trigger the InvoicePaid event
            event(new InvoicePaid($invoice));

            // Log user activity
            UserActivity::create([
                'user_id' => auth()->id(),
                'activity_type' => 'invoice_upload',
                'description' => 'Invoice uploaded successfully',
            ]);

            // Log successful payment

            return response()->json(["message" => "Invoice paid successfully"], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Failed to pay invoice', [
                'error' => $e->getMessage(),
                'invoice_id' => $validated["invoice_id"],
                'user_id' => Auth::id(),
                'action' => 'pay_invoice',
            ]);

            return response()->json([
                "message" => "Failed to pay invoice",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get invoice details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoiceDetails(Request $request)
    {
        $validated = $request->validate([
            "invoice_id" => "required|exists:invoices,id",
        ]);

        try {
            $invoice = Invoice::with([
                "details",
                "reservation.user",
                "reservation.room",
            ])->findOrFail($validated["invoice_id"]);

            // Check if the invoice belongs to the authenticated user
            if ($invoice->reservation->user_id !== Auth::id()) {
                Log::warning('Unauthorized attempt to access invoice details', [
                    'invoice_id' => $validated["invoice_id"],
                    'user_id' => Auth::id(),
                    'action' => 'get_invoice_details',
                ]);
                return response()->json(["message" => "Unauthorized"], 403);
            }

            $user = $invoice->reservation->user;

            // Format payment details
            $paymentDetails = $invoice->details->map(
                fn($detail) => [
                    "category" => $detail->category,
                    "amount" => $detail->amount,
                    "description" => $detail->description,
                ]
            );

            $totalAmount = $paymentDetails->sum("amount");


            return response()->json([
                "invoice_id" => $invoice->id,
                "reservation" => [
                    "id" => $invoice->reservation->id,
                    "customer_name" => $user->name,
                    "room_number" => $invoice->reservation->room->number,
                    "building" => $invoice->reservation->room->building->name,
                    "term" => $invoice->reservation->term,
                    "year" => $invoice->reservation->year,
                ],
                "payment_details" => $paymentDetails,
                "total_amount" => $totalAmount,
                "status" => $invoice->status,
                "payment_method" => $invoice->payment_method,
                "due_date" => $invoice->due_date,
                "paid_at" => $invoice->paid_at,
                "created_at" => $invoice->created_at,
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to retrieve invoice details', [
                'error' => $e->getMessage(),
                'invoice_id' => $validated["invoice_id"],
                'user_id' => Auth::id(),
                'action' => 'get_invoice_details',
            ]);

            return response()->json([
                "message" => "Failed to retrieve invoice details",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    
}