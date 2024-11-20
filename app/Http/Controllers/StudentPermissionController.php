<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentPermission;
use App\Models\User;
use Carbon\Carbon;

class StudentPermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        // Fetching data needed for the dashboard metrics
        $permissions = StudentPermission::with('student')->get();
        $totalPermissions = $permissions->count();
        $approvedPermissionsCount = $permissions->where('status', 'approved')->count();
        $pendingPermissionsCount = $permissions->where('status', 'pending')->count();
        $deniedPermissionsCount = $permissions->where('status', 'denied')->count();
        $lateArrivalPermissionsCount = $permissions->where('type', 'late_arrival')->count();
        $extendedStayPermissionsCount = $permissions->where('type', 'extended_stay')->count();

        // Pass data to the view
        return view('admin.student-permissions.index', compact(
            'permissions',
            'totalPermissions',
            'approvedPermissionsCount',
            'pendingPermissionsCount',
            'deniedPermissionsCount',
            'lateArrivalPermissionsCount',
            'extendedStayPermissionsCount'
        ));
    }




    public function manage()
    {
        // Fetching data needed for the dashboard metrics
        $permissions = StudentPermission::get();
        $totalPermissions = $permissions->count();
        $approvedPermissionsCount = $permissions->where('status', 'approved')->count();
        $pendingPermissionsCount = $permissions->where('status', 'pending')->count();
        $deniedPermissionsCount = $permissions->where('status', 'denied')->count();
        $lateArrivalPermissionsCount = $permissions->where('type', 'late_arrival')->count();
        $extendedStayPermissionsCount = $permissions->where('type', 'extended_stay')->count();

        // Pass data to the view
        return view('admin.student-permissions.manage', compact(
            'permissions',
            'totalPermissions',
            'approvedPermissionsCount',
            'pendingPermissionsCount',
            'deniedPermissionsCount',
            'lateArrivalPermissionsCount',
            'extendedStayPermissionsCount'
        ));
    }


    public function store(Request $request)
    {
        try {
            $permission = StudentPermission::create($request->all());
    
            // Respond with success message
            return response()->json(['success' => true, 'message' => 'Permission created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create permission'], 500);
        }
    }
    

    public function edit($id)
    {
        $permission = StudentPermission::findOrFail($id);
        return response()->json($permission);  // Return permission data for AJAX
    }
    
    public function update(Request $request, $id)
    {
        try {
            $permission = StudentPermission::findOrFail($id);
            $permission->update($request->all());
    
            // Respond with success message
            return response()->json(['success' => true, 'message' => 'Permission updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update permission'], 500);
        }
    }
    
    
    public function destroy($id)
    {
        $permission = StudentPermission::findOrFail($id);
        $permission->delete();  // Delete the permission
        return response()->json(['success' => true]);  // Return success message
    }
    
}
