<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StudentPaymentController extends Controller
{
    public function uploadPayment(Request $request)
{
    $request->validate([
        'payment_receipt' => 'required|image|mimes:jpg,jpeg,png,pdf|max:2048',
        'term' => 'required|string',
    ]);

    DB::beginTransaction();

    try {
        $user = auth()->user();
        $invoice = $this->getInvoiceForTerm($request->term, $user, $request);

        if (!$invoice) {
            return back()->with('error', __('messages.invoice_not_found'));
        }

        $file = $request->file('payment_receipt');
        if (!$file) {
            return back()->with('error', __('messages.payment_receipt_missing'));
        }

        $filePath = $this->storePaymentReceipt($file);

        // If file is not uploaded correctly, return an error
        if (!$filePath) {
            return back()->with('error', __('messages.payment_upload_error'));
        }

        $this->createPaymentRecord($invoice, $filePath);
        $this->markInvoiceAsPaid($invoice);

        DB::commit();
        return back()->with('success', __('messages.payment_upload_success'));

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', __('messages.payment_upload_error'));
    }
}


    private function getInvoiceForTerm($term, $user, $request)
    {
        if ($term === 'second_term') {
            return $this->getSecondTermInvoice($user);
        } else {
            return $this->getExistingInvoice($user, $request);
        }
    }

    private function getSecondTermInvoice($user)
    {
        $existingInvoice = Invoice::where('reservation_id', $user->reservation_id)
                                  ->where('term', 'second_term')
                                  ->first();

        if (!$existingInvoice) {
            $invoice = new Invoice();
            $invoice->reservation_id = $user->reservation->id;
            $invoice->amount = 15000;
            $invoice->status = 'unpaid';
            $invoice->term = 'second_term';
            $invoice->save();
            return $invoice;
        }

        return $existingInvoice;
    }

    private function getExistingInvoice($user, $request)
    {
        if (!$request->has('invoice_id')) {
            return null;
        }

        $invoice = Invoice::findOrFail($request->invoice_id);

        if ($invoice->reservation->user_id !== $user->id || $invoice->status === 'paid') {
            return null;
        }

        return $invoice;
    }

    private function storePaymentReceipt($file)
{
    try {
        $fileName = 'payment_' . time() . '.' . $file->getClientOriginalExtension();
        $directory = 'payments';

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $filePath = $file->storeAs($directory, $fileName, 'public');
        return $filePath;
    } catch (\Exception $e) {
        \Log::error('Error storing payment receipt:', ['error' => $e->getMessage()]);
        return null; 
    }
}


    private function createPaymentRecord($invoice, $filePath)
    {
        $payment = new Payment();
        $payment->reservation_id = $invoice->reservation_id;
        $payment->amount = $invoice->amount;
        $payment->receipt_image = $filePath;
        $payment->status = 'pending';
        $payment->save();
    }

    private function markInvoiceAsPaid($invoice)
    {
        $invoice->status = 'paid';
        $invoice->save();
    }
}
