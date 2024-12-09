<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\Faculty;
use App\Models\Parents;
use App\Models\Sibling;
use App\Models\EmergencyContact;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Program;
use App\Models\Governorate;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;

class StudentProfileController extends Controller
{
    /**
     * Show the student's profile page.
     */
    public function index()
    {
        $user = Auth::user();
        $governorates = Governorate::all();
        $programs = Program::all();
        $countries = Country::all();
        $faculties = Faculty::all();
        return view('student.profile', compact('user', 'governorates', 'programs', 'countries', 'faculties'));
    }

    /**
     * Update the student's basic profile information.
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
        
            return redirect()->route('student.profile')->with('success', 'Profile updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage(), [
                'user_id' => Auth::user()->id,
                'exception' => $e,
            ]);
            return back()->with('error', 'An error occurred while updating your profile. Please try again later.');
        }
    }

    /**
     * Update the student's profile picture.
     */
    public function updateProfilePicture(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $user = Auth::user();
        
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete('profile_pictures/' . $user->profile_picture);
            }
        
            $profilePicture = $request->file('profile_picture');
            $imageName = 'user_' . $user->id . '.' . $profilePicture->getClientOriginalExtension();
            $contents = file_get_contents($request->file('profile_picture'));
            Storage::disk('public')->put('profile_pictures/' . $imageName, $contents);
        
            $user->profile_picture = $imageName;
            $user->save();
        
