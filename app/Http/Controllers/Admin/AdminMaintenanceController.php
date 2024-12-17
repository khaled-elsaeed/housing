<?php

namespace App\Http\Controllers\Admin;

use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\MaintenanceRequests\MaintenanceRequestsExport;

use Illuminate\Support\Facades\Log;

class AdminMaintenanceController extends Controller
{
    public function index()
{
    try {
        
        $maintenanceRequests = MaintenanceRequest::with(['user','issues'])
        ->orderByRaw("FIELD(status, 'pending', 'in_progress', 'completed', 'rejected')")
        ->get();
            
        $totalMaintenanceRequests = $maintenanceRequests->count();

        
        $maleRequests = $maintenanceRequests->filter(fn($request) => optional($request->user)->gender === 'male');
        $femaleRequests = $maintenanceRequests->filter(fn($request) => optional($request->user)->gender === 'female');

        $maleTotalCount = $maleRequests->count();
        $femaleTotalCount = $femaleRequests->count();

        
        $pendingRequests = $maintenanceRequests->where('status', 'pending');
        $completedRequests = $maintenanceRequests->where('status', 'completed');
        $rejectedRequests = $maintenanceRequests->where('status', 'rejected');

        $pendingMaintenanceRequestsCount = $pendingRequests->count();
        $completedMaintenanceRequestsCount = $completedRequests->count();
        $rejectedMaintenanceRequestsCount = $rejectedRequests->count();

        
        $malePendingCount = $maleRequests->where('status', 'pending')->count();
        $femalePendingCount = $femaleRequests->where('status', 'pending')->count();

        $maleCompletedCount = $maleRequests->where('status', 'completed')->count();
        $femaleCompletedCount = $femaleRequests->where('status', 'completed')->count();

        $maleRejectedCount = $maleRequests->where('status', 'rejected')->count();
        $femaleRejectedCount = $femaleRequests->where('status', 'rejected')->count();

        
        return view('admin.maintenance.index', compact(
            'maintenanceRequests',
            'totalMaintenanceRequests',
            'maleTotalCount',
            'femaleTotalCount',
            'pendingMaintenanceRequestsCount',
            'completedMaintenanceRequestsCount',
            'rejectedMaintenanceRequestsCount',
            'malePendingCount',
            'femalePendingCount',
            'maleCompletedCount',
            'femaleCompletedCount',
            'maleRejectedCount',
            'femaleRejectedCount'
        ));
    } catch (Exception $e) {
        Log::error('Error retrieving maintenance request data: ' . $e->getMessage(), [
            'exception' => $e,
            'stack' => $e->getTraceAsString(),
        ]);

        return response()->view('errors.505');
    }
}

public function updateStatus(Request $request, $id)
{
    try {
        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed,rejected',
        ]);

        $maintenanceRequest = MaintenanceRequest::findOrFail($id);

        $maintenanceRequest->status = $validated['status'];
        $maintenanceRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status' => $maintenanceRequest->status
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error updating maintenance request status: ' . $e->getMessage(), [
            'exception' => $e,
            'stack_trace' => $e->getTraceAsString(),
        ]);

        return response()->json(['error' => 'Failed to update the maintenance request status.'], 500);
    }
}

public function downloadMaintenanceRequestsExcel()
{
    try {
        $export = new MaintenanceRequestsExport();
        return $export->downloadExcel();
    } catch (\Exception $e) {
        Log::error('Error exporting maintenance requests to Excel', [
            'exception' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['error' => 'Failed to export maintenance requests to Excel'], 500);
    }
}

  /**
     * Fetch the issues for a specific maintenance request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIssues($id)
    {
        try {
            // Find the maintenance request by its ID
            $maintenanceRequest = MaintenanceRequest::with('issues')->findOrFail($id);

            // Return issues and additional data as JSON
            return response()->json([
                'issues' => $maintenanceRequest->issues,
                'additional_info' => $maintenanceRequest->description,
                'status' => $maintenanceRequest->status
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching issues for maintenance request: ' . $e->getMessage(), [
                'exception' => $e,
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to fetch issues for this maintenance request.'], 500);
        }
    }


    
}
