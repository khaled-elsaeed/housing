<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentPaymentController extends Controller
{
    /**
     * Handle the payment receipt upload and payment processing.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadPayment(Request $request)
    {
        $request->validate([
            'payment_receipt' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'term' => 'required|string',
        ]);

        $term = $request->term;
        DB::beginTransaction();

        try {
            $user = auth()->user();

            // Ensure the user has a reservation
            if (!$user->reservation) {
                return back()->with('error', __('pages.student.profile.no_reservation_found'));
            }

            // Handle invoice creation for second term or existing invoice
            if ($term === 'second_term') {
                $existingSecondTermInvoice = $this->CheckIfSecondTermInvoiceExists($user->reservation->id);

                if (!$existingSecondTermInvoice) {
                    $invoice = $this->createInvoice('second_term', $user->reservation->id);
                } else {
                    $invoice = $existingSecondTermInvoice;
                }
            } else {
                $invoice = Invoice::findOrFail($request->invoice_id);

                if ($invoice->reservation->user_id !== $user->id) {
                    return back()->with('error', __('pages.student.profile.unauthorized_upload'));
                }

                if ($invoice->status === 'paid') {
                    return back()->with('error', __('pages.student.profile.invoice_already_paid'));
                }
            }

            // Upload the payment receipt and get the URL
            $imageUrl = $this->handlePaymentReceiptUpload($request->file('payment_receipt'));

            // Create payment record
            $this->createPayment($invoice->reservation_id, $imageUrl);

            $invoice->status = 'paid';
            $invoice->save();

            DB::commit();

            return back()->with('success', __('pages.student.profile.payment_upload_success'));

        } catch (\Exception $e) {
            Log::error('Error uploading payment receipt', ['error' => $e->getMessage()]);
            DB::rollBack();
            return back()->with('error', __('pages.student.profile.payment_upload_error'));
        }
    }

    /**
     * Check if a second term invoice exists for the given reservation.
     *
     * @param int $reservationId
     * @return Invoice|null
     */
    private function CheckIfSecondTermInvoiceExists($reservationId)
    {
        return Invoice::where('reservation_id', $reservationId)
            ->where('term', 'second_term')
            ->first();
    }

    /**
     * Create a new invoice for a specific term and reservation.
     *
     * @param string $term
     * @param int $reservationId
     * @return Invoice
     */
    private function createInvoice($term, $reservationId)
    {
        $invoice = new Invoice();
        $invoice->reservation_id = $reservationId;
        $invoice->amount = 15000;
        $invoice->status = 'unpaid';
        $invoice->term = $term;
        $invoice->save();

        return $invoice;
    }

    /**
     * Create a new payment record.
     *
     * @param int $reservationId
     * @param string $imageUrl
     * @return Payment
     */
    private function createPayment($reservationId, $imageUrl)
    {
        $payment = new Payment();
        $payment->reservation_id = $reservationId;
        $payment->amount = 15000;
        $payment->receipt_image = $imageUrl;
        $payment->status = 'pending';
        $payment->save();

        return $payment;
    }

    /**
     * Handle file upload of the payment receipt.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    private function handlePaymentReceiptUpload($file)
    {
        $directory = 'payments';

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $path = Storage::disk('public')->putFile($directory, $file);

        // Generate the full URL for the file
        return asset('storage/' . $path);
    }
}