            return back()->with('success', 'Profile picture updated successfully!');
        } catch (Exception $e) {
            Log::error('Error updating profile picture: ' . $e->getMessage(), [
                'user_id' => Auth::user()->id,
                'exception' => $e,
            ]);
            return back()->with('error', 'An error occurred while updating your profile picture. Please try again later.');
        }
    }

    /**
     * Delete the student's profile picture.
     */
    public function deleteProfilePicture()
    {
        try {
            $user = Auth::user();
        
            if ($user->profile_picture) {
                Storage::disk('public')->delete('profile_pictures/' . $user->profile_picture);
                $user->profile_picture = null;
                $user->save();
        
                return back()->with('success', 'Profile picture deleted successfully!');
            }
        
            return back()->with('error', 'No profile picture to delete.');
        } catch (Exception $e) {
            Log::error('Error deleting profile picture: ' . $e->getMessage(), [
                'user_id' => Auth::user()->id,
                'exception' => $e,
            ]);
            return back()->with('error', 'An error occurred while deleting your profile picture. Please try again later.');
        }
    }

    /**
     * Fetch cities based on the selected governorate.
     */
    public function getCitiesByGovernorate($governorate_id)
    {
        try {
            $cities = City::where('governorate_id', $governorate_id)->get();
            return response()->json($cities);
        } catch (Exception $e) {
            Log::error('Error fetching cities: ' . $e->getMessage(), [
                'governorate_id' => $governorate_id,
                'exception' => $e,
            ]);
            return response()->json(['error' => 'Could not fetch cities.'], 500);
        }
    }

    /**
     * Get programs associated with a specific faculty.
     */
    public function getProgramsForFaculty($facultyId)
    {
        try {
            $programs = Program::where('faculty_id', $facultyId)->get();
            return response()->json($programs);
        } catch (Exception $e) {
            Log::error('Error fetching programs for faculty: ' . $e->getMessage(), [
                'faculty_id' => $facultyId,
                'exception' => $e,
            ]);
            return response()->json(['error' => 'Could not fetch programs.'], 500);
        }
    }

    /**
     * Update the student's address information.
     */
    public function updateAddress(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'governorate_id' => 'required|exists:governorates,id',
                'city_id' => 'required|exists:cities,id',
                'street' => 'nullable|string|max:255',
            ]);
    
            $user = auth()->user();
    
            if ($user->student) {
                // Update the student's address
                $user->student->update([
                    'governorate_id' => $request->governorate_id,
                    'city_id' => $request->city_id,
                    'street' => $request->street,
                ]);
            }
    
            return redirect()->back()->with('success', 'Address updated successfully.');
        } catch (Exception $e) {
            // Log the error message for debugging
            Log::error('Error updating address: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return back()->with('error', 'An error occurred while updating your address. Please try again later.');
        }
    }
    
    /**
     * Update the student's academic information.
     */
    public function updateAcademicInfo(Request $request)
    {
        try {
            // Validate the input data
            $validatedData = $request->validate([
                'faculty_id' => 'required|exists:faculties,id',
                'program_id' => 'required|exists:programs,id',
            ]);
        
            // Get the currently authenticated user
            $user = auth()->user();
        
            // Check if the student exists
            if ($user->student) {
                // Update the academic info
                $user->student->update([
                    'faculty_id' => $request->faculty_id,  // Fix: Use faculty_id
                    'program_id' => $request->program_id,  // Fix: Use program_id
                ]);
            }
        
            // Redirect with success message
            return redirect()->back()->with('success', 'Academic info updated successfully.');
        } catch (Exception $e) {
            // Log the error and return an error message
            Log::error('Error updating academic info: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return back()->with('error', 'An error occurred while updating your academic information. Please try again later.');
        }
    }
    
    /**
     * Update or create the student's parent information.
     */
    public function updateParentInfo(Request $request)
    {
        try {
            $request->validate([
                'parent_name' => 'required|string|max:255',
                'parent_relation' => 'required|string',
                'parent_email' => 'nullable|email',
                'parent_mobile' => 'nullable|string|max:25',
                'parent_living_abroad' => 'required|boolean',
                'parent_abroad_country_id' => 'nullable|exists:countries,id',
                'parent_living_with' => 'nullable|string',
                'parent_governorate_id' => 'nullable|exists:governorates,id',
                'parent_city_id' => 'nullable|exists:cities,id',
            ]);

            $parent = $request->user()->parent;

            if ($parent) {
                $parent->update([
                    'name' => $request->input('parent_name'),
                    'relation' => $request->input('parent_relation'),
                    'email' => $request->input('parent_email'),
                    'mobile' => $request->input('parent_mobile'),
                    'living_abroad' => $request->input('parent_living_abroad'),
                    'abroad_country_id' => $request->input('parent_abroad_country_id'),
                    'living_with' => $request->input('parent_living_with'),
                    'governorate_id' => $request->input('parent_governorate_id'),
                    'city_id' => $request->input('parent_city_id'),
                ]);
            } else {
                Parents::create([
                    'user_id' => $request->user()->id,
                    'name' => $request->input('parent_name'),
                    'relation' => $request->input('parent_relation'),
                    'email' => $request->input('parent_email'),
                    'mobile' => $request->input('parent_mobile'),
                    'living_abroad' => $request->input('parent_living_abroad'),
                    'abroad_country_id' => $request->input('parent_abroad_country_id'),
                    'living_with' => $request->input('parent_living_with'),
                    'governorate_id' => $request->input('parent_governorate_id'),
                    'city_id' => $request->input('parent_city_id'),
                ]);
            }

            return redirect()->back()->with('success', 'Parent information updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating parent info: ' . $e->getMessage(), [
                
                'exception' => $e,
            ]);
            return redirect()->back()->with('error', 'An error occurred while updating parent information. Please try again later.');
        }
    }

    // Update or create sibling information
    public function updateOrCreateSiblingInfo(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'sibling_gender' => 'required|string',
                'sibling_name' => 'required|string|max:255',
                'sibling_national_id' => 'required|string|max:255',
                'sibling_faculty' => 'nullable|exists:faculties,id',
            ]);

            // Get the authenticated user
            $user = Auth::user();

            // Check if the user already has sibling info, if not create it
            if ($user->sibling) {
                // Update existing sibling info
                $user->sibling->update([
                    'gender' => $validatedData['sibling_gender'],
                    'name' => $validatedData['sibling_name'],
                    'national_id' => $validatedData['sibling_national_id'],
                    'faculty_id' => $validatedData['sibling_faculty'] ?? null,
                ]);
            } else {
                // Create new sibling info
                $user->sibling()->create([
                    'gender' => $validatedData['sibling_gender'],
                    'name' => $validatedData['sibling_name'],
                    'national_id' => $validatedData['sibling_national_id'],
                    'faculty_id' => $validatedData['sibling_faculty'] ?? null,
                ]);
            }

            // Success response
            return redirect()->back()->with('success', 'Sibling info updated successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating sibling info: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Return an error response
            return back()->with('error', 'An error occurred while updating sibling information. Please try again later.');
        }
    }

    // Controller method to update or create the emergency contact
public function updateOrCreateEmergencyInfo(Request $request)
{
    // Validate the incoming data
    $validatedData = $request->validate([
        'emergency_contact_name' => 'required|string|max:255',
        'emergency_phone' => 'required|string|max:15',
        'relationship' => 'required|string',  // Add validation for relationship
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Check if the user already has emergency contact information
    $emergency = $user->emergencyContact;

    if ($emergency) {
        // Update the existing emergency contact info
        $emergency->update([
            'contact_name' => $validatedData['emergency_contact_name'],
            'phone' => $validatedData['emergency_phone'],
            'relation' => $validatedData['relationship'],  // Save the relationship
        ]);
    } else {
        // Create a new emergency contact record
        $user->emergencyContact()->create([
            'name' => $validatedData['emergency_contact_name'],
            'phone' => $validatedData['emergency_phone'],
            'relation' => $validatedData['relationship'],  // Save the relationship
        ]);
    }

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Emergency info updated successfully.');
}

}
