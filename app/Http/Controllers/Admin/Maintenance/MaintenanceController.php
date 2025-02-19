<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Insurance;
use App\Models\InvoiceDetail;
use App\Models\AdminAction; // Assuming you have a model for admin action logs
use App\Exports\Invoices\InvoicesExport;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            'user_id' => auth()->id(), // Assuming the user is logged in
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
        $maintenanceRequest->update(['status' => 'assigned']);

        $assignment = $maintenanceRequest->assignments()->create([
            'staff_id' => $request->staff_id,
            'assigned_at' => now(),
        ]);

        return response()->json([
            'message' => 'Request assigned successfully!',
            'data' => $assignment,
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

            $query = MaintenanceRequest::with(['room.reservation.user'])
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
                $query->where(function ($q) use ($searchTerm, $currentLang) {
                    $q->whereHas('room.reservation.user', function ($userQuery) use ($searchTerm, $currentLang) {
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
                        'building' => $buildingNumber
                    ]);
                })
                
                ->addColumn('resident_phone', function ($request) {
                    return $request->room->reservation->user->phone ?? 'N/A';
                })
                ->addColumn('category', function ($request) {
                    return $request->category->name;
                })
                ->addColumn('problems', function ($request) {
                    return collect($request->problems);
                })
                ->addColumn('status', function ($request) {
                    return trans($request->status);
                })
                ->addColumn('assigned_staff', function ($request) {
                    return $request->assignments?->first()?->staff->name ?? 'N/A';
                })
                ->addColumn('created_at', function ($request) {
                    return $request->created_at->format('Y-m-d H:i:s');
                })
                
                ->make(true);
        } catch (Exception $e) {
            Log::error('Failed to fetch maintenance requests', [
                'error' => $e->getMessage(),
                'action' => 'fetch_maintenance_requests',
                'request_data' => $request->all(),
                'admin_id' => auth()->id(),
            ]);
            return response()->json(["error" => "Failed to fetch maintenance requests data."], 500);
        }
    }



public function fetchStaff(Request $request)
{
    // Get category name from request
    $categoryName = $request->input('category');

    // Define category-to-role mapping
    $categoryRoles = [
        'Water and Sanitary Issues' => ['plumber'],
        'Electrical Issues' => ['electrician'],
        'General Housing Issues' => ['carpenter', 'plumber', 'electrician'],
    ];

    // Validate if category exists
    if (!isset($categoryRoles[$categoryName])) {
        return response()->json(['error' => 'Invalid category name'], 400);
    }

    // Get the roles associated with the category
    $roles = $categoryRoles[$categoryName];

   // Fetch users who have "technician" as the main role AND match the category roles
$technicians = User::whereHas('roles', fn($query) => $query->where('name', 'technician'))
->whereHas('roles', fn($query) => $query->whereIn('name', $roles))
->with('roles')
->get();

$technicians = $technicians->map(function ($tech) {
$tech->name = $tech->name; // Assuming 'name' is a property
return $tech; // Return the modified object
});

    
    return response()->json(["staff"=>$technicians]);
}


}