<?php

namespace App\Http\Controllers\Student;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Student;
use App\Contracts\UploadServiceContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InvoiceDetail;
use App\Http\Controllers\Controller;

class StudentPaymentController extends Controller
{
    /**
     * Dependency injection of upload service
     *
     * @param UploadServiceContract $uploadService Service for handling file uploads
     */
    public function __construct(private UploadServiceContract $uploadService)
    {
    }

    /**
     * Process payment for an existing invoice
     *
     * @param Request $request HTTP request containing invoice payment details
     * @return \Illuminate\Http\JsonResponse JSON response indicating payment status
     * @throws \Illuminate\Validation\ValidationException If validation fails
     * @throws \Exception If payment processing encounters an error
     */
    public function payInvoice(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            "invoice_id" => "required|exists:invoices,id", // Ensure invoice exists
            "payment_method" => "required|string|in:bank_transfer,instapay", // Validate payment method
        ]);

        // Retrieve the invoice
        $invoice = Invoice::findOrFail($validated["invoice_id"]);

        // Check if user owns the invoice
        if ($invoice->reservation->user_id !== auth()->id()) {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        // Prevent duplicate payments
        if ($invoice->status === "paid") {
            return response()->json(["message" => "Invoice already paid"], 400);
        }

        // Use database transaction for atomic operation
        DB::beginTransaction();

        try {
            // Upload payment receipt
            $paymentImage = $this->storePaymentImage(
                $request->file("invoice-receipt")
            );

            // Update invoice with payment details
            $invoice->update([
                "media_id" => $paymentImage->id,
                "status" => "paid",
                "payment_method" => $validated["payment_method"],
                "paid_at" => now(), // Mark payment timestamp
            ]);

            // Commit transaction
            DB::commit();

            return response()->json(
                ["message" => "Invoice paid successfully"],
                200
            );
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();

            // Log detailed error information
            Log::error("Invoice payment failed", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);

            return response()->json(
                ["message" => "Failed to pay invoice"],
                500
            );
        }
    }

    /**
     * Create a new invoice and process its payment
     *
     * @param Request $request HTTP request containing invoice creation details
     * @return \Illuminate\Http\JsonResponse JSON response with invoice and reservation details
     * @throws \Illuminate\Validation\ValidationException If validation fails
     * @throws \Exception If invoice creation or payment processing fails
     */
    public function addInvoice(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            "term" => "required|in:first_term,second_term", // Validate academic term
            "payment_method" => "required|string|in:bank_transfer,instapay", // Validate payment method
        ]);

        // Get authenticated user
        $user = auth()->user();

        // Retrieve user's most recent room reservation
        $lastRoom = $user
            ->reservations()
            ->with("room")
            ->latest()
            ->first()?->room;

        // Ensure user has a previous room reservation
        if (!$lastRoom) {
            return response()->json(
                ["message" => "No previous room reservation found"],
                400
            );
        }

        // Use database transaction for atomic operation
        DB::beginTransaction();

        try {
            // Create a new reservation for the student
            $reservation = $this->createNewReservation(
                $user->id,
                $lastRoom->id,
                $validated["term"]
            );

            // Store payment receipt image
            $paymentImage = $this->storePaymentImage(
                $request->file("invoice-receipt")
            );

            // Create invoice with payment details
            $invoice = $this->createInvoice(
                $reservation,
                $validated["payment_method"],
                $paymentImage
            );

            // Add itemized invoice details
            $this->createInvoiceDetails($invoice);

            // Commit transaction
            DB::commit();

            return response()->json(
                [
                    "message" => "Invoice created and paid successfully",
                    "invoice_id" => $invoice->id,
                    "reservation_id" => $reservation->id,
                ],
                201
            );
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();

            // Log detailed error information
            Log::error("Invoice creation failed", [
                "error" => $e->getMessage(),
                "user_id" => $user->id,
                "trace" => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    "message" => "Failed to create and pay invoice",
                    "error" => config("app.debug") ? $e->getMessage() : null,
                ],
                500
            );
        }
    }

    /**
     * Retrieve detailed information about a specific invoice
     *
     * @param Request $request HTTP request containing invoice ID
     * @return \Illuminate\Http\JsonResponse Detailed invoice information
     * @throws \Illuminate\Validation\ValidationException If validation fails
     */
    public function getInvoiceDetails(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            "invoice_id" => "required|exists:invoices,id", // Ensure invoice exists
        ]);

        // Retrieve invoice with related data
        $invoice = Invoice::with([
            "details",
            "reservation.user",
            "reservation.room",
        ])->findOrFail($validated["invoice_id"]);

        // Check invoice ownership
        if ($invoice->reservation->user_id !== auth()->id()) {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        // Get associated user
        $user = $invoice->reservation->user;

        // Transform invoice details for response
        $paymentDetails = $invoice->details->map(
            fn($detail) => [
                "category" => $detail->category,
                "amount" => $detail->amount,
                "description" => $detail->description,
            ]
        );

        // Calculate total invoice amount
        $totalAmount = $paymentDetails->sum("amount");

        // Return comprehensive invoice details
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

    /**
     * Create a new student room reservation
     *
     * @param int $userId ID of the user creating the reservation
     * @param int $roomId ID of the room being reserved
     * @param string $term Academic term for the reservation
     * @return Reservation Created reservation instance
     */
    private function createNewReservation($userId, $roomId, $term)
    {
        return Reservation::create([
            "user_id" => $userId,
            "room_id" => $roomId,
            "year" => "2024-2025", // Academic year
            "term" => $term,
            "status" => "upcoming", // Initial reservation status
        ]);
    }

    /**
     * Upload and store payment receipt image
     *
     * @param \Illuminate\Http\UploadedFile $file Uploaded payment receipt
     * @return mixed Uploaded file details
     */
    private function storePaymentImage($file)
    {
        return $this->uploadService->upload($file, "payments");
    }

    /**
     * Create a new invoice for a reservation
     *
     * @param Reservation $reservation Associated reservation
     * @param string $paymentMethod Payment method used
     * @param mixed $paymentImage Uploaded payment receipt
     * @return Invoice Created invoice instance
     */
    private function createInvoice($reservation, $paymentMethod, $paymentImage)
    {
        return Invoice::create([
            "reservation_id" => $reservation->id,
            "media_id" => $paymentImage->id,
            "payment_method" => $paymentMethod,
            "status" => "paid",
            "paid_at" => now(), // Payment timestamp
        ]);
    }

    /**
     * Create itemized invoice details
     *
     * @param Invoice $invoice Invoice to add details to
     */
    private function createInvoiceDetails($invoice)
    {
        // Add accommodation fee
        InvoiceDetail::create([
            "invoice_id" => $invoice->id,
            "category" => "fee",
            "amount" => 10000,
        ]);

        // Add student insurance
        InvoiceDetail::create([
            "invoice_id" => $invoice->id,
            "category" => "insurance",
            "amount" => 5000,
        ]);
    }
}
