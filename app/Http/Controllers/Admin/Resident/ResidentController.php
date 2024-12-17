<?php

namespace App\Http\Controllers\Admin\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Program;
use App\Models\Faculty;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\App;


class ResidentController extends Controller
{
    /**
     * Display the residents index page.
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Fetch residents with search, pagination, and filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchResidents(Request $request)
    {
        $currentLang = App::getLocale(); // Get current locale

        try {
            $query = User::role('resident')
                ->whereHas('student', function ($q) {
                    $q->where('application_status', 'final_accepted');
                })
                ->with(['student', 'student.faculty']);

                if ($request->filled('customSearch')) {
                    $searchTerm = $request->get('customSearch');
                    $query->where(function ($q) use ($searchTerm, $currentLang) {
                        $q->whereHas('student', function ($q) use ($searchTerm, $currentLang) {
                            $q->where('name_' . ($currentLang == 'ar' ? 'ar' : 'en'), 'like', '%' . $searchTerm . '%')
                              ->orWhere('national_id', 'like', '%' . $searchTerm . '%')
                              ->orWhere('mobile', 'like', '%' . $searchTerm . '%');
                        });
                    });
                }
                

            $filteredQuery = clone $query;
            $totalRecords = User::role('resident')->count();
            $filteredRecords = $filteredQuery->count();

            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $residents = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->get('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $residents->map(function ($resident) use ($currentLang) {
                    $location = method_exists($resident, 'getLocationDetails') 
                        ? $resident->getLocationDetails() 
                        : ['building' => 'N/A', 'apartment' => 'N/A', 'room' => 'N/A'];
                    
                    // Construct the location string
                    $locationString = __(
                        'pages.admin.apartment.building'
                    ) . ' ' . $location['building'] . ' - ' . __(
                        'pages.admin.rooms.apartment'
                    ) . ' ' .$location['apartment'] . ' - ' . __(
                        'pages.admin.rooms.room'
                    ) . ' ' . $location['room'];

                    return [
                        'resident_id' => $resident->id,
                        'name' => $resident->student->{'name_' . ($currentLang == 'ar' ? 'ar' : 'en')} ?? 'N/A',  
                        'national_id' => $resident->student->national_id ?? 'N/A',
                        'location' => $locationString,
                        'faculty' => $resident->student->faculty->{'name_' . ($currentLang == 'ar' ? 'ar' : 'en')} ?? 'N/A',  
                        'mobile' => $resident->student->mobile ?? 'N/A',
                        'registration_date' => $resident->created_at ? $resident->created_at->format('F j, Y, g:i A') : 'N/A',
                    ];
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching residents data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch residents data.'], 500);
        }
    }

    /**
     * Fetch summary data for residents.
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Download residents data in Excel format.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Download residents data in PDF format.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Get more detailed information for a specific resident.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Display the resident creation page with required data.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $governorates = Governorate::all();
            $cities = City::all(); 
            $faculties = Faculty::all(); 
            $programs = Program::all(); 
    
            return view('admin.residents.create', compact('governorates', 'cities', 'faculties', 'programs'));
        } catch (Exception $e) {
            Log::error('Error retrieving resident create page data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->view('errors.505', [], 505);
        }
    }
}
