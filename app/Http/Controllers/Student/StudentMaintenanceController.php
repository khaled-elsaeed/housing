<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceProblem;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use App\Contracts\UploadServiceContract;
use Illuminate\Support\Facades\{Auth, DB};
use Exception;

class StudentMaintenanceController extends Controller
{
    public function __construct(private UploadServiceContract $uploadService)
    {
    }

    /**
     * Display the maintenance request index page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $requests = MaintenanceRequest::where('user_id', Auth::user()->id)
                ->with(['category', 'room.reservation.user'])
                ->orderBy('created_at', 'asc')
                ->paginate(3);
                
            return view('student.maintenance.index', compact('requests'));
        } catch (Exception $e) {
            logError('Failed to load resident maintenance page', 'show_resident_maintenance_page', $e);
            return redirect()->back()->withErrors([
                'error' => __('Unable to load the maintenace page. Please try again later.'),
            ]);
        }
    }

    /**
     * Display the maintenance request creation page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            $categories = MaintenanceCategory::all();
            return view('student.maintenance.create', compact('categories'));
        } catch (Exception $e) {
            logError('Failed to load maintenance creation page', 'load_maintenance_create_page', $e);
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

            $validatedData = $request->validate([
                'category_id' => 'required|exists:maintenance_categories,id',
                'problems' => 'required|array',
                'problems.*' => 'exists:maintenance_problems,id',
                'description' => 'required|string|max:1000',
                'photos' => 'nullable|array',
                'photos.*' => 'image|mimes:jpeg,png,jpg|max:3072',
            ]);
    
            $user = Auth::user();
            $reservation = $user->reservations()->latest()->first();
    
            if (!$reservation) {
                return errorResponse(trans('You do not have an active reservation.'), 400);
            }
    
            DB::transaction(function () use ($request, $reservation, $user, &$maintenanceRequest) {
                $maintenanceRequest = MaintenanceRequest::create([
                    'user_id' => $user->id,
                    'room_id' => $reservation->room_id,
                    'category_id' => $request->category_id,
                    'description' => $request->description,
                    'status' => 'pending',
                ]);
    
                $maintenanceRequest->problems()->attach($request->problems);
    
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $this->uploadService->upload($photo, 'maintenance', $maintenanceRequest);
                    }
                }
    
                userActivity($user->id, 'create_maintenance_request', 'Submitted a new maintenance request successfully.');
            });
    
            return successResponse(trans('Maintenance request submitted successfully.'));
        } catch (Exception $e) {
            logError('Failed to store maintenance request', 'store_maintenance_request', $e);
            return errorResponse(trans('Failed to submit the maintenance request. Please try again.'), 500);
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
                ->map(fn($problem) => [
                    'id' => $problem->id,
                    'category_id' => $problem->category_id,
                    'name' => $problem->name,
                    'is_active' => $problem->is_active,
                ]);
            return successResponse('', null, ['data' => $problems]);
            
        } catch (Exception $e) {
            logError('Failed to fetch problems by category', 'fetch_problems_by_category', $e);
            return errorResponse(trans('Failed to fetch problems. Please try again.'), 500);
        }
    }
}