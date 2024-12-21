<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StudentPaymentController extends Controller
{
    /**
     * Upload the payment receipt for the student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $invoiceId
     * @return \Illuminate\Http\Response
     */
    public function uploadPayment(Request $request, $invoiceId)
    {
        // Validation for the uploaded receipt
        $request->validate([
            'payment_receipt' => 'required|image|mimes:jpg,jpeg,png,pdf|max:2048', 
        ]);

        // Find the invoice by ID
        $invoice = Invoice::findOrFail($invoiceId);

        // Check if the student owns this reservation/invoice
        if ($invoice->reservation->user_id !== Auth::id()) {
            return back()->with('error', 'You are not authorized to upload a payment for this invoice.');
        }

        // Check if the invoice is unpaid
        if ($invoice->status === 'paid') {
            return back()->with('error', 'This invoice is already paid.');
        }

        // Store the uploaded receipt image in the 'public/payments' folder
        $file = $request->file('payment_receipt');
        $fileName = 'payment_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('public/payments', $fileName);

        // Create a new payment record
        $payment = new Payment();
        $payment->reservation_id = $invoice->reservation_id;
        $payment->amount = $invoice->amount;
        $payment->receipt_image = 'payments/' . $fileName; // Save the path in the database
        $payment->status = 'pending';  // Payment is initially 'pending'
        $payment->save();

        // Optionally update the invoice status (e.g., mark as 'pending')
        $invoice->status = 'paid';  // Update invoice status to pending after upload
        $invoice->save();

        // Return success message to the user
        return back()->with('success', 'Payment receipt uploaded successfully. Your payment is under review.');
    }
}
