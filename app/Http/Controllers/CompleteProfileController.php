<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Program;
use App\Models\Country;
use App\Services\CompleteProfileService;

class CompleteProfileController extends Controller
{

    protected $completeProfileService;

    public function __construct(completeProfileService $completeProfileService)
    {
        $this->completeProfileService = $completeProfileService;
    }
    /**
     * Show the Complete Profile form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $profileData = $this->completeProfileService->getUserProfileData($user);
        $formData = $this->getFormData();
        

        return view('student.complete-profile', compact('profileData', 'formData'));
    }

    private function getFormData()
    {
        $formData = [
            'faculties' => Faculty::all(),
            'governorates' => Governorate::all(),
            'cities' => City::all(),
            'programs' => Program::all(),
            'countries' => Country::all(),
        ];
    
        return $formData;
    }
    

}
