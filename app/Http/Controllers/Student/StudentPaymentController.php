<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Contracts\UploadServiceContract;
use App\Events\InvoicePaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Exception;

class StudentPaymentController extends Controller
{
    /**
     * @var UploadServiceContract
     */
    private $uploadService;

    /**
     * StudentPaymentController constructor.
     *
     * @param UploadServiceContract $uploadService
     */
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
                'photos.*' => 'image|mimes:jpeg,png,jpg|max:3072', // Max 5MB
            ]);

            $invoice = Invoice::findOrFail($validated['invoice_id']);

            // Authorization check
            if ($invoice->reservation->user_id !== Auth::id()) {
                logError('Unauthorized attempt to pay invoice', 'pay_invoice', new Exception('User not authorized'));
                return errorResponse('Unauthorized', 403);
            }

            DB::beginTransaction();

            if ($invoice->status !== 'paid') {
                $invoice->update([
                    'status' => 'pending',
                    'payment_method' => $validated['payment_method'],
                ]);
            }

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
     * Update media associated with an invoice.
     *
     * @param Request $request
     * @param int $invoiceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMedia(Request $request, $invoiceId)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);

            // Authorization check
            if ($invoice->reservation->user_id !== Auth::id()) {
                logError('Unauthorized attempt to update media', 'update_media', new Exception('User not authorized'));
                return errorResponse('Unauthorized', 403);
            }

            // Handle new photo uploads
            if ($request->hasFile('photos')) {
                $request->validate([
                    'photos' => 'array',
                    'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
                ]);
                foreach ($request->file('photos') as $photo) {
                    $this->uploadService->upload($photo, 'payments', $invoice);
                }
                $invoice->admin_approval = 'pending';
                $invoice->save();
            }

            // Handle deleted media
            if ($request->filled('deleted_media')) {
                $deletedMediaIds = explode(',', $request->input('deleted_media'));
                $mediaItems = $invoice->media()->whereIn('id', $deletedMediaIds)->get();
                foreach ($mediaItems as $media) {
                    $this->uploadService->delete($media);
                }
                $invoice->admin_approval = 'pending';
                $invoice->save();
            }
            return successResponse(trans('Payment images updated successfully'), null, ['media' => $media]);

        } catch (Exception $e) {
            logError('Failed to update invoice media', 'update_media', $e);
            return errorResponse('Failed to update invoice media', 500);
        }
    }

    /**
     * Retrieve media associated with a specific invoice.
     *
     * @param int $invoiceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoiceMedia($invoiceId)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);

            // Authorization check
            if ($invoice->reservation->user_id !== Auth::id()) {
                logError('Unauthorized attempt to retrieve media', 'get_invoice_media', new Exception('User not authorized'));
                return errorResponse('Unauthorized', 403);
            }

            // Fetch media associated with the invoice
            $media = $invoice->media()->get()->map(function ($media) {
                return [
                    'id' => $media->id,
                    'path' => asset($media->path),
                    'size' => $media->size, 
                    'created_at' => $media->created_at,
                ];
            });

            return successResponse(trans('Invoice media retrieved successfully'), null, ['media' => $media]);
        } catch (Exception $e) {
            logError('Failed to retrieve invoice media', 'get_invoice_media', $e);
            return errorResponse('Failed to retrieve invoice media', 500);
        }
    }
}