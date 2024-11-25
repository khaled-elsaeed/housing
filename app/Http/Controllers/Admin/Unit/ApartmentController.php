<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Models\Apartment; // Import the Apartment model
use App\Models\Building; // Import the Building model
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\Units\ApartmentsExport;



class ApartmentController extends Controller
{
    /**
     * Display a listing of the buildings and associated apartments.
     */
    public function index()
    {
        try{
            $apartments = Apartment::with('building')->get();
 $buildingNumbers = $apartments->pluck('building.number')->unique();        // Calculate necessary counts
        $totalApartments = $apartments->count();
        $occupiedCount = $apartments->where('occupancy_status', 'full_occupied')->count();
        $emptyCount = $apartments->where('occupancy_status', 'empty')->count();

        $maleApartments = $apartments->filter(function ($apartment) {
            return $apartment->building->gender === 'male';
        });
        $maleApartmentCount = $maleApartments->count();
        $maleOccupiedCount = $maleApartments->where('occupancy_status', 'full_occupied')->count();
        $malePartiallyOccupiedCount = $maleApartments->where('occupancy_status', 'partially_occupied')->count();

        $femaleApartments = $apartments->filter(function ($apartment) {
            return $apartment->building->gender === 'female';
        });
        $femaleApartmentCount = $femaleApartments->count();
        $femaleOccupiedCount = $femaleApartments->where('occupancy_status', 'full_occupied')->count();
        $femalePartiallyOccupiedCount = $femaleApartments->where('occupancy_status', 'partially_occupied')->count();

        $maintenanceCount = $apartments->where('status', 'under_maintenance')->count();
        $maleUnderMaintenanceCount = $maleApartments->where('status', 'under_maintenance')->count();
        $femaleUnderMaintenanceCount = $femaleApartments->where('status', 'under_maintenance')->count();

        return view('admin.unit.apartment', compact(
            'apartments', 
        'buildingNumbers', 
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
    } catch (Exception $e) {
        Log::error('Error retrieving apartment page data: ' . $e->getMessage(), [
            'exception' => $e,
            'stack' => $e->getTraceAsString(),
        ]);

        return response()->view('errors.505');
    }
        
    }


    public function destroy($id)
    {
        try {
            $apartment = Apartment::findOrFail($id);
            $apartment->delete();
    
            return response()->json(['success' => true, 'message' => 'Apartment deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting apartment ' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting apartment.'], 500);
        }
    }
    
    public function updateStatus(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'status' => 'required|in:active,inactive,under_maintenance'
        ]);
    
        $apartment = Apartment::find($request->apartment_id);
        $apartment->status = $request->status;
        $apartment->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Apartment status updated successfully.'
        ]);
    }
    
    public function updateNote(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'note' => 'required|string'
        ]);
    
        $apartment = Apartment::find($request->apartment_id);
        $apartment->note = $request->note;
        $apartment->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Apartment note updated successfully.'
        ]);
    }
    
    public function downloadApartmentsExcel()
    {
        try {
            $export = new ApartmentsExport();
            return $export->downloadExcel();
        } catch (\Exception $e) {
            Log::error('Error exporting apartments to Excel', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to export apartments to Excel'], 500);
        }
    }
    
}
