<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Models\UniversityArchieve;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Country;
use App\Models\Faculty; 
use App\Models\Program;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class StudentProfileController extends Controller
{
    /**
     * Show the student's home page.
     */
    public function index()
    {
        try {
            $user = auth()->user();
    
            // Check if the user has archived data
            $archivedData = $user->universityArchive()->first(); // Ensure universityArchive relationship exists
    
            if (!$archivedData) {
                // Handle case where no archived data is found for the user
                return redirect()->route('profile.complete')->with('error', 'No archived data found.');
            }
    
            // Fetch all governorates, cities, and countries
            $governorates = Governorate::all(); // Consider paginating if you have a large number
            $cities = City::all(); // You might want to paginate this too
            $countries = Country::all(); // Assuming you have a Country model set up
    
            // Fetch all programs (not filtered by faculties)
            $programs = Program::all(); // Fetch all programs, or you can apply any conditions if needed
    
            // Return the view with the required data
            return view('student.profile-complete', compact('archivedData', 'governorates', 'cities', 'countries', 'programs'));
    
        } catch (\Exception $e) {
            Log::error('Error loading student profile: ' . $e->getMessage());
            // Return a generic error message to the user without exposing details
            return redirect()->route('home')->with('error', 'Something went wrong while loading the page.');
        }
    }
    

    /**
     * Fetch cities based on the selected governorate.
     */
    public function getCitiesByGovernorate(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'governorate_id' => 'required|exists:governorates,id',
            ]);

            // Fetch cities for the selected governorate
            $cities = City::where('governorate_id', $request->governorate_id)->get();

            // Return cities as a JSON response
            return response()->json($cities);
        } catch (\Exception $e) {
            Log::error('Error fetching cities: ' . $e->getMessage());
            return response()->json(['error' => 'Could not fetch cities.'], 500);
        }
    }
}
