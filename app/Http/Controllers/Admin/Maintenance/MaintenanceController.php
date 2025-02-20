<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    /**
     * Display the maintenance requests view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Total Maintenance Requests
        $totalMaintenanceRequests = MaintenanceRequest::count();

        // Pending Maintenance Requests
        $pendingMaintenanceRequestsCount = MaintenanceRequest::where('status', 'pending')->count();

        // Completed Maintenance Requests
        $completedMaintenanceRequestsCount = MaintenanceRequest::where('status', 'completed')->count();

        // Rejected Maintenance Requests
        $rejectedMaintenanceRequestsCount = MaintenanceRequest::where('status', 'rejected')->count();

        // Gender Breakdown for Total Requests
        $maleTotalCount = MaintenanceRequest::whereHas('room.reservation.user', function ($query) {
            $query->where('gender', 'male');
        })->count();

        $femaleTotalCount = MaintenanceRequest::whereHas('room.reservation.user', function ($query) {
            $query->where('gender', 'female');
        })->count();

        // Gender Breakdown for Pending Requests
        $malePendingCount = MaintenanceRequest::where('status', 'pending')
            ->whereHas('room.reservation.user', function ($query) {
                $query->where('gender', 'male');
            })->count();

        $femalePendingCount = MaintenanceRequest::where('status', 'pending')
            ->whereHas('room.reservation.user', function ($query) {
                $query->where('gender', 'female');
            })->count();

        // Gender Breakdown for Completed Requests
        $maleCompletedCount = MaintenanceRequest::where('status', 'completed')
            ->whereHas('room.reservation.user', function ($query) {
                $query->where('gender', 'male');
            })->count();

        $femaleCompletedCount = MaintenanceRequest::where('status', 'completed')
            ->whereHas('room.reservation.user', function ($query) {
                $query->where('gender', 'female');
            })->count();

        // Gender Breakdown for Rejected Requests
        $maleRejectedCount = MaintenanceRequest::where('status', 'rejected')
            ->whereHas('room.reservation.user', function ($query) {
                $query->where('gender', 'male');
            })->count();

        $femaleRejectedCount = MaintenanceRequest::where('status', 'rejected')
            ->whereHas('room.reservation.user', function ($query) {
                $query->where('gender', 'female');
            })->count();

        return view('admin.maintenance.index', [
            'totalMaintenanceRequests' => $totalMaintenanceRequests,
            'pendingMaintenanceRequestsCount' => $pendingMaintenanceRequestsCount,
            'completedMaintenanceRequestsCount' => $completedMaintenanceRequestsCount,
            'rejectedMaintenanceRequestsCount' => $rejectedMaintenanceRequestsCount,
            'maleTotalCount' => $maleTotalCount,
            'femaleTotalCount' => $femaleTotalCount,
            'malePendingCount' => $malePendingCount,
            'femalePendingCount' => $femalePendingCount,
            'maleCompletedCount' => $maleCompletedCount,
            'femaleCompletedCount' => $femaleCompletedCount,
            'maleRejectedCount' => $maleRejectedCount,
            'femaleRejectedCount' => $femaleRejectedCount,
        ]);
    }

    /**
     * Store a new maintenance request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $maintenanceRequest = MaintenanceRequest::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Maintenance request created successfully!',
            'data' => $maintenanceRequest,
        ], 201);
    }

    /**
     * Assign a maintenance request to staff.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);

        $maintenanceRequest = MaintenanceRequest::findOrFail($id);

        $maintenanceRequest->update([
            'status' => 'assigned',
            'assigned_to' => $request->staff_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request assigned successfully!',
            'data' => $maintenanceRequest,
        ]);
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
        $currentLang = App::getLocale();

        $query = MaintenanceRequest::with(['room.reservation.user.student', 'media'])
            ->select('maintenance_requests.*')
            ->orderByRaw("
                CASE 
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'assigned' THEN 2
                    WHEN status = 'in_progress' THEN 3
                    ELSE 4
                END,
                created_at DESC
            ");

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            $query->where('status', $status);
        }

        // Custom search filtering
        if ($request->filled('customSearch')) {
            $searchTerm = $request->get('customSearch');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('room.reservation.user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%")
                        ->orWhere('phone', 'like', "%{$searchTerm}%");
                })
                ->orWhere('title', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('resident_name', function ($request) {
                return $request->room->reservation->user->name ?? 'N/A';
            })
            ->addColumn('resident_location', function ($request) {
                $roomNumber = $request->room->number ?? 'N/A';
                $apartmentNumber = $request->room->apartment->number ?? 'N/A';
                $buildingNumber = $request->room->apartment->building->number ?? 'N/A';

                return trans('resident_location', [
                    'room' => $roomNumber,
                    'apartment' => $apartmentNumber,
                    'building' => $buildingNumber,
                ]);
            })
            ->addColumn('resident_phone', function ($request) {
                return $request->room->reservation->user->student->phone ?? 'N/A';
            })
            ->addColumn('category', function ($request) {
                return $request->category->name ?? 'N/A';
            })
            ->addColumn('problems', function ($request) {
                return collect($request->problems);
            })
            ->addColumn('status', function ($request) {
                return trans($request->status);
            })
            ->addColumn('assigned_staff', function ($request) {
                return $request->assignedTo->name ?? 'N/A';
            })
            ->addColumn('created_at', function ($request) {
                return $request->created_at;
            })
            ->addColumn('has_photos', function ($request) {
                // Check if the request has any media (photos)
                return $request->media->isNotEmpty() ? 'Yes' : 'No';
            })
            ->addColumn('photos', function ($request) {
                // Generate buttons for each photo
                if ($request->media->isNotEmpty()) {
                    $buttons = $request->media->map(function ($media) {
                        $url = asset('storage/' . $media->path); // Adjust the path as needed
                        return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-primary me-2">View Photo</a>';
                    })->implode('');

                    return $buttons;
                }

                return 'No photos';
            })
            ->rawColumns(['photos']) // Allow HTML in the photos column
            ->make(true);
    } catch (Exception $e) {
        Log::error('Failed to fetch maintenance requests', [
            'error' => $e->getMessage(),
            'action' => 'fetch_maintenance_requests',
            'request_data' => $request->all(),
            'admin_id' => auth()->id(),
        ]);
        return response()->json(['error' => 'Failed to fetch maintenance requests data.'], 500);
    }
}

    /**
     * Fetch staff based on category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchStaff(Request $request)
{
    $categoryName = $request->input('category');
    $categoryRoles = [
        'Water and Sanitary Issues' => ['plumber'],
        'Electrical Issues' => ['electrician'],
        'General Housing Issues' => ['carpenter', 'plumber', 'electrician'],
    ];
    
    if (!isset($categoryRoles[$categoryName])) {
        return response()->json(['error' => 'Invalid category name'], 400);
    }
    
    $roles = $categoryRoles[$categoryName];
    $technicians = User::whereHas('roles', fn($query) => $query->where('name', 'technician'))
        ->whereHas('roles', fn($query) => $query->whereIn('name', $roles))
        ->with('roles')
        ->get();
    
    $technicians = $technicians->map(function($technician) {
        $technician->name = $technician->name;
        return $technician;
    });
    
    return response()->json(['staff' => $technicians]);
}
}