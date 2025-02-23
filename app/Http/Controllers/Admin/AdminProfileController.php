<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Notification;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Log;
use App\Contracts\UploadServiceContract;

class AdminProfileController extends Controller
{
    private $uploadService;

    /**
     * Constructor for AdminProfileController.
     *
     * @param UploadServiceContract $uploadService
     */
    public function __construct(UploadServiceContract $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Display the admin profile page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $user = User::find($user->id);

        // Retrieve all notifications for the authenticated user
        $notifications = $user->notifications;

        return view('admin.profile', compact('user', 'notifications'));
    }

    /**
     * Update the admin's basic profile information.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $userId = Auth::user()->id;
            $user = User::find($userId);

            // Validate the incoming request fields
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $userId,
                'password' => 'nullable|confirmed', // Ensure the password confirmation field is checked if password is provided
            ]);

            // Check if the password is valid (if it's provided)
            if ($request->filled('password')) {
                $validPassword = $this->validatePassword($request->input('password'));

                if ($validPassword) {
                    return response()->json([
                        'success' => false,
                        'message' => $validPassword,
                    ], 422);
                }
            }

            // Proceed with updating the user profile
            $user->first_name_en = $request->input('first_name');
            $user->last_name_en = $request->input('last_name');
            $user->email = $request->input('email');

            // Update password if it's provided and valid
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            // Save the updated user
            $user->save();

            // Log user activity
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
        } catch (\Exception $e) {
            Log::error('Error updating profile', [
                'error' => $e->getMessage(),
                'user_id' => Auth::user()->id,
                'action' => 'update_profile_data',
            ]);

            return response()->json([
                'success' => false,
                'message' => trans('Error updating profile'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Custom function to validate password strength.
     *
     * @param string $password
     * @return string|null
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
     * Update the admin's profile picture.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePicture(Request $request)
{
    try {
        // Validate the uploaded file
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096', // Max 4MB
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
    } catch (\Exception $e) {
        Log::error('Error updating profile picture', [
            'error' => $e->getMessage(),
            'user_id' => Auth::user()->id,
            'action' => 'update_profile_picture',
        ]);

        return response()->json([
            'success' => false,
            'message' => trans('Error updating profile picture'),
            'error' => $e->getMessage(),
        ], 500);
    }
}



    /**
     * Delete the admin's profile picture.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProfilePicture()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('Please login first'),
                ], 401);
            }

            $user = Auth::user();
            $profilePicture = $user->profilePictureMedia()->first();

            if ($profilePicture) {
                $this->uploadService->delete($profilePicture->path);
                $profilePicture->delete();

                // Log user activity
                UserActivity::create([
                    'user_id' => $user->id,
                    'activity_type' => 'delete_profile_picture',
                    'description' => 'Delete profile picture',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => trans('Profile picture deleted successfully'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => trans('No profile picture found'),
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting profile picture', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'action' => 'delete_profile_picture',
            ]);

            return response()->json([
                'success' => false,
                'message' => trans('Error deleting profile picture'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}