<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentPaymentController extends Controller
{
    public function uploadPayment(Request $request)
    {
        // Validate the request
        $request->validate([
            'payment_receipt' => 'required|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'term' => 'required|string',
        ]);
    
        $term = $request->term;
        DB::beginTransaction();
    
        try {
            // Process for second_term
            if ($term === 'second_term') {
                $existingSecondTermInvoice = Invoice::where('reservation_id', auth()->user()->reservation_id)
                                                     ->where('term', 'second_term')
                                                     ->first();
    
                if (!$existingSecondTermInvoice) {
                    // Create new second term invoice
                    $secondTermInvoice = new Invoice();
                    $secondTermInvoice->reservation_id = auth()->user()->reservation->id;
                    $secondTermInvoice->amount = 15000;
                    $secondTermInvoice->status = 'unpaid';
                    $secondTermInvoice->term = 'second_term';
                    $secondTermInvoice->save();
    
                    $invoice = $secondTermInvoice;
                } else {
                    // Use existing second term invoice
                    $invoice = $existingSecondTermInvoice;
                }
            } else {
                // Process for first_term
                $invoiceId = $request->invoice_id;
                $invoice = Invoice::findOrFail($invoiceId);
    
                // Check if the user is authorized to make payment
                if ($invoice->reservation->user_id !== auth()->id()) {
                    return back()->with('error', __('messages.unauthorized_upload'));
                }
    
                // Check if the invoice is already paid
                if ($invoice->status === 'paid') {
                    return back()->with('error', __('messages.invoice_already_paid'));
                }
            }
    
            // Process file upload
            $file = $request->file('payment_receipt');
            $fileName = 'payment_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/payments', $fileName);
    
            // Save payment record
            $payment = new Payment();
            $payment->reservation_id = $invoice->reservation_id;
            $payment->amount = $invoice->amount;
            $payment->receipt_image = 'payments/' . $fileName;
            $payment->status = 'pending';
            $payment->save();
    
            // Update invoice status
            $invoice->status = 'paid';
            $invoice->save();
    
            DB::commit();
    
            // Return success response
            return back()->with('success', __('messages.payment_upload_success'));
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.payment_upload_error'));
        }
    }
    

}

