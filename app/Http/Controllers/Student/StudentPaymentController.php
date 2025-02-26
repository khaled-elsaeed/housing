<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\UserActivity;
use App\Contracts\UploadServiceContract;
use App\Events\InvoicePaid;
use Illuminate\Support\Facades\Storage;

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
            'deleted_media' => 'nullable|string',

        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // Authorization check
        if ($invoice->reservation->user_id !== Auth::id()) {
            logError('Unauthorized attempt to pay invoice', 'pay_invoice', new Exception('User not authorized'));
            return errorResponse('Unauthorized', 403);
        }

        DB::beginTransaction();

        if (!empty($validated['deleted_media'])) {
            $deletedMediaIds = explode(',', $validated['deleted_media']);
            $invoice->media()->whereIn('id', $deletedMediaIds)->delete();

            // Log user activity for media deletion
            userActivity(Auth::id(), 'invoice_media_deleted', 'Invoice media deleted: ' . $validated['deleted_media']);
        }

        // If the invoice is not already paid, mark it as paid
        if ($invoice->status !== 'paid') {
            $invoice->update([
                'status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'paid_at' => now(),
            ]);

        }

        // Handle photo uploads (for both initial payment and updates)
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $this->uploadService->upload($photo, 'payments', $invoice);
            }
        }

        // Log user activity
            // Log user activity
            userActivity(Auth::id(), 'invoice_upload', 'Invoice uploaded successfully');

        // Trigger event
        event(new InvoicePaid($invoice));

        DB::commit();

        return successResponse('Invoice media updated successfully');
    } catch (Exception $e) {
        DB::rollBack();
        logError('Failed to update invoice media', 'pay_invoice', $e);
        return errorResponse('Failed to update invoice media', 500);
    }
}
    /**
     * Retrieve media associated with a specific invoice.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

public function getInvoiceMedia($invocieId)
{
    try {
        

        $invoice = Invoice::findOrFail($invocieId);

        

        // Fetch media associated with the invoice
        $media = $invoice->media()->get()->map(function ($media) {
            return [
                'id' => $media->id,
                'path' => asset($media->path), 
                'size' => $media->size, // File size in bytes
                'created_at' => $media->created_at,
            ];
        });

        return successResponse('Invoice media retrieved successfully', null, [
            'media' => $media,
        ]);
    } catch (Exception $e) {
        logError('Failed to retrieve invoice media', 'get_invoice_media', $e);
        return errorResponse('Failed to retrieve invoice media', 500);
    }
}
}