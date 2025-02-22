<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StaffMaintenanceController extends Controller
{
    /**
     * Display the maintenance dashboard for staff.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $staffId = auth()->id();
            
            // Get maintenance request statistics for the logged-in staff
            $statistics = $this->getMaintenanceStatistics($staffId);

            return view('staff.maintenance', $statistics);
        } catch (Exception $e) {
            Log::error('Failed to load staff maintenance dashboard', [
                'error' => $e->getMessage(),
                'staff_id' => auth()->id()
            ]);
            return redirect()->back()->with('error', __('Failed to load the staff maintenance dashboard.'));
        }
    }

    /**
     * Accept a maintenance request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept($id)
    {
        try {
            $staffId = auth()->id();
            $maintenanceRequest = MaintenanceRequest::findOrFail($id);

            // Verify the request is assigned to this staff member
            if ($maintenanceRequest->assigned_to !== $staffId) {
                return response()->json([
                    'success' => false,
                    'message' => __('You are not authorized to perform this action.')
                ], 403);
            }

            // Update request status
            $maintenanceRequest->update([
                'status' => 'in_progress',
                'staff_accepted_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('The maintenance request has been accepted successfully.'),
                'data' => $maintenanceRequest
            ]);
        } catch (Exception $e) {
            Log::error('Failed to accept maintenance request', [
                'error' => $e->getMessage(),
                'request_id' => $id,
                'staff_id' => auth()->id()
            ]);
            return response()->json(['error' => __('An error occurred while accepting the maintenance request.')], 500);
        }
    }

    /**
     * Complete a maintenance request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete($id)
    {
        try {
            $staffId = auth()->id();
            $maintenanceRequest = MaintenanceRequest::findOrFail($id);

            // Verify the request is assigned to this staff member
            if ($maintenanceRequest->assigned_to !== $staffId) {
                return response()->json([
                    'success' => false,
                    'message' => __('You are not authorized to perform this action.')
                ], 403);
            }

            // Update request status
            $maintenanceRequest->update([
                'status' => 'completed',
                'completed_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('The maintenance request has been accepted successfully.'),
                'data' => $maintenanceRequest
            ]);
        } catch (Exception $e) {
            Log::error('Failed to accept maintenance request', [
                'error' => $e->getMessage(),
                'request_id' => $id,
                'staff_id' => auth()->id()
            ]);
            return response()->json(['error' => __('An error occurred while accepting the maintenance request.')], 500);
        }
    }

    /**
     * Fetch maintenance requests for DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchRequests(Request $request)
    {
        try {
            $staffId = auth()->id();
            
            $query = MaintenanceRequest::with([
                'room.reservation.user.student',
                'media',
                'category',
                'problems'
            ])
            ->where('assigned_to', $staffId)
            ->select('maintenance_requests.*')
            ->orderByRaw("
                CASE 
                    WHEN status = 'assigned' THEN 1
                    WHEN status = 'in_progress' THEN 2
                    WHEN status = 'completed' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('updated_at','ASC');

            // Apply filters
            $this->applyRequestFilters($query, $request);

            return DataTables::of($query)
                ->addColumn('resident_name', fn ($request) => 
                    $request->room->reservation->user->name ?? 'N/A')
                ->addColumn('resident_location', fn ($request) => 
                    $this->formatResidentLocation($request))
                ->addColumn('resident_phone', fn ($request) => 
                    $request->room->reservation->user->student->phone ?? 'N/A')
                ->addColumn('category', fn ($request) => 
                    $request->category->name_en ?? 'N/A')
                ->addColumn('problems', fn ($request) => 
                    $request->problems->pluck('name')->implode(', '))
                ->addColumn('has_photos', fn ($request) => 
                    $request->media->isNotEmpty() ? 'Yes' : 'No')
                ->addColumn('photos', fn ($request) => 
                    $this->formatPhotosHtml($request))
                ->rawColumns(['photos'])
                ->make(true);
        } catch (Exception $e) {
            Log::error('Failed to fetch maintenance requests', [
                'error' => $e->getMessage(),
                'staff_id' => auth()->id()
            ]);
            return response()->json(['error' => __('An error occurred while fetching maintenance requests.')], 500);
        }
    }

    /**
     * Get maintenance statistics for staff dashboard.
     *
     * @param int $staffId
     * @return array
     */
    private function getMaintenanceStatistics($staffId)
    {
        $baseQuery = MaintenanceRequest::where('assigned_to', $staffId);

        return [
            'totalRequests' => $baseQuery->count(),
            'assignedRequests' => $baseQuery->where('status', 'assigned')->count(),
            'inProgressRequests' => $baseQuery->where('status', 'in_progress')->count(),
            'completedRequests' => $baseQuery->where('status', 'completed')->count()
        ];
    }

    /**
     * Apply filters to the maintenance requests query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     */
    private function applyRequestFilters($query, Request $request)
    {
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Custom search filtering
        if ($request->filled('customSearch')) {
            $searchTerm = $request->get('customSearch');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('room.reservation.user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('phone', 'like', "%{$searchTerm}%");
                })
                ->orWhere('title', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
    }

    /**
     * Format resident location string.
     *
     * @param MaintenanceRequest $request
     * @return string
     */
    private function formatResidentLocation($request)
    {
        return trans('resident_location', [
            'room' => $request->room->number ?? 'N/A',
            'apartment' => $request->room->apartment->number ?? 'N/A',
            'building' => $request->room->apartment->building->number ?? 'N/A'
        ]);
    }

    /**
     * Format photos HTML for DataTables.
     *
     * @param MaintenanceRequest $request
     * @return string
     */
    private function formatPhotosHtml($request)
    {
        if ($request->media->isEmpty()) {
            return '<span class="text-muted">No photos</span>';
        }

        $buttons = $request->media->map(function ($media, $index) {
            $url = asset($media->path);
            return sprintf(
                '<a href="%s" target="_blank" class="btn btn-s btn-outline-primary rounded-circle p-0 me-1" 
                    data-bs-toggle="tooltip" title="Photo %d" style="width: 32px; height: 32px; line-height: 32px;">
                    <i class="fa fa-image" style="font-size: 12px;"></i>
                </a>',
                $url,
                $index + 1
            );
        })->implode('');

        return '<div class="d-flex flex-nowrap">' . $buttons . '</div>';
    }
}