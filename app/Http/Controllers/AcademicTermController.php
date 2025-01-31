<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicTerm;
use App\Models\Reservation; // Ensure this model exists
use Illuminate\Support\Facades\DB;
use Exception;

class AcademicTermController extends Controller
{
    /**
     * Create a new academic term (API endpoint).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        // Validate the request
        $request->validate([
            'academic_year' => 'required|string',
            'name' => 'required|string',
            'semester' => 'required|in:first,second,summer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        // Create the academic term
        $academicTerm = AcademicTerm::create([
            'academic_year' => $request->academic_year,
            'name' => $request->name,
            'semester' => $request->semester,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'planned', 
        ]);

        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => trans('Academic term created successfully.'),
        ], 201);
    }

    /**
     * Start an academic term.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function start($id)
    {
        try {
            DB::beginTransaction();

            $academicTerm = AcademicTerm::findOrFail($id);

            // Check if there's already an active term
            if (AcademicTerm::where('status', 'active')->exists()) {
                throw new Exception(trans('Another academic term is already active.'));
            }

            // Check if the term is already completed
            if ($academicTerm->status === 'completed') {
                throw new Exception(trans('Cannot start a completed academic term.'));
            }

            $academicTerm->update([
                'status' => 'active',
                'start_date' => now()
            ]);

            // Update all related reservations to completed
            Reservation::where('academic_term_id', $id)
                      ->where('status', 'upcoming')
                      ->update(['status' => 'active']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('Academic term started successfully.')
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * End an academic term and update related reservations to "completed".
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function end($id)
    {
        try {
            DB::beginTransaction();

            $academicTerm = AcademicTerm::findOrFail($id);

            // Check if the term is active
            if ($academicTerm->status !== 'active') {
                throw new Exception(trans('Only active academic terms can be ended.'));
            }

            $academicTerm->update([
                'status' => 'completed',
                'end_date' => now()
            ]);

            // Update all related reservations to completed
            Reservation::where('academic_term_id', $id)
                      ->whereNotIn('status', ['cancelled', 'rejected'])
                      ->update(['status' => 'completed']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('Academic term ended successfully and all related reservations have been completed.')
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}