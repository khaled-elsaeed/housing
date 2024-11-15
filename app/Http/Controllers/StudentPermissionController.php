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
        $permissions = StudentPermission::all();
        return view('admin.student-permissions.manage', compact('permissions'));
    }




    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        StudentPermission::create($request->all());
        return back()->with('success', 'Permission created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        $permission = StudentPermission::findOrFail($id);
        $permission->update($request->all());
        return back()->with('success', 'Permission updated successfully.');
    }

    public function destroy($id)
    {
        $permission = StudentPermission::findOrFail($id);
        $permission->delete();
        return back()->with('success', 'Permission deleted successfully.');
    }
}
