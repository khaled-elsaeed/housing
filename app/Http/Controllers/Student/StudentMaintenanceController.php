<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceProblem;
use App\Models\MaintenanceRequest;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;

class StudentMaintenanceController extends Controller
{

    public function index()
{
    

    return view('student.maintenance.index');
}
public function fetchUserRequests(Request $request)
{
    try {
        $user = Auth::user();

        $query = MaintenanceRequest::whereHas('room.reservation', function ($query) use ($user) {
                $query->where('user_id', $user->id); 
            })
            ->select('maintenance_requests.*')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addColumn('category', function ($request) {
                return $request->category ? $request->category->name : 'N/A'; // Localized name
            })
            ->addColumn('room', function ($request) {
                return $request->room ? $request->room->room_number : 'N/A'; // Room details
            })
            ->addColumn('reservation', function ($request) {
                return $request->reservation ? $request->reservation->id : 'N/A'; // Reservation ID
            })
            ->addColumn('user', function ($request) {
                return $request->reservation && $request->reservation->user 
                    ? $request->reservation->user->name 
                    : 'N/A'; // User from reservation
            })
            ->addColumn('status', function ($request) {
                return $request->status;
            })
            ->addColumn('created_at', function ($request) {
                return $request->created_at->format('Y-m-d H:i:s');
            })
            ->make(true);
    } catch (Exception $e) {
        Log::error('Failed to fetch user maintenance requests', [
            'error' => $e->getMessage(),
            'action' => 'fetch_user_maintenance_requests',
            'user_id' => Auth::id(),
        ]);
        return response()->json(['error' => 'Failed to fetch maintenance requests.'], 500);
    }
}


    /**
     * Display a listing of maintenance requests for the student.
     * This method will still return a view.
     */
    public function create()
    {
        $user = Auth::user();
        $requests = MaintenanceRequest::query()
            ->with(['category', 'room.reservation'])
            ->whereHas('room.reservation', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->paginate(10);

        $categories = MaintenanceCategory::all();

        // Get statistics
        $stats = [
            'total' => $requests->total(),
            'pending' => MaintenanceRequest::whereHas('room.reservation', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::whereHas('room.reservation', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completed' => MaintenanceRequest::whereHas('room.reservation', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'completed')->count(),
        ];

        return view('student.maintenance.create', compact('requests', 'categories', 'stats'));
    }

    /**
     * Store a newly created maintenance request.
     * This method will return a JSON response.
     */
    public function store(Request $request)
{
    // Validate the request
    $validatedData = $request->validate([
        'category_id' => 'required|exists:maintenance_categories,id',
        'problems' => 'required|array',
        'problems.*' => 'exists:maintenance_problems,id',
        'description' => 'required|string|max:1000',
        'photos' => 'nullable|array',
        'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // Max 5MB per file
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Retrieve the room_id from the user's reservation
    $reservation = $user->reservations()->latest()->first();
    if (!$reservation) {
        return response()->json([
            'success' => false,
            'message' => 'You do not have an active reservation.',
        ], 400);
    }

    $room_id = $reservation->room_id;

    // Fetch problem details (id + name)
    $problems = MaintenanceProblem::whereIn('id', $validatedData['problems'])
        ->get(['id', 'name']) // Fetch only id & name
        ->toArray();

    // Create the maintenance request
    $maintenanceRequest = MaintenanceRequest::create([
        'user_id' => $user->id,
        'room_id' => $room_id,
        'category_id' => $validatedData['category_id'],
        'problems' => $problems,
        'description' => $validatedData['description'],
    ]);

    // Handle file uploads
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $photo) {
            // Store the file on the disk (e.g., "public" disk)
            $path = $photo->store('maintenance_photos', 'public');

            // Create a new Media record
            $media = new Media([
                'name' => $photo->getClientOriginalName(),
                'file_name' => $photo->getClientOriginalName(),
                'mime_type' => $photo->getClientMimeType(),
                'path' => $path,
                'disk' => 'public',
                'file_hash' => md5_file($photo->getRealPath()),
                'collection' => 'maintenance',
                'size' => $photo->getSize(),
            ]);

            // Associate the media with the maintenance request
            $maintenanceRequest->media()->save($media);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Maintenance request submitted successfully.',
        'data' => $maintenanceRequest,
    ], 201);
}

    


    /**
     * Display the specified maintenance request.
     * This method will return a JSON response.
     */
    public function show($id)
    {
        $user = Auth::user();
        $request = MaintenanceRequest::with(['category', 'room.building', 'room.reservation', 'assignedTo'])
            ->whereHas('room.reservation', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);

        // Parse problems from JSON
        $request->problemsList = json_decode($request->problems, true);

        // Parse photos from JSON
        $request->photosList = !empty($request->photos) ? json_decode($request->photos, true) : [];

        // Get timeline data
        $timeline = $this->getRequestTimeline($request);

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $request,
                'timeline' => $timeline,
            ],
        ]);
    }

