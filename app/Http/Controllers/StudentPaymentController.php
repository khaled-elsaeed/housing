<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentPaymentController extends Controller
{
    public function uploadPayment(Request $request)
    {
        $request->validate([
            'payment_receipt' => 'required|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'term' => 'required|string',
        ]);
    
        $term = $request->term;
        DB::beginTransaction();
    
        try {
            // Ensure the user has a reservation
            $user = auth()->user();
            if (!$user->reservation) {
                return back()->with('error', __('messages.no_reservation_found'));
            }

            // Process for second_term
            if ($term === 'second_term') {
                $existingSecondTermInvoice = Invoice::where('reservation_id', auth()->user()->reservation_id)
                                                     ->where('term', 'second_term')
                                                     ->first();
    
                if (!$existingSecondTermInvoice) {
                    $secondTermInvoice = new Invoice();
                    $secondTermInvoice->reservation_id = $user->reservation->id;
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
                // Ensure invoice_id is present in the request
                if (!$request->has('invoice_id')) {
                    return back()->with('error', __('messages.invoice_id_missing'));
                }

                $invoiceId = $request->invoice_id;
                $invoice = Invoice::findOrFail($invoiceId);
    
                // Check if the invoice belongs to the authenticated user
                if ($invoice->reservation->user_id !== $user->id) {
                    return back()->with('error', __('messages.unauthorized_upload'));
                }
    
                // Check if the invoice is already paid
                if ($invoice->status === 'paid') {
                    return back()->with('error', __('messages.invoice_already_paid'));
                }
            }
    
            $file = $request->file('payment_receipt');
            $fileName = 'payment_' . time() . '.' . $file->getClientOriginalExtension();
            
            $directory = 'public/payments';
            
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }
            
            $filePath = $file->storeAs($directory, $fileName);
            
    
            $payment = new Payment();
            $payment->reservation_id = $invoice->reservation_id;
            $payment->amount = $invoice->amount;
            $payment->receipt_image = 'payments/' . $fileName;
            $payment->status = 'pending';
            $payment->save();
    
            $invoice->status = 'paid';
            $invoice->save();
    
            DB::commit();
    
            return back()->with('success', __('messages.payment_upload_success'));
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.payment_upload_error'));
        }
    }
    

}

