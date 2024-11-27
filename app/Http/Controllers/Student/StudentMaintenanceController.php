<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentMaintenanceController extends Controller
{
    // Show the maintenance request form
    public function showForm()
    {
        return view('student.maintenance');
    }

    // Handle the form submission and store the request
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'issue_type' => 'required|string',
            'water_issues' => 'nullable|array',
            'electrical_issues' => 'nullable|array',
            'housing_issues' => 'nullable|array',
            'image' => 'nullable|image|max:2048', // Max 2MB for the image
        ]);

        // Prepare the data for storing
        $data = [
            'issue_type' => $request->input('issue_type'),
            'water_issues' => json_encode($request->input('water_issues', [])),
            'electrical_issues' => json_encode($request->input('electrical_issues', [])),
            'housing_issues' => json_encode($request->input('housing_issues', [])),
        ];

        // Handle image upload (if there's an image)
        if ($request->hasFile('image')) {
            // Store the image in 'maintenance-images' directory
            $path = $request->file('image')->store('maintenance-images', 'public');
            $data['image'] = $path;
        }

        // Save the maintenance request in the database
        MaintenanceRequest::create($data);

        // Redirect or show success message
        return redirect()->route('student.maintenance.form')->with('success', 'Your maintenance request has been submitted successfully.');
    }
}
