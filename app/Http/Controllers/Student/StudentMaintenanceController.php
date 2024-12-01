<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceIssue;
use App\Models\MaintenanceImage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class StudentMaintenanceController extends Controller
{
    // Show the maintenance request form
    public function showForm()
    {
        return view('student.maintenance');
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'additional_info' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Image validation
            'housing_issues' => 'nullable|array',
            'housing_issues.*' => 'string',
        ]);

        // Start a database transaction
        \DB::beginTransaction();

        try {
            // Create the maintenance request
            $maintenanceRequest = MaintenanceRequest::create([
                'user_id' => Auth::id(),
                'additional_info' => $validated['additional_info'] ?? null,
            ]);

            // Handle housing issues, if any
            if (!empty($validated['housing_issues'])) {
                foreach ($validated['housing_issues'] as $issue) {
                    MaintenanceIssue::create([
                        'maintenance_request_id' => $maintenanceRequest->id,
                        'issue_type' => 'General Housing',
                        'description' => $issue,
                    ]);
                }
            }

            // Handle image upload, if any
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('maintenance', 'public');

                MaintenanceImage::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'image_path' => $imagePath,
                ]);
            }

            // Commit the transaction
            \DB::commit();

            // Return a successful response
           
            return response()->json([
                'success' => true,
                'message' => 'Maintenance request submitted successfully.'
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            \DB::rollBack();

            // Return an error response
            return response()->json([
                'message' => 'Error while submitting the maintenance request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
