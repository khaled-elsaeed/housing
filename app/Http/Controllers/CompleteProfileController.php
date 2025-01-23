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
use App\Http\Requests\CompleteProfileRequest;

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

    public function store(CompleteProfileRequest $request)
    {
        try {
            // Get validated data
            $validatedData = $request->validated();

            // Get authenticated user
            $user = auth()->user();

            // Store profile data using service
            $result = $this->completeProfileService->storeProfileData($user, $validatedData);

            if (!$result['success']) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => $result['message']]);
            }

            // Mark profile as completed
            $user->update(['profile_completed' => true]);

            // Redirect with success message
            return redirect()
                ->route('student.home')
                ->with('success', __('Profile completed successfully'));

        } catch (\Exception $e) {
            \Log::error('Profile completion error: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => __('An error occurred while completing your profile')]);
        }
    }
}
