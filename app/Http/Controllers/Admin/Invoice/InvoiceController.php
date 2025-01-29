<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Insurance;
use App\Models\InvoiceDetail;
use Yajra\DataTables\DataTables;
use App\Exports\Invoices\InvoicesExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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
            return view("admin.invoices.index");
        } catch (\Exception $e) {
            Log::error("Error displaying Invoice page", [
                "exception" => $e->getMessage(),
                "stack_trace" => $e->getTraceAsString(),
            ]);
            return response()->view("errors.500");
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
            $query = Invoice::with([
                "reservation" => function ($query) {
                    $query->with(["user.student"]);
                },
            ]);

            // Gender filtering
            if ($request->filled("gender")) {
                $gender = $request->get("gender");
                $query->whereHas("reservation.user.student", function ($query) use ($gender) {
                    $query->where("gender", $gender);
                });
            }

            // Custom search filtering
            if ($request->filled("customSearch")) {
                $searchTerm = $request->get("customSearch");
                $query->where(function ($query) use ($searchTerm, $currentLang) {
                    $query->whereHas("reservation.user.student", function ($query) use ($searchTerm, $currentLang) {
                        $query
                            ->where("name_" . ($currentLang == "ar" ? "ar" : "en"), "like", "%$searchTerm%")
                            ->orWhere("national_id", "like", "%$searchTerm%")
                            ->orWhere("mobile", "like", "%$searchTerm%");
                    });
                });
            }

            // Filter invoices by approval status if provided
            if ($request->filled("admin_approval")) {
                $approvalStatus = $request->get("admin_approval");
                $query->where("admin_approval", $approvalStatus);
            }

            // Order by payment status and admin approval
            $query->orderByRaw("
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

            // Count total and filtered records
            $totalRecords = $query->count("invoices.id");
            $filteredRecords = $query->count("invoices.id");

            // Paginate
            $invoices = $query
                ->select("invoices.*") // Select only invoice fields
                ->skip($request->get("start", 0))
                ->take($request->get("length", 10))
                ->get();

            return response()->json([
                "draw" => $request->get("draw"),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $invoices->map(function ($invoice) use ($currentLang) {
                    $student = $invoice->reservation->user->student ?? null;
                    $faculty = $student->faculty ?? null;

                    return [
                        "invoice_id" => $invoice->id,
                        "name" => $student ? $student->{"name_" . ($currentLang == "ar" ? "ar" : "en")} : "N/A",
                        "national_id" => $student ? $student->national_id : "N/A",
                        "faculty" => $faculty ? $faculty->{"name_" . ($currentLang == "ar" ? "ar" : "en")} : "N/A",
                        "mobile" => $student ? $student->mobile : "N/A",
                        "invoice_status" => $invoice->status,
                        "admin_approval" => $invoice->admin_approval,
                        "actions" => '<button type="button" class="btn btn-round btn-info-rgba" data-invoice-id="' . $invoice->id . '" id="details-btn" title="More Details"><i class="feather icon-info"></i></button>',
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching invoices data: " . $e->getMessage(), [
                "exception" => $e,
            ]);
            return response()->json(["error" => "Failed to fetch invoices data."], 500);
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
    public function fetchInvoice($invoiceId){
        try {
            // Fetch invoice with all necessary relationships
            $invoice = Invoice::with(["reservation.user.student.faculty", "reservation.room.apartment.building", "reservation.room.apartment", "media", "details"])->findOrFail($invoiceId);

            // Get the reservation and user
            $reservation = $invoice->reservation;
            $user = $reservation->user;
            $room = $reservation->room;

            // Build student details
            $studentDetails = [
                "name" => $user->student->name_en ?? "N/A",
                "balance" => $user->balance . " EGP",
                "faculty" => $user->student->faculty->name_en ?? "N/A",
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
                "media" => $mediaArray, // Now it's an array as expected by frontend
                "invoice_id" => $invoice->id, // Added for the status buttons
                "status" => $invoice->admin_approval,
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error("Invoice not found:", ["invoice_id" => $invoiceId]);
            return response()->json(["error" => "Invoice not found"], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching invoice details:", [
                "invoice_id" => $invoiceId,
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
            return response()->json(["error" => "Failed to fetch invoice details"], 500);
        }
    }

    /**
     * Update the status of a payment to either 'accepted' or 'rejected'.
     *
     * @param Request $request
     * @param int $paymentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePaymentStatus(Request $request, $invoiceId){
        try {
            // Validate the request
            $validated = $request->validate([
                "status" => "required|in:accepted,rejected", // Ensure status is valid
                "paidDetails" => "required_if:status,accepted|array|min:1", // Ensure paidDetails is provided if status is 'accepted'
                "paidDetails.*" => "required_if:status,accepted|exists:invoice_details,id", // Ensure each paid detail exists in the database
                "overPaymentAmount" => "nullable",
                "notes" => "nullable|string|max:500", // Add validation for notes
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
            $invoice->notes = $validated["notes"] ?? null; // Save the notes
            $invoice->save();

            // Handle additional logic for 'accepted' status
            if ($status == "accepted") {
                $this->markInvoiceDetailsAsPaid($validated["paidDetails"], $invoice);
                $this->updateReservationStatus($invoice);
            }
            $overPaymentAmount = $validated["overPaymentAmount"];
            if ($overPaymentAmount != null && $overPaymentAmount > 0) {
                $invoice->reservation->user->balance += $overPaymentAmount;
                $invoice->reservation->user->save(); // Also need to save the change
            }

            // Commit the transaction
            DB::commit();

            // Return success response
            return response()->json([
                "success" => true,
                "message" => "Invoice status updated successfully",
                "status" => $status,
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error and return a failure response
            Log::error("Error updating payment status: " . $e->getMessage(), [
                "exception" => $e,
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
    private function markInvoiceDetailsAsPaid(array $paidDetails, Invoice $invoice){
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

        // Update the overall invoice paid status
        $this->updateInvoicePaidStatus($invoice);
    }

    /**
     * Update the overall paid status of the invoice.
     *
     * @param Invoice $invoice
     */
    private function updateInvoicePaidStatus(Invoice $invoice){
        $totalInvoiceDetails = $invoice->details->count();
        $paidInvoiceDetails = $invoice->details->where("status", "paid")->count();

        if ($totalInvoiceDetails == $paidInvoiceDetails) {
            $invoice->paid_status = "full_paid";
        } elseif ($totalInvoiceDetails > $paidInvoiceDetails && $paidInvoiceDetails != 0) {
            $invoice->paid_status = "partial_paid";
        }

        $invoice->save();
    }

    /**
     * Update the reservation status if the invoice is accepted.
     *
     * @param Invoice $invoice
     */
    private function updateReservationStatus(Invoice $invoice){
        if ($invoice->reservation && $invoice->reservation->status == "pending") {
            $invoice->reservation->status = "active";
            $invoice->reservation->save();
        }
    }
}
