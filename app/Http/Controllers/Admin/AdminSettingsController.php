<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminSettingsController extends Controller
{
    /**
     * Display comprehensive system settings
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $academicTerms = AcademicTerm::orderBy('start_date', 'desc')->get();

        return view('admin.setting', compact('settings', 'academicTerms'));
    }

    /**
     * Update global system settings
     */
    public function updateSystemSettings(Request $request)
    {
        try {
            DB::beginTransaction();

            // Reservation Settings
            $reservationSettings = [
                'reservation_start' => $request->reservation_start,
                'reservation_end' => $request->reservation_end,
                'student_type' => $request->student_type,
                'reservation_status' => $request->reservation_status
            ];

            foreach ($reservationSettings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            DB::commit();

            return redirect()->back()->with('success', 'System settings updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Create a new academic term
     */
    public function createAcademicTerm(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string'
        ]);

        try {
            // Check for overlapping terms
            $overlappingTerm = AcademicTerm::where(function($query) use ($validatedData) {
                $query->whereBetween('start_date', [$validatedData['start_date'], $validatedData['end_date']])
                      ->orWhereBetween('end_date', [$validatedData['start_date'], $validatedData['end_date']]);
            })->exists();

            if ($overlappingTerm) {
                throw ValidationException::withMessages([
                    'start_date' => ['The selected dates overlap with an existing academic term.']
                ]);
            }

            AcademicTerm::create([
                'name' => $validatedData['name'],
                'code' => $validatedData['code'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'description' => $validatedData['description'],
                'status' => 'upcoming'
            ]);

            return redirect()->back()->with('success', 'Academic term created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create academic term: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing academic term
     */
    public function updateAcademicTerm(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string'
        ]);

        try {
            $term = AcademicTerm::findOrFail($id);
            $term->update($validatedData);

            return redirect()->back()->with('success', 'Academic term updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update academic term: ' . $e->getMessage());
        }
    }

    /**
     * Delete an academic term
     */
    public function deleteAcademicTerm($id)
    {
        try {
            $term = AcademicTerm::findOrFail($id);
            $term->delete();

            return redirect()->back()->with('success', 'Academic term deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete academic term: ' . $e->getMessage());
        }
    }

    /**
     * Automatic academic term status update
     */
    public function updateTermStatus()
    {
        $now = now();

        AcademicTerm::where('start_date', '>', $now)
            ->update(['status' => 'upcoming']);

        AcademicTerm::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->update(['status' => 'active']);

        AcademicTerm::where('end_date', '<', $now)
            ->update(['status' => 'completed']);
    }
}