    /**
     * Get problems for a specific category (AJAX endpoint).
     * This method will return a JSON response.
     */
    public function getProblemsByCategory($id)
{
    $problems = MaintenanceProblem::where('category_id', $id)
        ->where('is_active', true)
        ->get()
        ->map(function ($problem) {
            return [
                'id' => $problem->id,
                'category_id' => $problem->category_id,
                'name' => $problem->name, // Uses the accessor dynamically
                'is_active' => $problem->is_active,
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $problems,
    ]);
}


    /**
     * Generate timeline data for the request.
     */
    private function getRequestTimeline($maintenanceRequest)
    {
        $timeline = [];

        // Creation event
        $timeline[] = [
            'date' => $maintenanceRequest->created_at->format('Y-m-d H:i'),
            'title' => __('Request Submitted'),
            'description' => __('Your maintenance request was submitted.'),
            'icon' => 'fas fa-plus-circle',
            'color' => 'primary',
        ];

        // Assignment event
        if ($maintenanceRequest->assigned_to) {
            $timeline[] = [
                'date' => $maintenanceRequest->assigned_at ? $maintenanceRequest->assigned_at->format('Y-m-d H:i') : null,
                'title' => __('Assigned to Staff'),
                'description' => __('Request assigned to') . ' ' . $maintenanceRequest->assignedTo->name,
                'icon' => 'fas fa-user-check',
                'color' => 'info',
            ];
        }

        // In progress event
        if (in_array($maintenanceRequest->status, ['in_progress', 'completed'])) {
            $timeline[] = [
                'date' => $maintenanceRequest->in_progress_at ? $maintenanceRequest->in_progress_at->format('Y-m-d H:i') : null,
                'title' => __('Work Started'),
                'description' => __('Maintenance work has begun on your request.'),
                'icon' => 'fas fa-tools',
                'color' => 'warning',
            ];
        }

        // Completed event
        if ($maintenanceRequest->status == 'completed') {
            $timeline[] = [
                'date' => $maintenanceRequest->completed_at ? $maintenanceRequest->completed_at->format('Y-m-d H:i') : null,
                'title' => __('Work Completed'),
                'description' => __('Maintenance work has been completed.'),
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
            ];

            // Resolution confirmation
            if ($maintenanceRequest->is_resolution_confirmed) {
                $timeline[] = [
                    'date' => $maintenanceRequest->resolution_confirmed_at ? $maintenanceRequest->resolution_confirmed_at->format('Y-m-d H:i') : null,
                    'title' => __('Resolution Confirmed'),
                    'description' => __('You confirmed the issue was successfully resolved.'),
                    'icon' => 'fas fa-thumbs-up',
                    'color' => 'success',
                ];
            }
        }

        // Cancelled event
        if ($maintenanceRequest->status == 'cancelled') {
            $timeline[] = [
                'date' => $maintenanceRequest->updated_at->format('Y-m-d H:i'),
                'title' => __('Request Cancelled'),
                'description' => __('This maintenance request was cancelled.'),
                'icon' => 'fas fa-ban',
                'color' => 'danger',
            ];
        }

        // Sort timeline by date
        usort($timeline, function ($a, $b) {
            if (empty($a['date']) && empty($b['date'])) return 0;
            if (empty($a['date'])) return 1;
            if (empty($b['date'])) return -1;
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $timeline;
    }
}