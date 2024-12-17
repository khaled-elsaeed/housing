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
use Illuminate\Support\Facades\App;

class InvoiceController extends Controller
{
    /**
     * Display the invoices page.
     *
     * This method loads the view for the invoices page in the admin panel.
     * Logs any exceptions that occur during the process.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function showInvoicesPage()
    {
        try {
            return view('admin.invoices.index');
        } catch (\Exception $e) {
            Log::error('Error displaying Invoice page', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->view('errors.500');
        }
    }

    /**
     * Fetch statistical data related to invoices.
     *
     * This method computes statistics for invoices such as total invoices,
     * invoices by gender, paid/unpaid invoices, and accepted payments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchStats()
    {
        $invoices = Invoice::with(['reservation.user'])->get();

        $totalInvoice = $invoices->count();
        $totalMaleInvoice = $invoices
            ->filter(function ($invoice) {
                return optional($invoice->reservation->user)->gender === 'male';
            })
            ->count();

        $totalFemaleInvoice = $invoices
            ->filter(function ($invoice) {
                return optional($invoice->reservation->user)->gender === 'female';
            })
            ->count();

        $totalPaidInvoice = $invoices->where('status', 'paid')->count();
        $totalPaidMaleInvoice = $invoices
            ->filter(function ($invoice) {
                return $invoice->status === 'paid' && optional($invoice->reservation->user)->gender === 'male';
            })
            ->count();

        $totalPaidFemaleInvoice = $invoices
            ->filter(function ($invoice) {
                return $invoice->status === 'paid' && optional($invoice->reservation->user)->gender === 'female';
            })
            ->count();

        $totalUnpaidInvoice = $invoices->where('status', 'unpaid')->count();
        $totalUnpaidMaleInvoice = $invoices
            ->filter(function ($invoice) {
                return $invoice->status === 'unpaid' && optional($invoice->reservation->user)->gender === 'male';
            })
            ->count();

        $totalUnpaidFemaleInvoice = $invoices
            ->filter(function ($invoice) {
                return $invoice->status === 'unpaid' && optional($invoice->reservation->user)->gender === 'female';
            })
            ->count();

        $totalAcceptedPayments = $invoices
            ->filter(function ($invoice) {
                return $invoice->reservation->payment && $invoice->reservation->payment->status === 'accepted';
            })
            ->count();

        $totalAcceptedMalePayments = $invoices
            ->filter(function ($invoice) {
                return $invoice->reservation->payment && $invoice->reservation->payment->status === 'accepted' && optional($invoice->reservation->user)->gender === 'male';
            })
            ->count();

        $totalAcceptedFemalePayments = $invoices
            ->filter(function ($invoice) {
                return $invoice->reservation->payment && $invoice->reservation->payment->status === 'accepted' && optional($invoice->reservation->user)->gender === 'female';
            })
            ->count();

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

    /**
     * Fetch and filter a list of invoices with optional criteria.
     *
     * This method retrieves invoices and applies optional filters for gender and a custom search term.
     * The results are paginated, and relevant data is returned as a JSON response.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing filter parameters.
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchInvoices(Request $request)
{
    $currentLang = App::getLocale(); // Get current locale

    try {
        $query = Invoice::with(['reservation' => function($query) {
            $query->with(['user.student', 'payment']);
        }]);
        
        // Gender filtering
        if ($request->filled('gender')) {
            $gender = $request->get('gender');
            $query->whereHas('reservation.user.student', function ($query) use ($gender) {
                $query->where('gender', $gender);
            });
        }

        // Custom search filtering
        if ($request->filled('customSearch')) {
            $searchTerm = $request->get('customSearch');
            $query->where(function ($query) use ($searchTerm, $currentLang) {
                $query->whereHas('reservation.user.student', function ($query) use ($searchTerm, $currentLang) {
                    $query
                        ->where('name_' . ($currentLang == 'ar' ? 'ar' : 'en'), 'like', "%$searchTerm%")
                        ->orWhere('national_id', 'like', "%$searchTerm%")
                        ->orWhere('mobile', 'like', "%$searchTerm%");
                });
            });
        }

        // Order by payment status
        $query->leftJoin('reservations', 'invoices.reservation_id', '=', 'reservations.id')
            ->leftJoin('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->orderByRaw("
                CASE 
                    WHEN payments.status = 'pending' THEN 1
                    WHEN payments.status = 'rejected' THEN 2
                    WHEN payments.status = 'accepted' THEN 3
                    ELSE 4
                END
            ");

        // Count total and filtered records
        $totalRecords = $query->count('invoices.id');
        $filteredRecords = $query->count('invoices.id');

        // Paginate
        $invoices = $query
            ->select('invoices.*') // Select only invoice fields
            ->skip($request->get('start', 0))
            ->take($request->get('length', 10))
            ->get();

        return response()->json([
            'draw' => $request->get('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $invoices->map(function ($invoice) use ($currentLang) {
                $payment = $invoice->reservation->payment ?? null;
                $student = $invoice->reservation->user->student ?? null;
                $faculty = $student->faculty ?? null;

                $invoiceStatus = $invoice->status === 'paid' ? 'Paid' : 'Unpaid';
                $buttonStatus = $invoice->status === 'paid' ? '' : 'disabled';

                return [
                    'invoice_id' => $invoice->id,
                    'name' => $student ? $student->{'name_' . ($currentLang == 'ar' ? 'ar' : 'en')} : 'N/A',
                    'national_id' => $student ? $student->national_id : 'N/A',
                    'faculty' => $faculty ? $faculty->{'name_' . ($currentLang == 'ar' ? 'ar' : 'en')} : 'N/A',
                    'mobile' => $student ? $student->mobile : 'N/A',
                    'invoice_status' => $invoiceStatus,
                    'payment_status' => $payment ? $payment->status : 'No Payment',
                    'actions' =>
                        '<button type="button" class="btn btn-round btn-info-rgba" data-payment-id="' .
                        ($payment ? $payment->id : '') .
                        '" id="details-btn" title="More Details" ' .
                        $buttonStatus .
                        '><i class="feather icon-info"></i></button>',
                ];
            }),
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching invoices data: ' . $e->getMessage(), ['exception' => $e]);
        return response()->json(['error' => 'Failed to fetch invoices data.'], 500);
    }
}

    /**
     * Download all invoices in Excel format.
     *
     * This method exports a list of invoices to an Excel file and returns the file for download.
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Fetch detailed information about a specific invoice by payment ID.
     *
     * This method retrieves payment details, associated user information, and student location
     * details, and returns them as a JSON response. If the payment or user is not found,
     * an appropriate error message is returned.
     *
     * @param int $paymentId The ID of the payment to fetch.
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchInvoice($paymentId)
    {
        try {
            $payment = Payment::where('id', $paymentId)->first(['id', 'receipt_image', 'reservation_id']);

            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $reservation = $payment->reservation;
            if (!$reservation || !$reservation->user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $user = $reservation->user;

            $studentDetails = [
                'name' => optional($user->student)->name_en ?? 'N/A',
                'faculty' => optional($user->student->faculty)->name_en ?? 'N/A',
            ];

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
                $payment->payment_url = $payment->receipt_image ? asset('storage/' . $payment->receipt_image) : null;
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
            $validated = $request->validate([
                'status' => 'required|in:accepted,rejected',
            ]);

            $status = $validated['status'];

            $payment = Payment::find($paymentId);

            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $payment->status = $status;
            $payment->save();

            $invoice = $payment->reservation->invoice;
            if ($invoice) {
                if ($status === 'accepted') {
                    $invoice->status = 'paid';
                    $invoice->save();
                } elseif ($status === 'rejected') {
                    $invoice->status = 'unpaid';
                    $invoice->save();
                }

                Log::channel('access')->info('Invoice status updated', [
                    'user_id' => optional($request->user())->id,
                    'invoice_id' => $invoice->id,
                    'new_status' => $status,
                    'ip' => $request->ip(),
                    'timestamp' => now(),
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully',
                'status' => $status,
                'payment_id' => $paymentId,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating payment status: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to update payment status.'], 500);
        }
    }
}
