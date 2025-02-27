<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Insurance;
use App\Models\InvoiceDetail;
use App\Models\AdminAction;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\Invoices\InvoicesExport;
use Illuminate\Http\Request;
use App\Events\InvoiceReject;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Throwable;

class InvoiceController extends Controller
{
    // -----------------------------------
    // Page Rendering
    // -----------------------------------

    /**
     * Display the invoices page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function showInvoicesPage()
    {
        try {
            return view('admin.invoices.index');
        } catch (Throwable $e) {
            logError('Failed to load invoices page', 'show_invoices_page', $e);
            return response()->view('errors.500', [], 500);
        }
    }

    // -----------------------------------
    // Data Fetching
    // -----------------------------------

    /**
     * Fetch invoice statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchStats()
    {
        try {
            return response()->json([
                'totalInvoice' => Invoice::count(),
                'totalMaleInvoice' => Invoice::whereHas('reservation.user', fn($q) => $q->where('gender', 'male'))->count(),
                'totalFemaleInvoice' => Invoice::whereHas('reservation.user', fn($q) => $q->where('gender', 'female'))->count(),
                'totalPaidInvoice' => Invoice::where('status', 'paid')->count(),
                'totalPaidMaleInvoice' => Invoice::where('status', 'paid')
                    ->whereHas('reservation.user', fn($q) => $q->where('gender', 'male'))->count(),
                'totalPaidFemaleInvoice' => Invoice::where('status', 'paid')
                    ->whereHas('reservation.user', fn($q) => $q->where('gender', 'female'))->count(),
                'totalUnpaidInvoice' => Invoice::where('status', 'unpaid')->count(),
                'totalUnpaidMaleInvoice' => Invoice::where('status', 'unpaid')
                    ->whereHas('reservation.user', fn($q) => $q->where('gender', 'male'))->count(),
                'totalUnpaidFemaleInvoice' => Invoice::where('status', 'unpaid')
                    ->whereHas('reservation.user', fn($q) => $q->where('gender', 'female'))->count(),
                'totalAcceptedPayments' => Invoice::where('admin_approval', 'accepted')->count(),
                'totalAcceptedMalePayments' => Invoice::where('admin_approval', 'accepted')
                    ->whereHas('reservation.user', fn($q) => $q->where('gender', 'male'))->count(),
                'totalAcceptedFemalePayments' => Invoice::where('admin_approval', 'accepted')
                    ->whereHas('reservation.user', fn($q) => $q->where('gender', 'female'))->count(),
            ]);
        } catch (Throwable $e) {
            logError('Failed to fetch invoice statistics', 'fetch_invoice_stats', $e);
            return errorResponse(trans('Failed to fetch invoice statistics.'), 500);
        }
    }

    /**
     * Fetch invoices with filtering and sorting for DataTables.
     *
     * @param Request $request HTTP request
     * @return \Illuminate\Http\JsonResponse
     */
  
