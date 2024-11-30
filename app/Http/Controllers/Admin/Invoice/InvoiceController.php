<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment; // Add the Payment model import
use Yajra\DataTables\DataTables;
use App\Exports\Invoices\InvoicesExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function showInvoicesPage()
    {
        try {
            Log::info('We are on the invoices page');
            return view('admin.invoices.index');
        } catch (\Exception $e) {
            Log::error('Error displaying Invoice page', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->view('errors.500');
        }
    }

    public function fetchStats()
    {
        $invoices = Invoice::with(['reservation.user'])->get();

        $totalInvoice = $invoices->count();
        $totalMaleInvoice = $invoices->filter(function ($invoice) {
            return optional($invoice->reservation->user)->gender === 'male';
        })->count();

        $totalFemaleInvoice = $invoices->filter(function ($invoice) {
            return optional($invoice->reservation->user)->gender === 'female';
        })->count();

        $totalPaidInvoice = $invoices->where('status', 'paid')->count();
        $totalPaidMaleInvoice = $invoices->filter(function ($invoice) {
            return $invoice->status === 'paid' && optional($invoice->reservation->user)->gender === 'male';
        })->count();

        $totalPaidFemaleInvoice = $invoices->filter(function ($invoice) {
            return $invoice->status === 'paid' && optional($invoice->reservation->user)->gender === 'female';
        })->count();

        $totalUnpaidInvoice = $invoices->where('status', 'unpaid')->count();
        $totalUnpaidMaleInvoice = $invoices->filter(function ($invoice) {
            return $invoice->status === 'unpaid' && optional($invoice->reservation->user)->gender === 'male';
        })->count();

        $totalUnpaidFemaleInvoice = $invoices->filter(function ($invoice) {
            return $invoice->status === 'unpaid' && optional($invoice->reservation->user)->gender === 'female';
        })->count();

        $totalAcceptedPayments = $invoices->filter(function ($invoice) {
            return $invoice->reservation->payment && $invoice->reservation->payment->status === 'accepted';
        })->count();

        $totalAcceptedMalePayments = $invoices->filter(function ($invoice) {
            return $invoice->reservation->payment && $invoice->reservation->payment->status === 'accepted' && optional($invoice->reservation->user)->gender === 'male';
        })->count();

        $totalAcceptedFemalePayments = $invoices->filter(function ($invoice) {
            return $invoice->reservation->payment && $invoice->reservation->payment->status === 'accepted' && optional($invoice->reservation->user)->gender === 'female';
        })->count();

        return response()->json([
            'totalInvoice' => $totalInvoice,
            'totalMaleInvoice' => $totalMaleInvoice,
            'totalFemaleInvoice' => $totalFemaleInvoice,
            'totalPaidInvoice' => $totalPaidInvoice,
            'totalPaidMaleInvoice' => $totalPaidMaleInvoice,
            'totalPaidFemaleInvoice' => $totalPaidFemaleInvoice,
            'totalUnpaidInvoice' => $totalUnpaidInvoice,
            'totalUnpaidMaleInvoice' => $totalUnpaidMaleInvoice,
            'totalUnpaidFemaleInvoice' => $totalUnpaidFemaleInvoice,
            'totalAcceptedPayments' => $totalAcceptedPayments,
            'totalAcceptedMalePayments' => $totalAcceptedMalePayments,
            'totalAcceptedFemalePayments' => $totalAcceptedFemalePayments,
        ]);
    }

    public function fetchInvoices(Request $request)
    {
        try {
            $query =  Invoice::with(['reservation'])
            ->orderBy('status', 'Desc'); 
    
            if ($request->filled('gender')) {
                $gender = $request->get('gender');
                $query->whereHas('reservation.user.student', function ($query) use ($gender) {
                    if ($gender) {
                        $query->where('gender', $gender); 
                    }
                });
            }
    
            if ($request->filled('customSearch')) {
                $searchTerm = $request->get('customSearch');
                $query->where(function ($query) use ($searchTerm) {
                    $query->whereHas('reservation.user.student', function ($query) use ($searchTerm) {
                        $query
                            ->where('name_en', 'like', "%$searchTerm%")
                            ->orWhere('national_id', 'like', "%$searchTerm%")
                            ->orWhere('mobile', 'like', "%$searchTerm%");
                    });
                });
            }
    
            $invoices = $query->paginate($request->get('length', 10));
    
            return response()->json([
                'draw' => $request->get('draw'),
                'recordsTotal' => $invoices->total(),
                'recordsFiltered' => $invoices->total(),
                'data' => $invoices->map(function ($invoice) {
                    $invoiceStatus = $invoice ? ($invoice->status == 'paid' ? 'Paid' : 'Unpaid') : 'No Invoice';
                    $buttonStatus = $invoice && $invoice->status == 'paid' ? '' : 'disabled';
    
                    return [
                        'invoice_id' => $invoice->id,  
                        'name' => optional($invoice->reservation->user->student)->name_en ?? 'N/A',  // Student name
                        'national_id' => optional($invoice->reservation->user->student)->national_id ?? 'N/A',  // Student national ID
                        'faculty' => optional($invoice->reservation->user->student->faculty)->name_en ?? 'N/A',  // Student faculty name
                        'mobile' => optional($invoice->reservation->user->student)->mobile ?? 'N/A',  // Student mobile number
                        'invoice_status' => $invoiceStatus,  // Invoice payment status
                        'payment_status' => optional($invoice->reservation->payment)->status ?? 'No Payment',  // Invoice payment status
                        'actions' => '<button type="button" class="btn btn-round btn-info-rgba" data-payment-id="' . optional($invoice->reservation->payment)->id . '" id="details-btn" title="More Details" ' . $buttonStatus . '><i class="feather icon-info"></i></button>',
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            // Log error and return a response with a failure message
            Log::error('Error fetching invoices data: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch invoices data.'], 500);
        }
    }
    

    public function downloadInvoicesExcel()
    {
        try {
            
            $export = new InvoicesExport();
            return $export->downloadExcel();
        } catch (\Exception $e) {
            Log::error('Error exporting applicants to Excel', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to export applicants to Excel'], 500);
        }
    }

    public function fetchInvoice($paymentId)
    {
        try {
            // Retrieve the payment with the given paymentId
            $payment = Payment::where('id', $paymentId)->first(['id', 'receipt_image', 'reservation_id']);
            
            // If payment not found, return error
            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }
    
            // Retrieve the user directly using the reservation associated with the payment
            $reservation = $payment->reservation;
            if (!$reservation || !$reservation->user) {
                return response()->json(['error' => 'User not found'], 404);
            }
    
            $user = $reservation->user;
    
            // Prepare student details with optional checks
            $studentDetails = [
                'name' => optional($user->student)->name_en ?? 'N/A',
                'faculty' => optional($user->student->faculty)->name_en ?? 'N/A',
            ];
    
            // Get location details
            $location = $user->getLocationDetails();
            if ($location) {
                $studentDetails['building'] = $location['building'] ?? 'N/A';
                $studentDetails['apartment'] = $location['apartment'] ?? 'N/A';
                $studentDetails['room'] = $location['room'] ?? 'N/A';
            } else {
                $studentDetails['building'] = 'N/A';
                $studentDetails['apartment'] = 'N/A';
                $studentDetails['room'] = 'N/A';
            }
    
            $payments = Payment::where('id', $paymentId)->get();
            $payments->transform(function ($payment) {
                $payment->payment_url = $payment->receipt_image 
                    ? asset('storage/' . $payment->receipt_image) 
                    : null;  
                return $payment;
            });
    
            return response()->json([
                'studentDetails' => $studentDetails,
                'payments' => $payments,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching invoice details: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch invoice details.'], 500);
        }
    }

    /**
     * Update the status of a payment to either 'accepted' or 'rejected'.
     *
     * @param Request $request
     * @param int $paymentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePaymentStatus(Request $request, $paymentId)
    {
        try {
            // Validate the request to ensure a status ('accepted' or 'rejected') is provided
            $validated = $request->validate([
                'status' => 'required|in:accepted,rejected',
            ]);

            $status = $validated['status'];

            // Find the payment by ID
            $payment = Payment::find($paymentId);

            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Update the payment status
            $payment->status = $status;
            $payment->save();

            // Optionally, you can add logic to update the associated invoice status
            $invoice = $payment->reservation->invoice;
            if ($invoice) {
                // If the payment is accepted, update the invoice status to 'paid'
                if ($status === 'accepted') {
                    $invoice->status = 'paid';
                    $invoice->save();
                } elseif ($status === 'rejected') {
                    $invoice->status = 'unpaid';
                    $invoice->save();
                }
            }

            // Log the payment status update
            Log::info("Payment status updated to '{$status}' for payment ID: {$paymentId}");

            // Return a success response
            return response()->json([
                'message' => 'Payment status updated successfully',
                'status' => $status,
                'payment_id' => $paymentId,
            ]);

        } catch (\Exception $e) {
            // Log any errors
            Log::error('Error updating payment status: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to update payment status.'], 500);
        }
    }
    
    
}
