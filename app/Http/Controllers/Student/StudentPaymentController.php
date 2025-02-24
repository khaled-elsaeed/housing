<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\UserActivity;
use App\Contracts\UploadServiceContract;
use App\Events\InvoicePaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Exception;

class StudentPaymentController extends Controller
{
    private $uploadService;

    public function __construct(UploadServiceContract $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Pay an invoice and handle payment submission.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payInvoice(Request $request)
    {
        try {
            
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'payment_method' => 'required|string|in:bank_transfer,instapay',
                'photos' => 'nullable|array',
                'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120', 
            ]);

            $invoice = Invoice::findOrFail($validated['invoice_id']);

            // Authorization check
            if ($invoice->reservation->user_id !== Auth::id()) {
                logError('Unauthorized attempt to pay invoice', 'pay_invoice', new Exception('User not authorized'));
                return errorResponse('Unauthorized', 403);
            }

            // Check if invoice is already paid
            if ($invoice->status === 'paid') {
                logError('Attempt to pay an already paid invoice', 'pay_invoice', new Exception('Invoice already paid'));
                return errorResponse('Invoice already paid', 400);
            }

            DB::beginTransaction();

            // Update invoice details
            $invoice->update([
                'status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'paid_at' => now(),
            ]);

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $this->uploadService->upload($photo, 'payments', $invoice);
                }
            }

            // Log user activity
            userActivity(Auth::id(), 'invoice_upload', 'Invoice uploaded successfully');

            // Trigger event
            event(new InvoicePaid($invoice));

            DB::commit();

            return successResponse('Invoice paid successfully');
        } catch (Exception $e) {
            DB::rollBack();
            logError('Failed to pay invoice', 'pay_invoice', $e);
            return errorResponse('Failed to pay invoice', 500);
        }
    }

    /**
     * Retrieve details of a specific invoice.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoiceDetails(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
            ]);

            $invoice = Invoice::with(['details', 'reservation.user', 'reservation.room'])
                ->findOrFail($validated['invoice_id']);

            // Authorization check
            if ($invoice->reservation->user_id !== Auth::id()) {
                logError('Unauthorized attempt to access invoice details', 'get_invoice_details', new Exception('User not authorized'));
                return errorResponse('Unauthorized', 403);
            }

            $user = $invoice->reservation->user;
            $paymentDetails = $invoice->details->map(fn($detail) => [
                'category' => $detail->category,
                'amount' => $detail->amount,
                'description' => $detail->description,
            ]);

            $totalAmount = $paymentDetails->sum('amount');

            return successResponse('Invoice details retrieved successfully', null, [
                'invoice_id' => $invoice->id,
                'reservation' => [
                    'id' => $invoice->reservation->id,
                    'customer_name' => $user->name,
                    'room_number' => $invoice->reservation->room->number,
                    'building' => $invoice->reservation->room->building->name,
                    'term' => $invoice->reservation->term,
                    'year' => $invoice->reservation->year,
                ],
                'payment_details' => $paymentDetails,
                'total_amount' => $totalAmount,
                'status' => $invoice->status,
                'payment_method' => $invoice->payment_method,
                'due_date' => $invoice->due_date,
                'paid_at' => $invoice->paid_at,
                'created_at' => $invoice->created_at,
            ]);
        } catch (Exception $e) {
            logError('Failed to retrieve invoice details', 'get_invoice_details', $e);
            return errorResponse('Failed to retrieve invoice details', 500);
        }
    }
}