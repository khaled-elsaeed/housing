<?php

namespace App\Http\Controllers\Admin\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Exports\Applicants\ApplicantsExport;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\App;

class ApplicantController extends Controller
{
    /**
     * Display the applicant page.
     *
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function showApplicantPage()
    {
        try {
            if (Gate::denies('is-admin')) {
                return response()->view('errors.403', [], 403);
            }
            return view('admin.applicant.index');
        } catch (Exception $e) {
            Log::error('Error retrieving applicant page data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->view('errors.505');
        }
    }

    /**
     * Fetch applicants based on search and filtering criteria.
     *
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchApplicants(Request $request)
    {
        $currentLang = App::getLocale(); // Get current locale

        try {
            $query = User::role('resident')
                ->with(['student'])
                ->leftJoin('students', 'students.user_id', '=', 'users.id')
                ->select('users.*', 'students.application_status', 'students.gender', 'students.created_at')
                ->orderBy('users.created_at', 'desc');

            if ($request->filled('customSearch')) {
                $searchTerm = $request->get('customSearch');
                $query->where(function ($query) use ($searchTerm, $currentLang) {
                    $query->whereHas('student', function ($query) use ($searchTerm, $currentLang) {
                        $query
                            ->where('name_' . ($currentLang == 'ar' ? 'ar' : 'en'), 'like', "%$searchTerm%")
                            ->orWhere('national_id', 'like', "%$searchTerm%")
                            ->orWhere('email', 'like', "%$searchTerm%")
                            ->orWhere('mobile', 'like', "%$searchTerm%");
                    });
                });
            }

            if ($request->filled('application_status')) {
                $status = $request->get('application_status');
                $query->whereIn('students.application_status', $status);
            }

            $totalRecords = $query->count();

            $filteredRecords = $query->count();

            $applicants = $query
                ->skip($request->get('start', 0))
                ->take($request->get('length', 10))
                ->get();

            return response()->json([
                'draw' => $request->get('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $applicants->map(function ($applicant) use ($currentLang) {
                    return [
                        'name' => $applicant->student->{'name_' . ($currentLang == 'ar' ? 'ar' : 'en')} ?? 'N/A',
                        'national_id' => $applicant->student->national_id ?? 'N/A',
                        'faculty' => $applicant->student->faculty->{'name_' . ($currentLang == 'ar' ? 'ar' : 'en')} ?? 'N/A',
                        'email' => $applicant->email ?? 'N/A',
                        'mobile' => $applicant->student->mobile ?? 'N/A',
                        'registration_date' => $applicant->created_at->format('F j, Y, g:i A'),
                        'actions' => '<button type="button" class="btn btn-round btn-info-rgba" data-applicant-id="' . $applicant->id . '" id="details-btn" title="More Details"><i class="feather icon-info"></i></button>',
                    ];
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching applicants data: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch applicants data.'], 500);
        }
    }

    /**
     * Fetch summary statistics for applicants.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchStats()
    {
        try {
            if (Gate::denies('is-admin')) {
                return response()->json(
                    [
                        'error' => 'Unauthorized access to statistics data.',
                    ],
                    403
                );
            }

            $applicants = User::role('resident')
                ->with(['student'])
                ->leftJoin('students', 'students.user_id', '=', 'users.id')
                ->select('users.*', 'students.application_status', 'students.gender')
                ->get();

            $totalApplicants = $applicants->count();
            $totalMaleCount = $applicants->where('gender', 'male')->count();
            $totalFemaleCount = $applicants->where('gender', 'female')->count();
            $totalPendingCount = $applicants->where('application_status', 'pending')->count();
            $totalPreliminaryAcceptedCount = $applicants->where('application_status', 'preliminary_accepted')->count();
            $totalFinalAcceptedCount = $applicants->where('application_status', 'final_accepted')->count();

            $malePreliminaryAcceptedCount = $applicants
                ->where('gender', 'male')
                ->where('application_status', 'preliminary_accepted')
                ->count();
            $malePendingCount = $applicants
                ->where('gender', 'male')
                ->where('application_status', 'pending')
                ->count();
            $maleFinalAcceptedCount = $applicants
                ->where('gender', 'male')
                ->where('application_status', 'final_accepted')
                ->count();

            $femalePreliminaryAcceptedCount = $applicants
                ->where('gender', 'female')
                ->where('application_status', 'preliminary_accepted')
                ->count();
            $femalePendingCount = $applicants
                ->where('gender', 'female')
                ->where('application_status', 'pending')
                ->count();
            $femaleFinalAcceptedCount = $applicants
                ->where('gender', 'female')
                ->where('application_status', 'final_accepted')
                ->count();

            return response()->json([
                'totalApplicants' => $totalApplicants,
                'totalMaleCount' => $totalMaleCount,
                'totalFemaleCount' => $totalFemaleCount,
                'totalPendingCount' => $totalPendingCount,
                'totalPreliminaryAcceptedCount' => $totalPreliminaryAcceptedCount,
                'totalFinalAcceptedCount' => $totalFinalAcceptedCount,
                'malePreliminaryAcceptedCount' => $malePreliminaryAcceptedCount,
                'malePendingCount' => $malePendingCount,
                'maleFinalAcceptedCount' => $maleFinalAcceptedCount,
                'femalePreliminaryAcceptedCount' => $femalePreliminaryAcceptedCount,
                'femalePendingCount' => $femalePendingCount,
                'femaleFinalAcceptedCount' => $femaleFinalAcceptedCount,
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching summary data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to fetch summary data.'], 500);
        }
    }

    /**
     * Fetch additional data for specific applicants.
     *
     * @param \Model\User
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchApplicantInfo($id)
    {
        try {
            $applicant = User::find($id);

            if (!$applicant) {
                return response()->json(['error' => 'Applicant not found', 'user' => $id], 404);
            }

            $details = [
                'faculty' => $applicant->student->faculty->name_en ?? 'Not available',
                'program' => $applicant->student->program->name_en ?? 'Not available',
                'score' => $applicant->student->universityArchive->score ?? 'Not available',
                'percent' => $applicant->student->universityArchive->percent ?? 'Not available',
                'governorate' => $applicant->student->governorate->name_ar ?? 'Not available',
                'city' => $applicant->student->city->name_ar ?? 'Not available',
                'street' => $applicant->student->street ?? 'Not available',
            ];

            return response()->json([
                'success' => true,
                'data' => $details,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching applicant details', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'applicant_id' => $id,
            ]);

            return response()->json(['error' => 'Applicant not found or an error occurred', 'user' => $id], 404);
        }
    }

    /**
     * Download applicants data as an Excel file.
     *
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadApplicantsExcel()
    {
        try {
            $export = new ApplicantsExport();
            return $export->downloadExcel();
        } catch (\Exception $e) {
            Log::error('Error exporting applicants to Excel', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to export applicants to Excel'], 500);
        }
    }

    /**
     * Download applicants data as a PDF file.
     *
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadApplicantsPDF()
    {
        try {
            $export = new ApplicantsExport();
            $pdf = $export->downloadPDF();
            return $pdf->download('applicants_report.pdf');
        } catch (\Exception $e) {
            Log::error('Error exporting applicants to PDF', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to export applicants to PDF'], 500);
        }
    }
}
