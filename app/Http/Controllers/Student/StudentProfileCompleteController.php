<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteProfileRequest;
use App\Models\{Faculty, Governorate, City, Program, Country};
use App\Services\CompleteProfileService;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class StudentProfileCompleteController extends Controller
{
    protected $completeProfileService;

    public function __construct(CompleteProfileService $completeProfileService)
    {
        $this->completeProfileService = $completeProfileService;
    }

    /**
     * Display the profile completion form.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            $user = auth()->user();
            $profileData = $this->completeProfileService->getUserProfileData($user);
            $formData = $this->getFormData();

            return view('student.complete-profile', compact('profileData', 'formData'));
        } catch (Throwable $e) {
            logError('Failed to load complete profile page', 'show_user_profile_page', $e);
            return redirect()->back()->withErrors([
                'error' => __('Unable to load the profile page. Please try again later.'),
            ]);
        }
    }

    /**
     * Store the profile completion data.
     *
     * @param CompleteProfileRequest $request
     * @return JsonResponse
     */
    public function store(CompleteProfileRequest $request)
    {
        try {
            $user = auth()->user();
            $this->completeProfileService->storeProfileData($user, $request->validated());
            return successResponse('Your registration has been completed successfully.',route('student.home'));
        } catch (Throwable $e) {
            logError('Error completing profile data', 'complete_user_profile', $e);
            return errorResponse(__('An error occurred while completing your profile.'), 500);
        }
    }

    /**
     * Retrieve form data for the profile completion form.
     *
     * @return array
     */
    private function getFormData(): array
    {
        try {
            return [
                'faculties' => Faculty::all(),
                'governorates' => Governorate::all(),
                'cities' => City::all(),
                'programs' => Program::all(),
                'countries' => Country::all(),
            ];
        } catch (Throwable $e) {
            logError('Failed to retrieve form data', 'get_form_data', $e);
            return array_fill_keys(['faculties', 'governorates', 'cities', 'programs', 'countries'], []);
        }
    }

   

    
}