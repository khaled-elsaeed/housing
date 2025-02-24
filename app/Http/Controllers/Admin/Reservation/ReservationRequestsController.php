<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use App\Models\{ReservationRequest, Room, AdminAction};
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Auth};
use Yajra\DataTables\Facades\DataTables;
use App\Exceptions\BusinessRuleException;
use Carbon\carbon;
use Exception;

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
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('admin.reservation.requests');
        } catch (Exception $e) {
            logError('Failed to load reservation requests page', 'show_reservation_requests_page', $e);
            return response()->view('errors.500');
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
            $query = ReservationRequest::with(['user.student', 'academicTerm'])
                ->select('reservation_requests.*')
                ->orderByRaw("
                    CASE status 
                        WHEN 'pending' THEN 1 
                        WHEN 'accepted' THEN 2 
                        WHEN 'rejected' THEN 3 
                        ELSE 4 
                    END
                ")
                ->orderBy('created_at', 'desc');

            if ($request->filled('customSearch')) {
                $search = $request->input('customSearch');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user.student', function ($userQuery) use ($search) {
                        $userQuery->where('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%")
                            ->orWhere('national_id', 'like', "%{$search}%");
                    });
                });
            }

            return DataTables::of($query)
                ->editColumn('name', fn($request) => $request->user?->name ?? 'N/A')
                ->editColumn('period_type', fn($request) => trans($request->period_type))
                ->editColumn('period_duration', fn($request) => 
                    $request->period_type === 'long' && $request->academicTerm
                        ? trans("{$request->academicTerm->semester} Term ( {$request->academicTerm->name} {$request->academicTerm->academic_year} )")
                        : ($request->start_date . trans(' To ') . $request->end_date)
                )
                ->editColumn('requested_at', fn($request) => 
                    carbon::parse($request->created_at)->format('F j, Y g:i A')
                )
                ->editColumn('status', fn($request) => $request->status ?? 'pending')
                ->editColumn('actions', fn($request) => 
                    $request->status !== 'pending'
                        ? carbon::parse($request->updated_at)->format('d M Y, h:i A')
                        : 'pending'
                )
                ->make(true);
        } catch (Exception $e) {
            logError('Failed to fetch reservation requests', 'fetch_reservation_requests', $e);
            return errorResponse('Failed to fetch reservation requests.', 500);
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

            return successResponse('Reservation request summary retrieved successfully', null, $summary);
        } catch (Exception $e) {
            logError('Failed to fetch reservation requests stats', 'fetch_reservation_requests_stats', $e);
            return errorResponse('Failed to fetch reservation requests stats.', 500);
        }
    }

    /**
     * Accept a reservation request.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Request $request, int $id)
    {
        try {

            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
            ]);

            return DB::transaction(function () use ($request, $id, $validated) {
                $reservationRequest = ReservationRequest::findOrFail($id);
                $room = Room::findOrFail($validated['room_id']);

                if ($reservationRequest->status !== 'pending') {
                    throw new BusinessRuleException('Only pending requests can be accepted');
                }

                $this->reservationService->newReservation($reservationRequest, $room);


                $reservationRequest->status = 'accepted';
                $reservationRequest->save();

                AdminAction::create([
                    'admin_id' => Auth::id(),
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

                userActivity($reservationRequest->user_id, 'reservation_accepted', 'Reservation request accepted by administration');

                return successResponse('Reservation request accepted successfully!', null, ['data' => $reservationRequest]);
            });
        } catch (BusinessRuleException $e) {
            return errorResponse(trans($e->getMessage()), 400);
        } catch (Exception $e) {
            logError('Failed to accept reservation request', 'accept_reservation_request', $e);
            return errorResponse('Failed to accept reservation request', 500);
        }
    }

    /**
     * Reject a reservation request.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(Request $request, int $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $reservationRequest = ReservationRequest::findOrFail($id);

                if ($reservationRequest->status !== 'pending') {
                    throw new BusinessRuleException('Only pending requests can be rejected');
                }

                $reservationRequest->status = 'rejected';
                $reservationRequest->rejection_reason = $request->input('reason');
                $reservationRequest->save();

                AdminAction::create([
                    'admin_id' => Auth::id(),
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

                userActivity($reservationRequest->user_id, 'reservation_rejected', 'Reservation request rejected by admin');

                return successResponse('Reservation request rejected successfully!', null, ['data' => $reservationRequest]);
            });
        } catch (BusinessRuleException $e) {
            return errorResponse(trans($e->getMessage()), 400);
        } catch (Exception $e) {
            logError('Failed to reject reservation request', 'reject_reservation_request', $e);
            return errorResponse('Failed to reject reservation request', 500);
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
            $this->reservationService->automateReservationProcess();

            AdminAction::create([
                'admin_id' => Auth::id(),
                'action' => 'auto_reserve',
                'description' => 'Auto reserved all pending reservation requests',
                'changes' => json_encode([]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            userActivity(Auth::id(), 'auto_reserve', 'Initiated auto reservation process');

            return successResponse('Auto reservation completed');
        } catch (Exception $e) {
            logError('Failed to process auto reservation', 'auto_reserve', $e);
            return errorResponse('Failed to process auto reservation', 500);
        }
    }
}