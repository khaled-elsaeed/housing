<?php

namespace App\Http\Controllers\Student;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Student;
use App\Models\UserActivity;
use App\Contracts\UploadServiceContract;
use App\Events\InvoicePaid;  // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InvoiceDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentPaymentController extends Controller
{
    public function __construct(private UploadServiceContract $uploadService)
    {
    }

    public function payInvoice(Request $request)
    {
        $validated = $request->validate([
            "invoice_id" => "required|exists:invoices,id",
            "payment_method" => "required|string|in:bank_transfer,instapay",
            "invoice-receipt" => "required",
        ]);

        $invoice = Invoice::findOrFail($validated["invoice_id"]);

        if ($invoice->reservation->user_id !== Auth::id()) {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        if ($invoice->status === "paid") {
            return response()->json(["message" => "Invoice already paid"], 400);
        }

        DB::beginTransaction();

        try {

            $paymentImage = $this->storePaymentImage($request->file("invoice-receipt"));

            $invoice->update([
                "media_id" => $paymentImage->id,
                "status" => "paid",
                "payment_method" => $validated["payment_method"],
                "paid_at" => now(),
            ]);

            DB::commit();

            event(new InvoicePaid($invoice));
            UserActivity::create([
                'user_id' => auth()->id(),
                'activity_type' => 'Invoice Upload',
                'description' => 'Invoice uploaded successfully'
            ]);

            return response()->json(["message" => "Invoice paid successfully"], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Invoice payment failed", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
            return response()->json([
                "message" => "Failed to pay invoice",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function getInvoiceDetails(Request $request)
    {
        $validated = $request->validate([
            "invoice_id" => "required|exists:invoices,id",
        ]);

        $invoice = Invoice::with([
            "details",
            "reservation.user",
            "reservation.room",
        ])->findOrFail($validated["invoice_id"]);

        if ($invoice->reservation->user_id !== Auth::id()) {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        $user = $invoice->reservation->user;

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
    }

    private function storePaymentImage($file)
    {
        if (!$file) {
            throw new \Exception("Payment receipt file is required.");
        }
        return $this->uploadService->upload($file, "payments");
    }
}