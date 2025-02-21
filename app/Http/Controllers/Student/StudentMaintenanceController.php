<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceProblem;
use App\Models\MaintenanceRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Contracts\UploadServiceContract;

use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class StudentMaintenanceController extends Controller
{
    public function __construct(private UploadServiceContract $uploadService)
    {
    }
    /**
 * Display the maintenance request index page.
 *
 * @return \Illuminate\View\View
 */
public function index()
{
    try {
        // Fetch paginated maintenance requests for the authenticated user
        $requests = MaintenanceRequest::whereHas('room.reservation', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['category', 'room.reservation.user'])
            ->orderBy('created_at', 'asc')
            ->paginate(3); // 10 requests per page

        return view('student.maintenance.index', compact('requests'));
    } catch (Exception $e) {
        Log::error('Failed to load resident maintenance page', [
            'error' => $e->getMessage(),
            'action' => 'show_resident_maintenance_page',
            'user_id' => auth()->id(),
        ]);
        return response()->view('errors.500');
    }
}

    /**
     * Display the maintenance request creation page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $categories = MaintenanceCategory::all();
            return view('student.maintenance.create', compact('categories'));
        } catch (Exception $e) {
            Log::error('Failed to load maintenance creation page', [
                'action' => 'load_maintenance_create_page',
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to load the page. Please try again.');
        }
    }

    /**
 * Store a newly created maintenance request.
 *
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function store(Request $request)
{
    try {
        // Validate the request
        $validatedData = $request->validate([
            'category_id' => 'required|exists:maintenance_categories,id',
            'problems' => 'required|array',
            'problems.*' => 'exists:maintenance_problems,id',
            'description' => 'required|string|max:1000',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB max per file
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Retrieve the room_id from the user's reservation
        $reservation = $user->reservations()->latest()->first();
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => trans('You do not have an active reservation.'),
            ], 400);
        }

        $room_id = $reservation->room_id;

        // Create a maintenance request
        $maintenanceRequest = MaintenanceRequest::create([
            'room_id' => $room_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium', // Default priority
            'status' => 'pending',
        ]);

        // Attach multiple problems to the request
        $maintenanceRequest->problems()->attach($request->problems);

        // Handle file uploads using UploadService
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Use UploadService to handle the file upload
                $this->uploadService->upload($photo, 'maintenance', $maintenanceRequest);
            }
        }

        return response()->json([
            'success' => true,
            'message' => trans('Maintenance request submitted successfully.'),
        ], 201);
    } catch (Exception $e) {
        Log::error('Failed to store maintenance request', [
            'action' => 'store_maintenance_request',
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
        ]);
        return response()->json([
            'success' => false,
            'message' => trans('Failed to submit the maintenance request. Please try again.'),
        ], 500);
    }
}
    /**
     * Get problems for a specific category.
     *
     * @param int $id The ID of the maintenance category.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProblemsByCategory($id)
    {
        try {
            $problems = MaintenanceProblem::where('category_id', $id)
                ->where('is_active', true)
                ->get()
                ->map(function ($problem) {
                    return [
                        'id' => $problem->id,
                        'category_id' => $problem->category_id,
                        'name' => $problem->name,
                        'is_active' => $problem->is_active,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $problems,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch problems by category', [
                'action' => 'fetch_problems_by_category',
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => trans('Failed to fetch problems. Please try again.'),
            ], 500);
        }
    }
}