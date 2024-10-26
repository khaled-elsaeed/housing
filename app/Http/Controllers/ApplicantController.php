<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Exports\ApplicantsExport;

class ApplicantController extends Controller
{
    public function showApplicantPage()
    {
        $applicants = $this->getApplicants();

        $totalApplicants = $applicants->count();
        $maleCount = $this->countGender($applicants, 'male');
        $femaleCount = $this->countGender($applicants, 'female');
        $occupancyCount = $this->calculateOccupancy($totalApplicants, $maleCount, $femaleCount);

        $totalPendingCount = $this->countStatus($applicants, 'pending');
        $totalPreliminaryAcceptedCount = $this->countStatus($applicants, 'preliminary_accepted');
        $totalFinalAcceptedCount = $this->countStatus($applicants, 'final_accepted');


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
            'occupancyCount',
            'totalPreliminaryAcceptedCount',   
            'totalPendingCount',
            'totalFinalAcceptedCount',
            'malePreliminaryAcceptedCount',   
            'malePendingCount',    
            'femalePreliminaryAcceptedCount', 
            'femalePendingCount',
            'maleFinalAcceptedCount',
            'femaleFinalAcceptedCount'
        ));
    }

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

    private function countGender($applicants, $gender)
    {
        return $applicants->filter(function ($applicant) use ($gender) {
            return strtolower($applicant->gender) === strtolower($gender);
        })->count();
    }

    private function calculateOccupancy($total, $maleCount, $femaleCount)
    {
        return $total > 0 ? ($maleCount + $femaleCount) : 0;
    }

    private function countStatus($applicants, $status)
    {
        return $applicants->filter(function ($applicant) use ($status) {
            return strtolower(optional($applicant->student)->application_status) === strtolower($status);
        })->count();
    }

    private function countGenderStatus($applicants, $gender, $status)
    {
        return $applicants->filter(function ($applicant) use ($gender, $status) {
            return strtolower($applicant->gender) === strtolower($gender) && 
                   strtolower(optional($applicant->student)->application_status) === strtolower($status);
        })->count();
    }

    public function export()
    {
        $export = new ApplicantsExport();
        return $export->download();
    }
    
}
