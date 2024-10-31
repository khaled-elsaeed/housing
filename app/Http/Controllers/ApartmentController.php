<?php

namespace App\Http\Controllers;

use App\Models\Apartment; // Import the Apartment model
use App\Models\Building; // Import the Building model
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the buildings and associated apartments.
     */
    public function index()
    {
        // Fetch all apartments with their associated buildings
        $apartments = Apartment::with('building')->get();
        // Get unique building IDs directly from the apartments
 // Get unique building numbers directly from the apartments
 $buildingNumbers = $apartments->pluck('building.number')->unique();        // Calculate necessary counts
        $totalApartments = $apartments->count();
        $occupiedCount = $apartments->where('occupancy_status', 'full_occupied')->count();
        $emptyCount = $apartments->where('occupancy_status', 'empty')->count();

        // Count male apartments
        $maleApartments = $apartments->filter(function ($apartment) {
            return $apartment->building->gender === 'male';
        });
        $maleApartmentCount = $maleApartments->count();
        $maleOccupiedCount = $maleApartments->where('occupancy_status', 'full_occupied')->count();
        $malePartiallyOccupiedCount = $maleApartments->where('occupancy_status', 'partially_occupied')->count();

        // Count female apartments
        $femaleApartments = $apartments->filter(function ($apartment) {
            return $apartment->building->gender === 'female';
        });
        $femaleApartmentCount = $femaleApartments->count();
        $femaleOccupiedCount = $femaleApartments->where('occupancy_status', 'full_occupied')->count();
        $femalePartiallyOccupiedCount = $femaleApartments->where('occupancy_status', 'partially_occupied')->count();

        // Count apartments under maintenance
        $maintenanceCount = $apartments->where('status', 'under_maintenance')->count();
        $maleUnderMaintenanceCount = $maleApartments->where('status', 'under_maintenance')->count();
        $femaleUnderMaintenanceCount = $femaleApartments->where('status', 'under_maintenance')->count();

        // Return the view with the required data
        return view('admin.housing.apartment', compact(
            'apartments', 
        'buildingNumbers', // Pass only the unique building numbers
            'totalApartments', 
            'occupiedCount', 
            'emptyCount',
            'maleApartmentCount',
            'maleOccupiedCount',
            'malePartiallyOccupiedCount',
            'femaleApartmentCount',
            'femaleOccupiedCount',
            'femalePartiallyOccupiedCount',
            'maintenanceCount',
            'maleUnderMaintenanceCount',
            'femaleUnderMaintenanceCount'
        ));
    }
}
