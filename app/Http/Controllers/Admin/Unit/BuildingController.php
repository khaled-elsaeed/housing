<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Models\Building;
use App\Models\Apartment;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\Units\BuildingsExport;

use Illuminate\Support\Facades\Log;

class BuildingController extends Controller
{
    public function index()
    {
        try {
            $buildings = Building::all();

            $totalBuildings = $buildings->count();
            $activeBuildingsCount = $this->countBuildingsByCriteria($buildings, 'status', 'active');
            $inactiveBuildingsCount = $this->countBuildingsByCriteria($buildings, 'status', 'inactive');
            $underMaintenanceCount = $this->countBuildingsByCriteria($buildings, 'status', 'under_maintenance');

            $maleBuildingCount = $this->countBuildingsByCriteria($buildings, 'gender', 'male');
            $femaleBuildingCount = $this->countBuildingsByCriteria($buildings, 'gender', 'female');

            $maleActiveCount = $this->countBuildingsByCriteria($buildings, 'gender', 'male', 'status', 'active');
            $maleInactiveCount = $this->countBuildingsByCriteria($buildings, 'gender', 'male', 'status', 'inactive');

            $femaleActiveCount = $this->countBuildingsByCriteria($buildings, 'gender', 'female', 'status', 'active');
            $femaleInactiveCount = $this->countBuildingsByCriteria($buildings, 'gender', 'female', 'status', 'inactive');

            $maleUnderMaintenanceCount = $this->countBuildingsByCriteria($buildings, 'gender', 'male', 'status', 'under_maintenance');
            $femaleUnderMaintenanceCount = $this->countBuildingsByCriteria($buildings, 'gender', 'female', 'status', 'under_maintenance');

            $maintenanceCount = $maleUnderMaintenanceCount + $femaleUnderMaintenanceCount;

            return view('admin.unit.building', compact(
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
        } catch (\Exception $e) {
            Log::error('Error fetching building data for builidng index page : ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'user' => auth()->user() ? auth()->user()->id : 'Guest',
            ]);            
            return redirect()->route('error.page') 
            ->with('error', 'An error occurred while fetching building data. Please try again later or contact IT support.');
        }
        
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'building_number' => 'required',
        'gender' => 'required',
        'max_apartments' => 'required|integer',
        'max_rooms_per_apartment' => 'required|integer',
    ]);

    $existingBuilding = Building::where('number', $request->building_number)->first();

    if ($existingBuilding) {
        return response()->json([
            'success' => false,
            'message' => 'The building number already exists.'
        ], 422); 
    }

    try {
        $building = Building::create([
            'number' => $request->building_number,
            'gender' => $request->gender,
            'max_apartments' => $request->max_apartments,
            'max_rooms_per_apartment' => $request->max_rooms_per_apartment,
        ]);

        $this->createApartments($building, $request->max_apartments, $request->max_rooms_per_apartment);

        return response()->json([
            'success' => true,
            'message' => 'Building created successfully.',
            'data' => $building
        ]);

    } catch (\Exception $e) {
        Log::error('Error adding building: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while saving the building. Please try again.'
        ], 500); 
    }
}

private function createApartments(Building $building, $maxApartments, $maxRoomsPerApartment)
{
    try {
        for ($i = 1; $i <= $maxApartments; $i++) {
            $apartment = Apartment::create([
                'building_id' => $building->id,
                'number' => $i,
                'max_rooms' => $maxRoomsPerApartment,
                'occupancy_status' => 'empty',
                'status' => 'active',
            ]);

            $this->createRooms($apartment, $maxRoomsPerApartment);
        }
    } catch (\Exception $e) {
        Log::error('Error creating apartments: ' . $e->getMessage());
        throw new \Exception('Error creating apartments.');
    }
}

private function createRooms(Apartment $apartment, $maxRooms)
{
    try {
        for ($j = 1; $j <= $maxRooms; $j++) {
            Room::create([
                'apartment_id' => $apartment->id,
                'number' => $j,
                'max_occupancy' => 6,
                'current_occupancy' => 0,
                'status' => 'active',
                'purpose' => 'accommodation',
                'type' => 'single',
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Error creating rooms: ' . $e->getMessage());
        throw new \Exception('Error creating rooms.');
    }
}


    private function countBuildingsByCriteria($buildings, $key, $value, $secondKey = null, $secondValue = null)
    {
        try {
            $filtered = $buildings->where($key, $value);
            if ($secondKey && $secondValue) {
                $filtered = $filtered->where($secondKey, $secondValue);
            }
            return $filtered->count();
        } catch (\Exception $e) {
            Log::error('Error counting buildings by criteria: ' . $e->getMessage());
            return 0;
        }
    }

    public function destroy($id)
{
    try {
        $building = Building::findOrFail($id);
        $building->delete();

        return response()->json(['success' => true, 'message' => 'Building deleted successfully.']);
    } catch (\Exception $e) {
        Log::error('Error deleting building ' . $id . ': ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error deleting building.'], 500);
    }
}

public function updateStatus(Request $request)
{
    $request->validate([
        'building_id' => 'required|exists:buildings,id',
        'status' => 'required|in:active,inactive,under_maintenance'
    ]);

    $building = Building::find($request->building_id);
    $building->status = $request->status;
    $building->save();

    return response()->json([
        'success' => true,
        'message' => 'Building status updated successfully.'
    ]);
}

public function updateNote(Request $request)
{
    $request->validate([
        'building_id' => 'required|exists:buildings,id',
        'note' => 'required|string'
    ]);

    $building = Building::find($request->building_id);
    $building->note = $request->note;
    $building->save();

    return response()->json([
        'success' => true,
        'message' => 'Building note updated successfully.'
    ]);
}

public function downloadBuildingsExcel()
{
    try {
        $export = new BuildingsExport();
        return $export->downloadExcel();
    } catch (\Exception $e) {
        Log::error('Error exporting buildings to Excel', [
            'exception' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['error' => 'Failed to export buildings to Excel'], 500);
    }
}


}
