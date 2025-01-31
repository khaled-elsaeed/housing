<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\Faculty;
use App\Models\Parents;
use App\Models\Sibling;
use App\Models\Invoice;
use App\Models\EmergencyContact;
use App\Models\Program;
use App\Models\Governorate;
use App\Models\Notification;
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
     * @return \Illuminate\View\View
     */
    public function index()
{
    try {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', trans('Please login first'));
        }

        $user = Auth::user();
        $governorates = Governorate::all();
        $programs = Program::all();
        $countries = Country::all();
        $faculties = Faculty::all();

        // Get reservations and invoices
        $reservations = $this->getUserReservations($user);
        $invoices = $this->getUserInvoices($user);

        return view('student.profile', compact(
            'user',
            'reservations',
            'governorates',
            'programs',
            'countries',
            'faculties',
            'invoices'
        ));
    } catch (Exception $e) {
        Log::error('Error fetching data for student profile: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->view('errors.500', [], 500);
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
        try {
            if (!$user || !$user->exists) {
                Log::warning('Invalid user provided to getUserReservations');
                return collect();
            }

            $reservations = $user->reservations()
                ->with(['invoice', 'room'])
                ->orderBy('created_at', 'asc')
                ->get();

            if ($reservations->isEmpty()) {
                Log::info('No reservations found for user:', ['user_id' => $user->id]);
            }

            // Log reservations for debugging
            Log::debug('Fetched reservations for user:', [
                'user_id' => $user->id,
                'reservations_count' => $reservations->count()
            ]);

            return $reservations;
        } catch (Exception $e) {
            Log::error('Error fetching reservations: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            throw new Exception('Failed to retrieve reservations: ' . $e->getMessage());
        }
    }

    /**
     * Get the user's invoices.
     *
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    protected function getUserInvoices(User $user)
    {
        try {
            // Get invoices through reservations with eager loading
            $invoices = Invoice::whereHas('reservation', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['reservation'])->get();

            // Log for debugging
            Log::debug('Fetched invoices for user:', [
                'user_id' => $user->id,
                'invoices_count' => $invoices->count(),
                'invoices' => $invoices->toArray()
            ]);

            return $invoices;
        } catch (Exception $e) {
            Log::error('Error fetching invoices: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'exception' => $e,
            ]);

            return collect();
        }
    }

    /**
     * Update the student's basic profile information.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            $userId = Auth::user()->id;
            $user = User::find($userId);

            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $userId,
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $user->first_name_en = $request->input('first_name');
            $user->last_name_en = $request->input('last_name');
            $user->email = $request->input('email');

            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            $user->save();

            return redirect()
                ->route('student.profile')
                ->with('success', trans('Profile updated successfully'));
        } catch (Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage(), [
                'user_id' => Auth::user()->id,
                'exception' => $e,
            ]);
            return back()->with('error', trans('Error updating profile'));
        }
    }

    /**
     * Update the student's profile picture.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
 * Update the student's profile picture.
 *
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateProfilePicture(Request $request)
{
    try {
        // Validate the uploaded file
        $request->validate([
            'profile_picture' => 'required',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user already has a profile picture
        if ($user->media) {
            // Delete the old profile picture file from storage
            $this->uploadService->delete($user->media->path);

            // Delete the old Media record from the database
            $user->media->delete();

            $user->save();
        }

        // Store the new profile picture
        $profileImage = $this->storeProfileImage($request->file('profile_picture'));

        // Update the user's media_id to the new profile picture
        $user->media_id = $profileImage->id;
        $user->save();

            return response()->json([
                'success' => true,
                'message' => trans('Profile picture updated successfully'),
                
            ]);
        } catch (Exception $e) {
        Log::error('Error updating profile picture: ' . $e->getMessage(), [
            'user_id' => Auth::user()->id,
            'exception' => $e,
        ]);
        return back()->with('error', trans('Error updating profile picture'));
    }
}

    private function storeProfileImage($file)
    {
        if (!$file) {
            throw new \Exception("Profile image file is required.");
        }
        return $this->uploadService->upload($file, "profile_picture");
    }

    /**
     * Delete the student's profile picture.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteProfilePicture()
{
    try {
        if (!Auth::check()) {
            return back()->with('error', trans('Please login first'));
        }

        $user = Auth::user();
        $profilePicture = $user->profilePictureMedia()->first();

        if ($profilePicture) {
            // Delete the file from storage
            $this->uploadService->delete($profilePicture->path);

            // Delete the media record from the database
            $profilePicture->delete();

            return back()->with('success', trans('Profile picture deleted successfully'));
        }

        return back()->with('error', trans('No profile picture found'));
    } catch (Exception $e) {
        Log::error('Error deleting profile picture: ' . $e->getMessage(), [
            'user_id' => Auth::id(),
            'exception' => $e,
        ]);
        return back()->with('error', trans('Error deleting profile picture'));
    }
}

}