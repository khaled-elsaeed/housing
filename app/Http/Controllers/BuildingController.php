<?php

namespace App\Http\Controllers;

use App\Models\Building; // Assuming your Building model is in App\Models
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * Display a listing of the buildings.
     */
    public function index()
    {
        // Fetch all buildings
        $buildings = Building::all(); 

        // Calculate necessary counts
        $totalBuildings = $buildings->count();
        $activeBuildingsCount = $this->countBuildingsByCriteria($buildings, 'status', 'active');
        $inactiveBuildingsCount = $this->countBuildingsByCriteria($buildings, 'status', 'inactive');
        $underMaintenanceCount = $this->countBuildingsByCriteria($buildings, 'status', 'under_maintenance');

        // Count buildings by gender
        $maleBuildingCount = $this->countBuildingsByCriteria($buildings, 'gender', 'male');
        $femaleBuildingCount = $this->countBuildingsByCriteria($buildings, 'gender', 'female');

        // Count active and inactive buildings by gender
        $maleActiveCount = $this->countBuildingsByCriteria($buildings, 'gender', 'male', 'status', 'active');
        $maleInactiveCount = $this->countBuildingsByCriteria($buildings, 'gender', 'male', 'status', 'inactive');
        
        $femaleActiveCount = $this->countBuildingsByCriteria($buildings, 'gender', 'female', 'status', 'active');
        $femaleInactiveCount = $this->countBuildingsByCriteria($buildings, 'gender', 'female', 'status', 'inactive');

        // Count males and females under maintenance
        $maleUnderMaintenanceCount = $this->countBuildingsByCriteria($buildings, 'gender', 'male', 'status', 'under_maintenance');
        $femaleUnderMaintenanceCount = $this->countBuildingsByCriteria($buildings, 'gender', 'female', 'status', 'under_maintenance');

        // Count of buildings under maintenance
        $maintenanceCount = $maleUnderMaintenanceCount + $femaleUnderMaintenanceCount;

        // Return the view with the required data
        return view('admin.housing.building', compact(
            'buildings', 
            'totalBuildings', 
            'activeBuildingsCount', 
            'inactiveBuildingsCount',
            'underMaintenanceCount',
            'maleBuildingCount',
            'femaleBuildingCount',
            'maleActiveCount',
            'maleInactiveCount',
            'femaleActiveCount',
            'femaleInactiveCount',
            'maleUnderMaintenanceCount',
            'femaleUnderMaintenanceCount',
            'maintenanceCount' 
        ));
    }

    /**
     * Count buildings by specific criteria.
     */
    private function countBuildingsByCriteria($buildings, $key, $value, $secondKey = null, $secondValue = null)
    {
        $filtered = $buildings->where($key, $value);
        if ($secondKey && $secondValue) {
            $filtered = $filtered->where($secondKey, $secondValue);
        }
        return $filtered->count();
    }
}
