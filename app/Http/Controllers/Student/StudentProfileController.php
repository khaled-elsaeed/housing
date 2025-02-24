<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, DB};
use App\Models\{User, Governorate, Program, Country, Faculty, Invoice, UserActivity};
use App\Contracts\UploadServiceContract;
use Exception;

class StudentProfileController extends Controller
{
    private $uploadService;

    public function __construct(UploadServiceContract $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Show the student's profile page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $governorates = Governorate::all();
            $programs = Program::all();
            $countries = Country::all();
            $faculties = Faculty::all();

            $reservations = $this->getUserReservations($user);
            $invoices = $this->getUserInvoices($user);

            return view(
                'student.profile',
                compact('user', 'reservations', 'governorates', 'programs', 'countries', 'faculties', 'invoices')
            );
        } catch (Exception $e) {
            logError('Failed to load user profile page', 'show_user_profile_page', $e);
            return response()->view('errors.500');
        }
    }

    /**
     * Get the user's reservations.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws Exception
     */
    private function getUserReservations(User $user)
    {
        if (!$user || !$user->exists) {
            logError('Invalid user provided to getUserReservations', 'get_user_reservations', new Exception('User not found'));
            return collect();
        }

        try {
            return $user->reservations()
                ->with(['invoice', 'room'])
                ->orderBy('created_at', 'asc')
                ->get();
        } catch (Exception $e) {
            logError('Failed to get user reservations', 'get_user_reservations', $e);
            throw $e;
        }
    }

    /**
     * Get the user's invoices.
     *
     * @param User $user
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    private function getUserInvoices(User $user)
    {
        if (!$user || !$user->exists) {
            logError('Invalid user provided to getUserInvoices', 'get_user_invoices', new Exception('User not found'));
            return collect();
        }

        try {
            $invoices = Invoice::whereHas('reservation', fn($query) => $query->where('user_id', $user->id))
                ->with(['reservation'])
                ->get();

            return $invoices ?: collect();
        } catch (Exception $e) {
            logError('Failed to get user invoices', 'get_user_invoices', $e);
            throw $e;
        }
    }

    /**
     * Update the student's basic profile information.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $user = User::findOrFail(Auth::id());

            $request->validate([
                'first_name_en' => 'required|string|max:255',
                'last_name_en' => 'required|string|max:255',
                'first_name_ar' => 'required|string|max:255',
                'last_name_ar' => 'required|string|max:255',
                'email' => "required|email|unique:users,email,{$user->id}",
                'password' => 'nullable|confirmed|min:8',
            ]);

            if ($request->filled('password')) {
                if ($error = $this->validatePassword($request->input('password'))) {
                    return errorResponse($error, 422);
                }
            }

            DB::beginTransaction();
            $updateData = [
                'first_name_en' => $request->input('first_name_en', $user->first_name_en),
                'last_name_en' => $request->input('last_name_en', $user->last_name_en),
                'first_name_ar' => $request->input('first_name_ar', $user->first_name_ar),
                'last_name_ar' => $request->input('last_name_ar', $user->last_name_ar),
                'email' => $request->input('email', $user->email),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->input('password'));
            }

            $user->update($updateData);
            
            userActivity($user->id, 'update_profile', 'Updated profile information');

            DB::commit();

            return successResponse(trans('Profile updated successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            logError('Error updating profile', 'update_profile_data', $e);
            return errorResponse(trans('Error updating profile'), 500);
        }
    }

    /**
     * Custom function to validate password strength.
     *
     * @param string $password
     * @return string|null
     */
    private function validatePassword(string $password): ?string
    {
        if (strlen($password) < 8) {
            return 'The password must be at least 8 characters long.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return 'The password must contain at least one uppercase letter.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            return 'The password must contain at least one lowercase letter.';
        }
        if (!preg_match('/\d/', $password)) {
            return 'The password must contain at least one number.';
        }
        if (!preg_match('/[@$!%*?&]/', $password)) {
            return 'The password must contain at least one special character (e.g., @, $, !, %, *, ?, &).';
        }
        return null;
    }

    /**
     * Update the student's profile picture.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePicture(Request $request)
    {
        try {
            $request->validate(['profile_picture' => 'required|image|max:5120']);
            $user = Auth::user();

            DB::beginTransaction();
            if ($profilePicture = $user->profilePictureMedia()->first()) {
                $this->uploadService->delete($profilePicture);
            }

            $photo = $request->file('profile_picture');
            $this->uploadService->upload($photo, 'profile_picture', $user);
            
            userActivity($user->id, 'update_profile_picture', 'Updated profile picture');

            DB::commit();

            return successResponse(trans('Profile picture updated successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            logError('Error updating profile picture', 'update_profile_picture', $e);
            return errorResponse(trans('Error updating profile picture'), 500);
        }
    }

    /**
     * Delete the student's profile picture.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProfilePicture()
    {
        try {
            $user = Auth::user();
            $profilePicture = $user->profilePictureMedia()->first();

            if (!$profilePicture) {
                return errorResponse(trans('No profile picture found'), 404);
            }

            DB::beginTransaction();
            $this->uploadService->delete($profilePicture);

            userActivity($user->id, 'delete_profile_picture', 'Deleted profile picture');

            DB::commit();

            return successResponse(trans('Profile picture deleted successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            logError('Error deleting profile picture', 'delete_profile_picture', $e);
            return errorResponse(trans('Error deleting profile picture'), 500);
        }
    }
}