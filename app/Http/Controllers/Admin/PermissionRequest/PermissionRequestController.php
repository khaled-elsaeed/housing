<?php

namespace App\Http\Controllers\Admin\PermissionRequest;

use App\Http\Controllers\Controller;
use App\Models\StudentPermissionRequest;
use Illuminate\Http\Request;

class PermissionRequestController extends Controller
{
    public function index()
    {
        try{
            $permissionRequests = StudentPermissionRequest::with(['user', 'studentPermission'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPermissionRequests = $permissionRequests->count();

        // Gender-based filtering
        $malePermissionRequests = $permissionRequests->where('gender', 'male');
        $femalePermissionRequests = $permissionRequests->where('gender', 'female');

        // Total male and female counts
        $totalMalePermissionRequests = $malePermissionRequests->count();
        $totalFemalePermissionRequests = $femalePermissionRequests->count();

        // Pending counts
        $pendingPermissionRequestsCount = $permissionRequests->where('status', 'pending')->count();
        $malePendingPermissionRequestsCount = $malePermissionRequests->where('status', 'pending')->count();
        $femalePendingPermissionRequestsCount = $femalePermissionRequests->where('status', 'pending')->count();

        // Approved counts
        $approvedPermissionRequestsCount = $permissionRequests->where('status', 'approved')->count();
        $maleApprovedPermissionRequestsCount = $malePermissionRequests->where('status', 'approved')->count();
        $femaleApprovedPermissionRequestsCount = $femalePermissionRequests->where('status', 'approved')->count();

        // Rejected counts
        $rejectedPermissionRequestsCount = $permissionRequests->where('status', 'rejected')->count();
        $maleRejectedPermissionRequestsCount = $malePermissionRequests->where('status', 'rejected')->count();
        $femaleRejectedPermissionRequestsCount = $femalePermissionRequests->where('status', 'rejected')->count();

        return view('admin.permissions.index', compact(
            'permissionRequests',
            'totalPermissionRequests',
            'totalMalePermissionRequests',
            'totalFemalePermissionRequests',
            'pendingPermissionRequestsCount',
            'malePendingPermissionRequestsCount',
            'femalePendingPermissionRequestsCount',
            'approvedPermissionRequestsCount',
            'maleApprovedPermissionRequestsCount',
            'femaleApprovedPermissionRequestsCount',
            'rejectedPermissionRequestsCount',
            'maleRejectedPermissionRequestsCount',
            'femaleRejectedPermissionRequestsCount'
        ));
        }catch (Exception $e) {
            Log::error('Error retrieving permissions page data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->view('errors.505');
        }
        
    }
}
