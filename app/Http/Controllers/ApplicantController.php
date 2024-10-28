<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Exports\ApplicantsExport;

class ApplicantController extends Controller
{
    // Show the applicant page with statistics
    public function showApplicantPage()
    {
        $applicants = $this->getApplicants();

        $totalApplicants = $applicants->count();
        $maleCount = $this->countGender($applicants, 'male');
        $femaleCount = $this->countGender($applicants, 'female');

        // Count application statuses
        $totalPendingCount = $this->countStatus($applicants, 'pending');
        $totalPreliminaryAcceptedCount = $this->countStatus($applicants, 'preliminary_accepted');
        $totalFinalAcceptedCount = $this->countStatus($applicants, 'final_accepted');

        // Count gender-specific application statuses
        $malePreliminaryAcceptedCount = $this->countGenderStatus($applicants, 'male', 'preliminary_accepted');
        $malePendingCount = $this->countGenderStatus($applicants, 'male', 'pending');
        $maleFinalAcceptedCount = $this->countGenderStatus($applicants, 'male', 'final_accepted');

        $femaleFinalAcceptedCount = $this->countGenderStatus($applicants, 'female', 'final_accepted');
        $femalePreliminaryAcceptedCount = $this->countGenderStatus($applicants, 'female', 'preliminary_accepted');
        $femalePendingCount = $this->countGenderStatus($applicants, 'female', 'pending');

        return view('admin.applicant.view', compact(
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
        ));
    }

    public function showInvoicePage(){
        return view('admin.applicant.invoice');
    }

    // Fetch applicants from the database
    private function getApplicants()
    {
        return User::role('resident')
                   ->with('universityArchive', 'student')
                   ->get()
                   ->map(function ($applicant) {
                       $applicant->has_student_profile = $applicant->hasStudentProfile();
                       $applicant->gender = optional($applicant->universityArchive)->gender ?? 'N/A';
                       return $applicant;
                   });
    }

    // Count the number of applicants by gender
    private function countGender($applicants, $gender)
    {
        return $applicants->filter(function ($applicant) use ($gender) {
            return strtolower($applicant->gender) === strtolower($gender);
        })->count();
    }

    // Count the number of applicants by application status
    private function countStatus($applicants, $status)
    {
        return $applicants->filter(function ($applicant) use ($status) {
            return strtolower(optional($applicant->student)->application_status) === strtolower($status);
        })->count();
    }

    // Count the number of applicants by gender and application status
    private function countGenderStatus($applicants, $gender, $status)
    {
        return $applicants->filter(function ($applicant) use ($gender, $status) {
            return strtolower($applicant->gender) === strtolower($gender) && 
                   strtolower(optional($applicant->student)->application_status) === strtolower($status);
        })->count();
    }

    // Download applicants' data as an Excel file
    public function downloadExcel()
    {
        $export = new ApplicantsExport();
        return $export->downloadExcel();
    }

    // Download applicants' data as a PDF file
    public function downloadPDF()
    {
        $export = new ApplicantsExport();
        $pdf = $export->downloadPDF();
        return $pdf->download('applicants_report.pdf');
    }
}
