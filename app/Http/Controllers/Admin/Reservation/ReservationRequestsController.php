<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use App\Models\ReservationRequest;
use App\Models\Room;
use App\Models\AdminAction; // Assuming you have a model for admin action logs
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ReservationService;

class ReservationRequestsController extends Controller
{
    private $reservationService;

    /**
     * Constructor to inject the ReservationService.
     *
     * @param ReservationService $reservationService
     */
    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Display a listing of reservation requests.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            return view('admin.reservation.requests');
        } catch (Exception $e) {
            Log::error('Failed to load reservation requests page', [
                'error' => $e->getMessage(),
                'action' => 'show_reservation_requests_page',
                'admin_id' => auth()->id(), // Log the admin performing the action
            ]);
            return response()->view("errors.500");
        }
    }

    /**
     * Fetch reservation requests for DataTable.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch(Request $request)
    {
        try {
            $query = ReservationRequest::with(['user', 'academicTerm'])
                ->select('reservation_requests.*');

            if ($request->filled('customSearch')) {
                $search = $request->input('customSearch');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('academicTerm', function ($termQuery) use ($search) {
                        $termQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('period_type', 'like', "%{$search}%")
                    ->orWhere('period_duration', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
                });
            }

            return DataTables::of($query)
                ->editColumn('user.name', function ($request) {
                    return $request->user?->name ?? 'N/A';
                })
                ->editColumn('period_type', function ($request) {
                    return trans($request->period_type);
                })
                ->editColumn('period_duration', function ($request) {
                    if ($request->period_type === "long" && $request->academicTerm) {
                        return trans($request->academicTerm->semester . " Term ( " . ($request->academicTerm->name) . " " . $request->academicTerm->academic_year . " )");
                    }
                    return $request->period_duration;
                })
                ->editColumn('requested_at', function ($request) {
                    return \Carbon\Carbon::parse($request->created_at)->format('F j, Y g:i A');
                })
                ->editColumn('status', function ($request) {
                    return $request->status ?? 'pending';
                })
                ->editColumn('actions', function ($request) {
                    return $request->status === 'accepted'
                        ? \Carbon\Carbon::parse($request->updated_at)->format('d M Y, h:i A')
                        : 'pending';
                })
                ->make(true);
        } catch (Exception $e) {
            Log::error('Failed to fetch reservation requests', [
                'error' => $e->getMessage(),
                'action' => 'fetch_reservation_requests',
                'admin_id' => auth()->id(),
            ]);
            return response()->json(["error" => "Failed to fetch reservation requests."], 500);
        }
    }

    /**
     * Get summary statistics for reservation requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSummary()
    {
        try {
            $summary = [
                'total' => ReservationRequest::count(),
                'pending' => ReservationRequest::where('status', 'pending')->count(),
                'accepted' => ReservationRequest::where('status', 'accepted')->count(),
                'rejected' => ReservationRequest::where('status', 'rejected')->count(),
            ];

            return response()->json($summary);
        } catch (Exception $e) {
            Log::error('Failed to fetch reservation requests stats', [
                'error' => $e->getMessage(),
                'action' => 'fetch_reservation_requests_stats',
                'admin_id' => auth()->id(),
            ]);
            return response()->json(["error" => "Failed to fetch reservation requests stats."], 500);
        }
    }

    /**
     * Accept a reservation request.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $id)
    {
        try {
            // Validate that room_id is provided
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
            ]);

            DB::beginTransaction();

            // Find the reservation request
            $reservationRequest = ReservationRequest::findOrFail($id);
            $room = Room::findOrFail($validated['room_id']);

            // Ensure the request is still pending
            if ($reservationRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be accepted',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create reservation using the reservation service
            $reservation = $this->reservationService->createReservation(
                reservationRequester: $reservationRequest->user,
                selectedRoom: $room,
                reservationPeriodType: $reservationRequest->period_type,
                academicTermId: $reservationRequest->academic_term_id,
                status: "pending"
            );

            if (!$reservation) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create reservation',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Update request status to accepted
            $reservationRequest->status = 'accepted';
            $reservationRequest->save();

            // Log admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'accept_reservation_request',
                'description' => 'Accepted a reservation request',
                'changes' => json_encode([
                    'reservation_request_id' => $reservationRequest->id,
                    'room_id' => $room->id,
                    'status' => 'accepted',
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation request accepted successfully',
                'data' => $reservationRequest,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to accept reservation request', [
                'error' => $e->getMessage(),
                'action' => 'accept_reservation_request',
                'reservation_request_id' => $id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to accept reservation request',
            ], 500);
        }
    }

    /**
     * Reject a reservation request.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $reservationRequest = ReservationRequest::findOrFail($id);

            // Ensure the request is still pending
            if ($reservationRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be rejected',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Update request status to rejected
            $reservationRequest->status = 'rejected';
            $reservationRequest->rejection_reason = $request->input('reason');
            $reservationRequest->save();

            // Log admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'reject_reservation_request',
                'description' => 'Rejected a reservation request',
                'changes' => json_encode([
                    'reservation_request_id' => $reservationRequest->id,
                    'status' => 'rejected',
                    'rejection_reason' => $request->input('reason'),
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation request rejected successfully',
                'data' => $reservationRequest,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject reservation request', [
                'error' => $e->getMessage(),
                'action' => 'reject_reservation_request',
                'reservation_request_id' => $id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to reject reservation request',
            ], 500);
        }
    }

    /**
     * Auto reserve all pending requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoReserve()
    {
        try {
            Log::info("Starting auto reservation process");

            // Process synchronously for immediate feedback
            $this->reservationService->automateReservationProcess();

            // Log admin action
            AdminAction::create([
                'admin_id' => auth()->id(),
                'action' => 'auto_reserve',
                'description' => 'Auto reserved all pending reservation requests',
                'changes' => json_encode([]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Auto reservation completed',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to process auto reservation', [
                'error' => $e->getMessage(),
                'action' => 'auto_reserve',
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to process auto reservation',
            ], 500);
        }
    }
}