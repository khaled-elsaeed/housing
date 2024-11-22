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
            $applicants = $this->fetchApplicants();

            $totalApplicants = $applicants->count();
            $maleCount = $this->countByGender($applicants, 'male');
            $femaleCount = $this->countByGender($applicants, 'female');

            $totalPendingCount = $this->countByStatus($applicants, 'pending');
            $totalPreliminaryAcceptedCount = $this->countByStatus($applicants, 'preliminary_accepted');
            $totalFinalAcceptedCount = $this->countByStatus($applicants, 'final_accepted');

            $malePreliminaryAcceptedCount = $this->countByGenderAndStatus($applicants, 'male', 'preliminary_accepted');
            $malePendingCount = $this->countByGenderAndStatus($applicants, 'male', 'pending');
            $maleFinalAcceptedCount = $this->countByGenderAndStatus($applicants, 'male', 'final_accepted');

            $femaleFinalAcceptedCount = $this->countByGenderAndStatus($applicants, 'female', 'final_accepted');
            $femalePreliminaryAcceptedCount = $this->countByGenderAndStatus($applicants, 'female', 'preliminary_accepted');
            $femalePendingCount = $this->countByGenderAndStatus($applicants, 'female', 'pending');

            return view(
                'admin.applicant.view',
                compact(
                    'applicants',
                    'totalApplicants',
                    'maleCount',
                    'femaleCount',
                    'totalPendingCount',
                    'totalPreliminaryAcceptedCount',
                    'totalFinalAcceptedCount',
                    'malePreliminaryAcceptedCount',
                    'malePendingCount',
                    'maleFinalAcceptedCount',
                    'femalePreliminaryAcceptedCount',
                    'femalePendingCount',
                    'femaleFinalAcceptedCount'
                )
            );
        } catch (\Exception $e) {
            Log::error('Error displaying applicant page', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->view('errors.500', [], 500);
        }
    }

    private function countByGender($applicants, $gender)
    {
        return $applicants
            ->filter(function ($applicant) use ($gender) {
                return strtolower($applicant->gender) === strtolower($gender);
            })
            ->count();
    }

    private function countByStatus($applicants, $status)
    {
        return $applicants
            ->filter(function ($applicant) use ($status) {
                return strtolower(optional($applicant->student)->application_status) === strtolower($status);
            })
            ->count();
    }

    private function countByGenderAndStatus($applicants, $gender, $status)
    {
        return $applicants
            ->filter(function ($applicant) use ($gender, $status) {
                return strtolower($applicant->gender) === strtolower($gender) && strtolower(optional($applicant->student)->application_status) === strtolower($status);
            })
            ->count();
    }

    private function fetchApplicants()
    {
        try {
            return User::role('resident')
                ->with(['student'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($applicant) {
                    $applicant->has_student_profile = $applicant->hasStudentProfile();
                    $applicant->gender = optional($applicant->student)->gender ?? 'N/A';
                    return $applicant;
                });
        } catch (\Exception $e) {
            Log::error('Error fetching applicants', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return collect(); 
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

    public function getApplicantAcademicEmail($id)
    {
        try {
            $applicant = User::findOrFail($id);
            $academicEmail = $applicant->student->universityArchive->academic_email ?? null;
            $hasAcademicEmail = !is_null($academicEmail);

            return response()->json([
                'academic_email' => $academicEmail,
                'has_academic_email' => $hasAcademicEmail,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching applicant university email', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'applicant_id' => $id
            ]);
            return response()->json(['error' => 'Failed to fetch applicant email'], 500);
        }
    }

    public function updateApplicantEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                'email-option' => 'required|string|in:manual,university',
                'manual-email' => 'nullable|email',
                'university-email' => 'nullable|email',
                'applicant-id' => 'required|integer|exists:users,id',
            ]);

            $applicantId = $request->input('applicant-id');
            $applicant = User::findOrFail($applicantId);

            $newEmail = null;

            if ($request->input('email-option') == 'manual') {
                $newEmail = $request->input('manual-email');
            } elseif ($request->input('email-option') == 'university') {
                $newEmail = $request->input('university-email');
            }

            if (!$newEmail) {
                return response()->json(['message' => 'No email provided for the selected option'], 400);
            }

            $applicant->email = filter_var($newEmail, FILTER_SANITIZE_EMAIL);
            $applicant->save();

            return response()->json(['message' => 'Email updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error updating applicant email', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to update email'], 500);
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

