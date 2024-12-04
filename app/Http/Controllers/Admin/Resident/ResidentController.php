<?php

namespace App\Http\Controllers\Admin\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Exports\Residents\ResidentsExport;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Exception;

class ResidentController extends Controller
{
    public function index()
    {
        try {
            return view('admin.residents.index');
        } catch (Exception $e) {
            Log::error('Error retrieving resident page data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->view('errors.505');
        }
    }

    public function fetchResidents(Request $request)
{
    try {
        // Base query with relationships
        $query = User::role('resident')
            ->whereHas('student', function ($q) {
                $q->where('application_status', 'final_accepted');
            })
            ->with(['student', 'student.faculty']);

        // Apply search filter
        if ($request->filled('customSearch')) {
            $searchTerm = $request->get('customSearch');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('student', function ($q) use ($searchTerm) {
                    $q->where('name_en', 'like', "%$searchTerm%")
                        ->orWhere('national_id', 'like', "%$searchTerm%")
                        ->orWhere('mobile', 'like', "%$searchTerm%");
                })->orWhereHas('student.faculty', function ($q) use ($searchTerm) {
                    $q->where('name_en', 'like', "%$searchTerm%");
                });
            });
        }

        // Clone query for filtered records count
        $filteredQuery = clone $query;
        $totalRecords = User::role('resident')->count();
        $filteredRecords = $filteredQuery->count();

        // Pagination
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $residents = $query->skip($start)->take($length)->get();

        // Map response data
        return response()->json([
            'draw' => $request->get('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $residents->map(function ($resident) {
                $location = method_exists($resident, 'getLocationDetails') 
                    ? $resident->getLocationDetails() 
                    : ['building' => 'N/A', 'apartment' => 'N/A', 'room' => 'N/A'];

                $locationString = implode(' - ', $location);

                return [
                    'resident_id' => $resident->id,
                    'name' => $resident->student->name_en ?? 'N/A',
                    'national_id' => $resident->student->national_id ?? 'N/A',
                    'location' => $locationString,
                    'faculty' => $resident->student->faculty->name_en ?? 'N/A',
                    'mobile' => $resident->student->mobile ?? 'N/A',
                    'registration_date' => $resident->created_at ? $resident->created_at->format('F j, Y, g:i A') : 'N/A',
                    'actions' => '<button type="button" class="btn btn-round btn-info-rgba" 
                                data-resident-id="' . $resident->id . '" id="details-btn" 
                                title="More Details"><i class="feather icon-info"></i></button>',
                ];
            }),
        ]);
    } catch (Exception $e) {
        // Log the error and return a failure response
        Log::error('Error fetching residents data: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch residents data.'], 500);
    }
}

    public function getSummary()
    {
        try {
            $totalResidents = User::role('resident')
                ->whereHas('student', function($query) {
                    $query->where('application_status', 'final_accepted');
                })
                ->count();
    
            $totalMaleCount = User::role('resident')
                ->whereHas('student', function($query) {
                    $query->where('gender', 'male')->where('application_status', 'final_accepted');
                })
                ->count();
    
            $totalFemaleCount = User::role('resident')
                ->whereHas('student', function($query) {
                    $query->where('gender', 'female')->where('application_status', 'final_accepted');
                })
                ->count();
    
            return response()->json([
                'totalResidents' => $totalResidents,
                'totalMaleCount' => $totalMaleCount,
                'totalFemaleCount' => $totalFemaleCount,
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching summary data: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Error fetching summary data'], 500);
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
            $resident = User::find($id);

            if (!$resident) {
                return response()->json(['error' => 'Resident not found', 'user' => $id], 404);
            }

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
