<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermissionRequest;
use App\Models\PermissionDetail;

class StudentPermissionController extends Controller
{
    /**
     * Show the permission request form.
     */
    public function showForm()
    {
        return view('student.permission'); // Replace with the actual view file
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'permission_type' => 'required|in:late_permission,another_permission',  // Updated validation rule
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'additional_info' => 'nullable|string',
        ]);
    
        // Begin a database transaction
        \DB::beginTransaction();
    
        try {
            // Create a new PermissionRequest
            $permissionRequest = PermissionRequest::create([
                'user_id' => auth()->id(),
                'additional_info' => $validatedData['additional_info'] ?? null,
            ]);
    
            // Add Permission Details
            $permissionDetail = PermissionDetail::create([
                'permission_request_id' => $permissionRequest->id,
                'permission_type' => $validatedData['permission_type'],
                'description' => $validatedData['description'] ?? null,
                'start_date' => $validatedData['start_date'] ?? null,
                'end_date' => $validatedData['end_date'] ?? null,
            ]);
    
            // Commit the transaction if everything is fine
            \DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Permission request submitted successfully.'
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            \DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit permission request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
