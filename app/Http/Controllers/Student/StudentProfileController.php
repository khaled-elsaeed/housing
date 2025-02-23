<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage, Log, Hash};
use App\Models\{User, Notification, Governorate, Program, EmergencyContact, Invoice, Sibling, Parents, Faculty, Country, City, UserActivity};
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $governorates = Governorate::all();
            $programs = Program::all();
            $countries = Country::all();
            $faculties = Faculty::all();

            // Get reservations and invoices
            $reservations = $this->getUserReservations($user);
            $invoices = $this->getUserInvoices($user);

            return view('student.profile', compact('user', 'reservations', 'governorates', 'programs', 'countries', 'faculties', 'invoices'));
        } catch (Exception $e) {
            Log::error('Failed to load user profile page', [
                'error' => $e->getMessage(),
                'action' => 'show_user_profile_page',
                'user_id' => auth()->id(),
            ]);
            return response()->view('errors.500');
        }
    }

    /**
     * Get the user's reservations.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUserReservations(User $user)
    {
        if (!$user || !$user->exists) {
            Log::warning('Invalid user provided to getUserReservations', [
                'user_id' => $user->id ?? null,
            ]);
            return collect();
        }

        $reservations = $user
            ->reservations()
            ->with(['invoice', 'room'])
            ->orderBy('created_at', 'asc')
            ->get();

        return $reservations;
    }

    /**
     * Get the user's invoices.
     *
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    protected function getUserInvoices(User $user)
    {
        if (!$user || !$user->exists) {
            Log::warning('Invalid user provided to getUserReservations', [
                'user_id' => $user->id ?? null,
            ]);
            return collect();
        }

        // Get invoices through reservations with eager loading
        $invoices = Invoice::whereHas('reservation', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['reservation'])
            ->get();

        if (!$invoices) {
            return collect();
        }
        return $invoices;
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
            $userId = Auth::user()->id;

            $user = User::find($userId);

            $request->validate([
                'first_name_en' => 'required|string|max:255',
                'last_name_en' => 'required|string|max:255',
                'first_name_ar' => 'required|string|max:255',
                'last_name_ar' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|confirmed|min:8',
            ]);

            // Check if the password is valid (if it's provided)
            if ($request->filled('password')) {
                $validPassword = $this->validatePassword($request->input('password'));

                if ($validPassword) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => $validPassword,
                        ],
                        422
                    );
                }
            }

            // Prepare the data
            $updateData = [
                'first_name_en' => $validatedData['first_name_en'] ?? $user->first_name_en,
                'last_name_en' => $validatedData['last_name_en'] ?? $user->last_name_en,
                'first_name_ar' => $validatedData['first_name_ar'] ?? $user->first_name_ar,
                'last_name_ar' => $validatedData['last_name_ar'] ?? $user->last_name_ar,
                'email' => $validatedData['email'] ?? $user->email,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validatedData['password']);
            }

            // Update the user with the prepared data
            $user->update($updateData);

            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'update_profile',
                'description' => 'Updated profile information',
            ]);

            return response()->json([
                'success' => true,
                'message' => trans('Profile updated successfully'),
                'data' => $user,
            ]);
        } catch (Exception $e) {
            Log::error('Error updating profile', [
                'error' => $e->getMessage(),
                'user_id' => Auth::user()->id,
                'action' => 'update_profile_data',
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => trans('Error updating profile'),
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Custom function to validate password strength.
     */
    private function validatePassword($password)
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

        return null; // No error, password is valid
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
            // Validate the uploaded file
            $request->validate([
                'profile_picture' => 'required',
            ]);

            $user = Auth::user();

            if ($profilePicture = $user->profilePictureMedia()->first()) {
                $this->uploadService->delete($profilePicture->path);
                $profilePicture->delete();
            }

            // Upload the new profile picture
            $photo = $request->file('profile_picture');
            $this->uploadService->upload($photo, 'profile_picture', $user);

            // Log user activity
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'update_profile_picture',
                'description' => 'Updated profile picture',
            ]);

            return response()->json([
                'success' => true,
                'message' => trans('Profile picture updated successfully'),
            ]);
        } catch (Exception $e) {
            Log::error('Error updating profile picture', [
                'error' => $e->getMessage(),
                'user_id' => Auth::user()->id,
                'action' => 'update_profile_picture',
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => trans('Error updating profile picture'),
                    'error' => $e->getMessage(),
                ],
                500
            );
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
            if (!Auth::check()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => trans('Please login first'),
                    ],
                    401
                );
            }

            $user = Auth::user();
            $profilePicture = $user->profilePictureMedia()->first();

            if ($profilePicture) {
                $this->uploadService->delete($profilePicture->path);
                $profilePicture->delete();
                return response()->json([
                    'success' => true,
                    'message' => trans('Profile picture deleted successfully'),
                ]);
            }

            // Log user activity
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'delete_profile_picture',
                'description' => 'Delete profile picture',
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => trans('No profile picture found'),
                ],
                404
            );
        } catch (Exception $e) {
            Log::error('Error deleting profile picture', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'action' => 'delete_profile_picture',
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => trans('Error deleting profile picture'),
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }
}