   public function fetchInvoices(Request $request)
   {
       try {
           $currentLang = App::getLocale();
           $query = Invoice::with(['reservation.user.student.faculty'])
               ->select('invoices.*')
               ->orderByRaw("
                   CASE 
                       WHEN status = 'pending' THEN 1
                       WHEN status = 'unpaid' THEN 2
                       ELSE 3
                   END,
                   CASE 
                       WHEN admin_approval = 'pending' THEN 1
                       WHEN admin_approval = 'accepted' THEN 2
                       ELSE 3
                   END
               ")
               ->orderBy('created_at', 'asc');

           if ($request->filled('gender')) {
               $query->whereHas('reservation.user.student', fn($q) => $q->where('gender', $request->get('gender')));
           }

           if ($request->filled('customSearch')) {
               $searchTerm = $request->get('customSearch');
               $query->whereHas('reservation.user.student', function ($q) use ($searchTerm, $currentLang) {
                   $q->where('name_' . ($currentLang == 'ar' ? 'ar' : 'en'), 'like', "%{$searchTerm}%")
                       ->orWhere('national_id', 'like', "%{$searchTerm}%")
                       ->orWhere('phone', 'like', "%{$searchTerm}%");
               });
           }

           if ($request->filled('admin_approval')) {
               $query->where('admin_approval', $request->get('admin_approval'));
           }

           return DataTables::of($query)
               ->addColumn('name', fn($invoice) => $invoice->reservation->user->student?->name ?? trans('N/A'))
               ->addColumn('national_id', fn($invoice) => $invoice->reservation->user->student?->national_id ?? trans('N/A'))
               ->addColumn('faculty', fn($invoice) => $invoice->reservation->user->student?->faculty?->name ?? trans('N/A'))
               ->addColumn('phone', fn($invoice) => $invoice->reservation->user->student?->phone ?? trans('N/A'))
               ->addColumn('status', fn($invoice) => trans($invoice->status))
               ->addColumn('admin_approval', fn($invoice) => trans($invoice->admin_approval))
               ->editColumn('reservation_duration', function ($invoice) {
                   // Check if the reservation period type is "long" and an academic term is associated
if ($invoice->reservation->period_type === "long" && $invoice->reservation->academicTerm) {
   // Construct the formatted string using the academic term details
   return trans('long_period_format', [
       'semester' => trans($invoice->reservation->academicTerm->semester),
       'name' => trans($invoice->reservation->academicTerm->name),
       'year' => app()->getLocale() == 'ar' ? arabicNumbers($invoice->reservation->academicTerm->academic_year) :$invoice->reservation->academicTerm->academic_year ,
   ]);
}

// Fallback to the period duration or 'N/A' if not available
return $invoice->reservation->period_duration ?? trans('N/A');
               })
               ->make(true);
       } catch (Throwable $e) {
           logError('Failed to fetch invoices', 'fetch_invoices', $e);
           return errorResponse(trans("Failed to fetch invoices data."), 500);
       }
   }


    /**
     * Fetch specific invoice details.
     *
     * @param int $invoiceId Invoice ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchInvoice($invoiceId)
    {
        try {
            $invoice = Invoice::with([
                'reservation.user.student.faculty',
                'reservation.room.apartment.building',
                'reservation.room.apartment',
                'media',
                'details'
            ])->findOrFail($invoiceId);

            $user = $invoice->reservation->user;
            $room = $invoice->reservation->room;
            $insurance = Insurance::where('user_id', $user->id)->first();

            $studentDetails = [
                'name' => $user->student->name ?? trans('N/A'),
                'balance' => $user->balance . ' EGP',
                'faculty' => $user->student->faculty->name ?? trans('N/A'),
                'building' => $room->apartment->building->number ?? trans('N/A'),
                'apartment' => $room->apartment->number ?? trans('N/A'),
                'room' => $room->number ?? trans('N/A'),
            ];

            $invoiceDetails = $invoice->details->map(fn($detail) => [
                'id' => $detail->id,
                'invoice_id' => $detail->invoice_id,
                'status' => $detail->status,
                'category' => $detail->category,
                'amount' => $detail->amount,
                'description' => $detail->description,
            ])->values()->all();

            $mediaArray = $invoice->media->map(fn($media) => [
                'payment_url' => asset($media->path),
                'collection' => $media->collection_name,
                'created_at' => $media->created_at,
                'updated_at' => $media->updated_at,
            ])->all();

            return response()->json([
                'studentDetails' => $studentDetails,
                'invoiceDetails' => $invoiceDetails,
                'media' => $mediaArray,
                'invoice_id' => $invoice->id,
                'status' => $invoice->status,
                'rejectReason' => $invoice->reject_reason,
                'admin_approval' => $invoice->admin_approval,
                'notes' => $invoice->notes,
                'pastInsurance' => $insurance?->amount ?? 0,
            ]);
        } catch (Throwable $e) {
            logError('Failed to fetch invoice details', 'fetch_invoice_details', $e);
            return errorResponse(trans('Failed to fetch invoice details'), 500);
        }
    }

    // -----------------------------------
    // Payment Status Management
    // -----------------------------------

    /**
     * Update payment status for an invoice.
     *
     * @param Request $request HTTP request
     * @param int $invoiceId Invoice ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePaymentStatus(Request $request, $invoiceId)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:accepted,rejected',
                'paidDetails' => 'required_if:status,accepted|array|min:1',
                'paidDetails.*.detailId' => 'required_if:status,accepted|exists:invoice_details,id',
                'paidDetails.*.amount' => 'required_if:status,accepted|numeric',
                'overPaymentAmount' => 'nullable|numeric|min:0',
                'newInsuranceAmount' => 'nullable|numeric|min:0',
                'rejectReason' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $invoice = Invoice::findOrFail($invoiceId);
            $status = $validated['status'];
            $invoice->admin_approval = $status;
            $invoice->notes = $validated['notes'] ?? null;

            if ($status === 'rejected') {
                $invoice->status = 'pending';
                $invoice->reject_reason = $validated['rejectReason'] ?? null;
                $invoice->save();
                event(new InvoiceReject($invoice));
            } elseif ($status === 'accepted') {
                $invoice->reject_reason = NULL;
                $invoice->status = "paid";
                $this->processAcceptedPayment($invoice, $validated);
            }

            $invoice->save();
            $this->logAdminAction($request, $invoiceId, $status, $validated);

            DB::commit();

            return successResponse(trans('Invoice status updated successfully'), null, ['status' => $status]);
        } catch (Throwable $e) {
            DB::rollBack();
            logError('Failed to update payment status', 'update_payment_status', $e);
            return errorResponse(trans('Failed to update payment status.'), 500);
        }
    }

    // -----------------------------------
    // Helper Methods
    // -----------------------------------

    /**
     * Process accepted payment details.
     *
     * @param Invoice $invoice Invoice instance
     * @param array $validated Validated request data
     * @return void
     */
    private function processAcceptedPayment(Invoice $invoice, array $validated): void
    {

        $this->markInvoiceDetailsAsPaid($validated['paidDetails'], $invoice);
        $this->updateReservationStatus($invoice);

        if (isset($validated['newInsuranceAmount']) && $validated['newInsuranceAmount'] > 0) {
            Insurance::updateOrCreate(
                ['user_id' => $invoice->reservation->user->id],
                [
                    'amount' => $validated['newInsuranceAmount'],
                    'status' => 'active'
                ]
            );
        }

        // Update invoice status to paid if all details are paid
        $allPaid = $invoice->details->every(fn($detail) => $detail->status === 'paid');
        if ($allPaid) {
            $invoice->status = 'paid';
        }
    }

    /**
     * Mark invoice details as paid.
     *
     * @param array $paidDetails Payment details
     * @param Invoice $invoice Invoice instance
     * @return void
     */
    private function markInvoiceDetailsAsPaid(array $paidDetails, Invoice $invoice): void
    {
        foreach ($paidDetails as $detail) {
            $invoiceDetail = InvoiceDetail::find($detail['detailId']);
            if ($invoiceDetail && $invoiceDetail->invoice_id === $invoice->id) { // Ensure detail belongs to this invoice
                $invoiceDetail->status = 'paid';
                $invoiceDetail->amount = $detail['amount'];
                $invoiceDetail->save();
            }
        }
    }

    /**
     * Update reservation status to active if pending.
     *
     * @param Invoice $invoice Invoice instance
     * @return void
     */
    private function updateReservationStatus(Invoice $invoice): void
    {
        if ($invoice->reservation && $invoice->reservation->status === 'pending') {
            $invoice->reservation->status = 'active';
            $invoice->reservation->save();
        }
    }

    /**
     * Log admin action for auditing.
     *
     * @param Request $request HTTP request
     * @param int $invoiceId Invoice ID
     * @param string $status New status
     * @param array $validated Validated request data
     * @return void
     */
    private function logAdminAction(Request $request, $invoiceId, string $status, array $validated): void
    {
        try {
            AdminAction::create([
                'admin_id' => Auth::id(),
                'invoice_id' => $invoiceId,
                'action' => 'update_payment_status',
                'description' => "Updated payment status to {$status}",
                'status' => $status,
                'changes' => json_encode($validated),
                'ip_address' => $request->ip(),
            ]);
        } catch (Throwable $e) {
            logError('Failed to log admin action', 'log_admin_action', $e);
        }
    }
}