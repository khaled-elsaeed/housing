<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Models\Building;
use App\Models\Apartment;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\Units\BuildingsExport;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class BuildingController extends Controller
{
    public function index()
    {
        try {
            $buildings = Building::all();
    
            $totalBuildings = $buildings->count();
            $activeBuildingsCount = $buildings->where('status', 'active')->count();
            $inactiveBuildingsCount = $buildings->where('status', 'inactive')->count();
            $underMaintenanceCount = $buildings->where('status', 'under_maintenance')->count();
    
            // Filtering by gender
            $maleBuildings = $buildings->where('gender', 'male');
            $femaleBuildings = $buildings->where('gender', 'female');
    
            $maleBuildingCount = $maleBuildings->count();
            $femaleBuildingCount = $femaleBuildings->count();
    
            // Further filtering by status
            $maleActiveCount = $maleBuildings->where('status', 'active')->count();
            $maleInactiveCount = $maleBuildings->where('status', 'inactive')->count();
            $maleUnderMaintenanceCount = $maleBuildings->where('status', 'under_maintenance')->count();
    
            $femaleActiveCount = $femaleBuildings->where('status', 'active')->count();
            $femaleInactiveCount = $femaleBuildings->where('status', 'inactive')->count();
            $femaleUnderMaintenanceCount = $femaleBuildings->where('status', 'under_maintenance')->count();
    
            // Total maintenance count
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
        } catch (Exception $e) {
            Log::error('Error retrieving admin building page data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);
    
            return view('error.page_init');
        }
    }
    


public function store(Request $request)
{
    $validated = $request->validate([
        'building_number' => 'required|integer',
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

    
    DB::beginTransaction();

    try {
        
        $building = Building::create([
            'number' => $request->building_number,
            'gender' => $request->gender,
            'max_apartments' => $request->max_apartments,
            'max_rooms_per_apartment' => $request->max_rooms_per_apartment,
        ]);

        
        $this->createApartments($building, $request->max_apartments, $request->max_rooms_per_apartment);

        
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Building created successfully.',
            'data' => $building
        ]);

    } catch (\Exception $e) {
        
        DB::rollBack();

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
            
            $existingApartment = Apartment::where('building_id', $building->id)
                ->where('number', $i)
                ->exists();

            if ($existingApartment) {
                throw new \Exception("Duplicate apartment number {$i} detected in building {$building->id}");
            }

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
        throw $e; 
    }
}


private function createRooms(Apartment $apartment, $maxRooms)
{
    try {
        for ($j = 1; $j <= $maxRooms; $j++) {
            
            $existingRoom = Room::where('apartment_id', $apartment->id)
                ->where('number', $j)
                ->exists();

            if ($existingRoom) {
                throw new \Exception("Duplicate room number {$j} detected in apartment {$apartment->id}");
            }

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
        throw $e; 
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
