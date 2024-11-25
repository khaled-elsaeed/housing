<?php

namespace App\Http\Controllers\Admin\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Exports\Residents\ResidentsExport;
use Illuminate\Support\Facades\Log;
use Exception;

class ResidentController extends Controller
{
    public function index()
    {
        try {

            $residents = User::role('resident')
            ->whereHas('student', function ($query) {
                $query->where('application_status', 'final_accepted');
            })
            ->with(['student'])
            ->orderBy('created_at', 'desc')
            ->get();
        

// Log the first 5 residents
$firstFiveresidents = $residents->take(5);

Log::info('First 5 residents fetched:', $firstFiveresidents->toArray());

    
            // Count the total number of residents, males, and females
            $totalResidents = $residents->count();
            $totalMaleCount = $residents->filter(fn($resident) => $resident->gender === 'male')->count();
            $totalFemaleCount = $residents->filter(fn($resident) => $resident->gender === 'female')->count();
    
            // Return the view with the necessary data
            return view(
                'admin.residents.index',
                compact(
                    'residents',
                    'totalResidents',
                    'totalMaleCount',
                    'totalFemaleCount'
                )
            );
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error('Error retrieving resident page data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);
    
            // Return a generic error page with the error message
            return response()->view('error.page_init', ['errorMessage' => $e->getMessage()]);
        }
    }
    
    public function downloadResidentsExcel()
    {
        try {
            $export = new ResidentsExport();
            return $export->downloadExcel();
        } catch (Exception $e) {
            Log::error('Error exporting residents to Excel', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to export residents to Excel'], 500);
        }
    }

    public function downloadResidentsPDF()
    {
        try {
            $export = new ResidentsExport();
            $pdf = $export->downloadPDF();
            return $pdf->download('residents_report.pdf');
        } catch (Exception $e) {
            Log::error('Error exporting residents to PDF', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to export residents to PDF'], 500);
        }
    }

    public function getResidentMoreDetails($id)
    {
        try {
            // Retrieve the resident by ID
            $resident = User::find($id);

            if (!$resident) {
                return response()->json(['error' => 'Resident not found', 'user' => $id], 404);
            }
    
            // Gather detailed information about the resident
            $details = [
                'faculty' => optional($resident->student->faculty)->name_en ?? 'Not available',
                'program' => optional($resident->student->program)->name_en ?? 'Not available',
                'score' => optional($resident->student->universityArchive)->score ?? 'Not available',
                'percent' => optional($resident->student->universityArchive)->percent ?? 'Not available',
                'governorate' => optional($resident->student->governorate)->name_ar ?? 'Not available',
                'city' => optional($resident->student->city)->name_ar ?? 'Not available',
                'street' => $resident->student->street ?? 'Not available',
            ];
            
            return response()->json([
                'success' => true,
                'data' => $details
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching resident details', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'resident_id' => $id
            ]);
    
            return response()->json(['error' => 'Resident not found or an error occurred', 'user' => $id], 404);
        }
    }
}
