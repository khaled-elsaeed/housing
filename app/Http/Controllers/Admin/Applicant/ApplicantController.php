<?php

namespace App\Http\Controllers\Admin\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Exports\Applicants\ApplicantsExport;
use Illuminate\Support\Facades\Log;

class ApplicantController extends Controller
{
    public function index()
    {
        try {
            $applicants = User::role('resident')
                ->with(['student'])
                ->leftJoin('students', 'students.user_id', '=', 'users.id')
                ->orderBy('users.created_at', 'desc')
                ->select('users.*', 'students.application_status')
                ->get()
                ->map(function ($applicant) {
                    $applicant->has_student_profile = $applicant->hasStudentProfile();
                    return $applicant;
                });

            $totalApplicants = $applicants->count();
            $totalMaleCount = $applicants->where('gender', 'male')->count();
            $totalFemaleCount = $applicants->where('gender', 'female')->count();
            $totalPendingCount = $applicants->where('application_status', 'pending')->count();
            $totalPreliminaryAcceptedCount = $applicants->where('application_status', 'preliminary_accepted')->count();
            $totalFinalAcceptedCount = $applicants->where('application_status', 'final_accepted')->count();

            $malePreliminaryAcceptedCount = $applicants->where('gender', 'male')->where('application_status', 'preliminary_accepted')->count();
            $malePendingCount = $applicants->where('gender', 'male')->where('application_status', 'pending')->count();
            $maleFinalAcceptedCount = $applicants->where('gender', 'male')->where('application_status', 'final_accepted')->count();

            $femalePreliminaryAcceptedCount = $applicants->where('gender', 'female')->where('application_status', 'preliminary_accepted')->count();
            $femalePendingCount = $applicants->where('gender', 'female')->where('application_status', 'pending')->count();
            $femaleFinalAcceptedCount = $applicants->where('gender', 'female')->where('application_status', 'final_accepted')->count();

            $filteredApplicants = $applicants->whereIn('application_status', ['pending', 'preliminary_accepted']);

            return view(
                'admin.applicant.view',
                compact(
                    'applicants',
                    'totalApplicants',
                    'totalMaleCount',
                    'totalFemaleCount',
                    'totalPendingCount',
                    'totalPreliminaryAcceptedCount',
                    'totalFinalAcceptedCount',
                    'malePreliminaryAcceptedCount',
                    'malePendingCount',
                    'maleFinalAcceptedCount',
                    'femalePreliminaryAcceptedCount',
                    'femalePendingCount',
                    'femaleFinalAcceptedCount',
                    'filteredApplicants'
                )
            );
        } catch (Exception $e) {
            Log::error('Error retrieving applicant page data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->view('error.page_init');
        }
    }

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

    public function getApplicantMoreDetails($id)
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
                'data' => $details
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching applicant details', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'applicant_id' => $id
            ]);

            return response()->json(['error' => 'Applicant not found or an error occurred', 'user' => $id], 404);
        }
    }
}
