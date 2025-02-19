<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Insurance;
use App\Models\InvoiceDetail;
use App\Models\AdminAction; // Assuming you have a model for admin action logs
use Yajra\DataTables\Facades\DataTables;
use App\Exports\Invoices\InvoicesExport;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display the invoices page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function showInvoicesPage()
    {
        try {
            return view("admin.invoices.index");
        } catch (\Exception $e) {
            Log::error('Failed to load invoices page', [
                'error' => $e->getMessage(),
                'action' => 'show_invoices_page',
                'user_id' => auth()->id(), // Log the admin performing the action
            ]);
            return response()->view("errors.500");
        }
    }

    /**
     * Fetch statistical data related to invoices.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchStats()
    {
        try {
            // Total invoices
            $totalInvoice = Invoice::count();

            // Total male and female invoices
            $totalMaleInvoice = Invoice::whereHas("reservation.user", function ($query) {
                $query->where("gender", "male");
            })->count();

            $totalFemaleInvoice = Invoice::whereHas("reservation.user", function ($query) {
                $query->where("gender", "female");
            })->count();

            // Paid invoices
            $totalPaidInvoice = Invoice::where("status", "paid")->count();
            $totalPaidMaleInvoice = Invoice::where("status", "paid")
                ->whereHas("reservation.user", function ($query) {
                    $query->where("gender", "male");
                })
                ->count();
            $totalPaidFemaleInvoice = Invoice::where("status", "paid")
                ->whereHas("reservation.user", function ($query) {
                    $query->where("gender", "female");
                })
                ->count();

            // Unpaid invoices
            $totalUnpaidInvoice = Invoice::where("status", "unpaid")->count();
            $totalUnpaidMaleInvoice = Invoice::where("status", "unpaid")
                ->whereHas("reservation.user", function ($query) {
                    $query->where("gender", "male");
                })
                ->count();
            $totalUnpaidFemaleInvoice = Invoice::where("status", "unpaid")
                ->whereHas("reservation.user", function ($query) {
                    $query->where("gender", "female");
                })
                ->count();

            // Accepted payments
            $totalAcceptedPayments = Invoice::where("admin_approval", "accepted")->count();
            $totalAcceptedMalePayments = Invoice::where("admin_approval", "accepted")
                ->whereHas("reservation.user", function ($query) {
                    $query->where("gender", "male");
                })
                ->count();
            $totalAcceptedFemalePayments = Invoice::where("admin_approval", "accepted")
                ->whereHas("reservation.user", function ($query) {
                    $query->where("gender", "female");
                })
                ->count();

            // Return the response
            return response()->json([
                "totalInvoice" => $totalInvoice,
                "totalMaleInvoice" => $totalMaleInvoice,
                "totalFemaleInvoice" => $totalFemaleInvoice,
                "totalPaidInvoice" => $totalPaidInvoice,
                "totalPaidMaleInvoice" => $totalPaidMaleInvoice,
                "totalPaidFemaleInvoice" => $totalPaidFemaleInvoice,
                "totalUnpaidInvoice" => $totalUnpaidInvoice,
                "totalUnpaidMaleInvoice" => $totalUnpaidMaleInvoice,
                "totalUnpaidFemaleInvoice" => $totalUnpaidFemaleInvoice,
                "totalAcceptedPayments" => $totalAcceptedPayments,
                "totalAcceptedMalePayments" => $totalAcceptedMalePayments,
                "totalAcceptedFemalePayments" => $totalAcceptedFemalePayments,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch invoice statistics', [
                'error' => $e->getMessage(),
                'action' => 'fetch_invoice_stats',
                'user_id' => auth()->id(),
            ]);
            return response()->json(["error" => "Failed to fetch invoice statistics."], 500);
        }
    }

    /**
     * Fetch and filter a list of invoices with optional criteria.
     *
     * @param \Illuminate\Http\Request $request
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
                    WHEN status = 'paid' THEN 1
                    WHEN status = 'unpaid' THEN 2
                    ELSE 3
                END,
                CASE 
                    WHEN admin_approval = 'pending' THEN 1
                    WHEN admin_approval = 'accepted' THEN 2
                    ELSE 3
                END
            ");

        // Gender filtering
        if ($request->filled('gender')) {
            $gender = $request->get('gender');
            $query->whereHas('reservation.user.student', function ($q) use ($gender) {
                $q->where('gender', $gender);
            });
        }

        // Custom search filtering
        if ($request->filled('customSearch')) {
            $searchTerm = $request->get('customSearch');
            $query->where(function ($q) use ($searchTerm, $currentLang) {
                $q->whereHas('reservation.user.student', function ($studentQuery) use ($searchTerm, $currentLang) {
                    $studentQuery->where('name_' . ($currentLang == 'ar' ? 'ar' : 'en'), 'like', "%{$searchTerm}%")
                        ->orWhere('national_id', 'like', "%{$searchTerm}%")
                        ->orWhere('phone', 'like', "%{$searchTerm}%");
                });
            });
        }

        // Filter by approval status
        if ($request->filled('admin_approval')) {
            $query->where('admin_approval', $request->get('admin_approval'));
        }

        return DataTables::of($query)
            ->addColumn('name', function ($invoice)  {
                return $invoice->reservation->user->student?->name ?? 'N/A';
            })
            ->addColumn('national_id', function ($invoice) {
                return $invoice->reservation->user->student?->national_id ?? 'N/A';
            })
            ->addColumn('faculty', function ($invoice)  {
                return $invoice->reservation->user->student?->faculty?->name?? 'N/A';
            })
            ->addColumn('phone', function ($invoice) {
                return $invoice->reservation->user->student?->phone ?? 'N/A';
            })
            ->addColumn('status', function ($invoice) {
                return trans($invoice->status);
            })
            ->addColumn('admin_approval', function ($invoice) {
                return trans($invoice->admin_approval);
            })
            ->make(true);
    } catch (Exception $e) {
        Log::error('Failed to fetch invoices', [
            'error' => $e->getMessage(),
            'action' => 'fetch_invoices',
            'request_data' => $request->all(),
            'admin_id' => auth()->id(),
        ]);
        return response()->json(["error" => "Failed to fetch invoices data."], 500);
    }
}

    /**
     * Fetch detailed information about a specific invoice by invoice ID.
     *
     * @param int $invoiceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchInvoice($invoiceId)
    {
        try {
            // Fetch invoice with all necessary relationships
            $invoice = Invoice::with(["reservation.user.student.faculty", "reservation.room.apartment.building", "reservation.room.apartment", "media", "details"])->findOrFail($invoiceId);

            // Get the reservation and user
            $reservation = $invoice->reservation;
            $user = $reservation->user;
            $room = $reservation->room;

            // Build student details
            $studentDetails = [
                "name" => $user->student->name ?? "N/A",
                "balance" => $user->balance . " EGP",
                "faculty" => $user->student->faculty->name ?? "N/A",
                "building" => $room->apartment->building->number ?? "N/A",
                "apartment" => $room->apartment->number ?? "N/A",
                "room" => $room->number ?? "N/A",
            ];

            // Format invoice details
            $invoiceDetails = $invoice->details
                ->map(function ($detail) {
                    return [
                        "id" => $detail->id,
                        "invoice_id" => $detail->invoice_id,
                        'status' => $detail->status,
                        "category" => $detail->category,
                        "amount" => $detail->amount,
                        "description" => $detail->description,
                    ];
                })
                ->values()
                ->all();

            // Format media for frontend
            $mediaArray = [];
            if ($invoice->media) {
                $mediaArray[] = [
                    "id" => $invoice->media->id,
                    "payment_url" => asset("storage/" . $invoice->media->path),
                    "collection" => $invoice->media->collection_name,
                    "created_at" => $invoice->media->created_at,
                    "updated_at" => $invoice->media->updated_at,
                ];
            }

            return response()->json([
                "studentDetails" => $studentDetails,
                "invoiceDetails" => $invoiceDetails,
                "media" => $mediaArray,
                "invoice_id" => $invoice->id,
                "status" => $invoice->admin_approval,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch invoice details', [
                'error' => $e->getMessage(),
                'action' => 'fetch_invoice_details',
                'invoice_id' => $invoiceId,
                'admin_id' => auth()->id(),
            ]);
            return response()->json(["error" => "Failed to fetch invoice details"], 500);
        }
    }

    /**
     * Update the status of a payment to either 'accepted' or 'rejected'.
     *
     * @param Request $request
     * @param int $invoiceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePaymentStatus(Request $request, $invoiceId)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                "status" => "required|in:accepted,rejected",
                "paidDetails" => "required_if:status,accepted|array|min:1",
                "paidDetails.*" => "required_if:status,accepted|exists:invoice_details,id",
                "overPaymentAmount" => "nullable",
                "notes" => "nullable|string|max:500",
            ]);

            // Start a database transaction
            DB::beginTransaction();

            // Find the invoice
            $invoice = Invoice::find($invoiceId);

            // Check if the invoice exists
            if (!$invoice) {
                return response()->json(["error" => "Invoice not found"], 404);
            }

            // Update the invoice status and notes
            $status = $validated["status"];
            $invoice->admin_approval = $status;
            $invoice->notes = $validated["notes"] ?? null;
            $invoice->save();

            // Handle additional logic for 'accepted' status
            if ($status == "accepted") {
                $this->markInvoiceDetailsAsPaid($validated["paidDetails"], $invoice);
                $this->updateReservationStatus($invoice);
            }
            $overPaymentAmount = $validated["overPaymentAmount"];
            if ($overPaymentAmount != null && $overPaymentAmount > 0) {
                $invoice->reservation->user->balance += $overPaymentAmount;
                $invoice->reservation->user->save();
            }

            // Commit the transaction
            DB::commit();

            // Log admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'update_payment_status',
                'description' => 'Updated payment status for invoice',
                'changes' => json_encode([
                    'invoice_id' => $invoiceId,
                    'status' => $status,
                    'paid_details' => $validated["paidDetails"],
                    'over_payment_amount' => $overPaymentAmount,
                    'notes' => $validated["notes"] ?? null,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Return success response
            return response()->json([
                "success" => true,
                "message" => "Invoice status updated successfully",
                "status" => $status,
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error
            Log::error('Failed to update payment status', [
                'error' => $e->getMessage(),
                'action' => 'update_payment_status',
                'invoice_id' => $invoiceId,
                'request_data' => $request->all(),
                'admin_id' => auth()->id(),
            ]);

            return response()->json(["error" => "Failed to update payment status."], 500);
        }
    }

    /**
     * Mark invoice details as paid.
     *
     * @param array $paidDetails
     * @param Invoice $invoice
     */
    private function markInvoiceDetailsAsPaid(array $paidDetails, Invoice $invoice)
    {
        foreach ($paidDetails as $detailId) {
            $invoiceDetail = InvoiceDetail::find($detailId);
            if ($invoiceDetail) {
                // Mark the detail as paid
                $invoiceDetail->status = "paid";
                $invoiceDetail->save();

                // Handle the 'insurance' category
                if ($invoiceDetail->category == "insurance") {
                    // Create or update insurance record
                    Insurance::updateOrCreate(
                        ["user_id" => $invoice->reservation->user->id],
                        [
                            "user_id" => $invoice->reservation->user->id,
                            "status" => "active",
                        ]
                    );
                }
            }
        }

    }


    /**
     * Update the reservation status if the invoice is accepted.
     *
     * @param Invoice $invoice
     */
    private function updateReservationStatus(Invoice $invoice)
    {
        if ($invoice->reservation && $invoice->reservation->status == "pending") {
            $invoice->reservation->status = "active";
            $invoice->reservation->save();
        }
    }
}