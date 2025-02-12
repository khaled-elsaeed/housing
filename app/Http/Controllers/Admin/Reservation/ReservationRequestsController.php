<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use App\Models\ReservationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ReservationService;
use App\Models\Room;


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
     * Display a listing of reservation requests
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            return view('admin.reservation.requests');
        } catch (Exception $e) {
            Log::error('Error loading reservation requests page: ' . $e->getMessage());
            return back()->with('error', 'Failed to load reservation requests page');
        }
    }

    /**
     * Fetch reservation requests for DataTable
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
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('academicTerm', function($termQuery) use ($search) {
                        $termQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('period_type', 'like', "%{$search}%")
                    ->orWhere('period_duration', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
                });
            }
    
            return DataTables::of($query)
               
                ->editColumn('user.name', function($request) {
                    return $request->user?->name ?? 'N/A';
                })
                ->editColumn('period_type', function($request) {
                    return Trans($request->period_type);
                })
                ->editColumn('period_duration', function ($request) {
                    if ($request->period_type === "long" && $request->academicTerm) {
                        return Trans($request->academicTerm->semester . " Term ( " . ($request->academicTerm->name) . " " . $request->academicTerm->academic_year . " )");
                    }
                    return $request->period_duration;
                })
                
                ->editColumn('requested_at', function ($request) {
                    return \Carbon\Carbon::parse($request->created_at)->format('F j, Y g:i A');
                })
                

                ->editColumn('status', function($request) {
                    return $request->status ?? 'pending';
                
                })
                
                ->editColumn('actions', function ($request) {
                    return $request->status === 'accepted' 
                        ? \Carbon\Carbon::parse($request->updated_at)->format('d M Y, h:i A') 
                        : 'pending'; 
                })
                
                ->make(true);

        } catch (Exception $e) {
            Log::error('Error fetching reservation requests: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch reservation requests'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get summary statistics for reservation requests
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
                'rejected' => ReservationRequest::where('status', 'rejected')->count()
            ];

            return response()->json($summary);
        } catch (Exception $e) {
            Log::error('Error getting reservation requests summary: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to get summary'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Accept a reservation request
     *
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
                'message' => 'Only pending requests can be accepted'
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
                'message' => 'Failed to create reservation'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Update request status to accepted
        $reservationRequest->status = 'accepted';

        $reservationRequest->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Reservation request accepted successfully',
            'data' => $reservationRequest
        ], Response::HTTP_OK);

    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error accepting reservation request: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to accept reservation request'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    /**
     * Reject a reservation request
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
            
            if ($reservationRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be rejected'
                ], Response::HTTP_BAD_REQUEST);
            }

            $reservationRequest->status = 'rejected';
            $reservationRequest->rejection_reason = $request->input('reason');
            $reservationRequest->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation request rejected successfully',
                'data' => $reservationRequest
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting reservation request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject reservation request'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Auto reserve all pending requests
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoReserve()
{
    try {
        Log::info("Starting auto reservation process");
        
        // Process synchronously for immediate feedback
        $this->reservationService->automateReservationProcess(false);

        return response()->json([
            'success' => true,
            'message' => 'Auto reservation completed successfully'
        ]);

    } catch (Exception $e) {
        Log::error('Auto reservation failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to process auto reservation: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
}