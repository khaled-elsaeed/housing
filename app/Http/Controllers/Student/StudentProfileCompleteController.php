<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Program;
use App\Models\Country;
use App\Services\CompleteProfileService;
use App\Http\Requests\CompleteProfileRequest;
use App\Http\Controllers\Controller;

class StudentProfileCompleteController extends Controller
{
    protected $completeProfileService;

    public function __construct(CompleteProfileService $completeProfileService)
    {
        $this->completeProfileService = $completeProfileService;
    }

    /**
     * Show the Complete Profile form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $user = auth()->user();

            \Log::info('Loading complete profile page for user', [
                'user_id' => $user->id ?? 'Guest',
                'email' => $user->email ?? 'No Email',
                'name' => $user->name ?? 'No Name',
            ]);

            $profileData = $this->completeProfileService->getUserProfileData($user);
            $formData = $this->getFormData();

            return view('student.complete-profile', compact('profileData', 'formData'));
        } catch (\Exception $e) {
            Log::error('Failed to load complete profile page', [
                'error' => $e->getMessage(),
                'action' => 'show_user_profile_page',
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->withErrors([
                    'error' => __('Unable to load the profile page. Please try again later.'),
                ]);
        }
    }

    /**
     * Retrieve form data for the profile completion form.
     *
     * @return array
     */
    private function getFormData()
    {
        return [
            'faculties' => Faculty::all(),
            'governorates' => Governorate::all(),
            'cities' => City::all(),
            'programs' => Program::all(),
            'countries' => Country::all(),
        ];
    }

    /**
     * Store the profile completion data.
     *
     * @param  \App\Http\Requests\CompleteProfileRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(CompleteProfileRequest $request)
    {
        try {
            // Get validated data
            $validatedData = $request->validated();

            // Get authenticated user
            $user = auth()->user();

            // Store profile data using the service
            $result = $this->completeProfileService->storeProfileData($user, $validatedData);

            if (!$result['success']) {
                \Log::error('Profile completion failed for user ID ' . $user->id . ': ' . $result['message']);

                return response()->json(
                    [
                        'success' => false,
                        'message' => __('An error occurred while completing your profile.'),
                    ],
                    400
                );
            }

            // Mark profile as completed
            $user->update(['profile_completed' => true]);

            return response()->json(
                [
                    'success' => true,
                    'message' => __('Your registration has been completed successfully.'),
                    'redirect' => route('student.home'),
                ],
                200
            );
        } catch (\Throwable $e) {
            Log::error('Error complete profile data', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'action' => 'complete_user_profile',
            ]);
            return response()->json(
                [
                    'success' => false,
                    'message' => __('An error occurred while completing your profile.'),
                ],
                500
            );
        }
    }
}
