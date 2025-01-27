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
        try {
            $apartments = Apartment::with('building')->get();
            $buildingNumbers = $apartments->pluck('building.number')->unique();
            $totalApartments = $apartments->count();
           

            $maleApartments = $apartments->filter(function ($apartment) {
                return $apartment->building->gender === 'male';
            });
            $maleApartmentCount = $maleApartments->count();
          

            $femaleApartments = $apartments->filter(function ($apartment) {
                return $apartment->building->gender === 'female';
            });
            $femaleApartmentCount = $femaleApartments->count();
           

            $maintenanceCount = $apartments->where('status', 'under_maintenance')->count();
            $maleUnderMaintenanceCount = $maleApartments->where('status', 'under_maintenance')->count();
            $femaleUnderMaintenanceCount = $femaleApartments->where('status', 'under_maintenance')->count();

            return view(
                'admin.unit.apartment',
                compact(
                    'apartments',
                    'buildingNumbers',
                    'totalApartments',
                   
                    'maleApartmentCount',
                    
                    'femaleApartmentCount',
                  
                    'maintenanceCount',
                    'maleUnderMaintenanceCount',
                    'femaleUnderMaintenanceCount'
                )
            );
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
            'status' => 'required|in:active,inactive,under_maintenance',
        ]);

        $apartment = Apartment::find($request->apartment_id);
        $apartment->status = $request->status;
        $apartment->save();

        return response()->json([
            'success' => true,
            'message' => 'Apartment status updated successfully.',
        ]);
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'note' => 'required|string',
        ]);

        $apartment = Apartment::find($request->apartment_id);
        $apartment->note = $request->note;
        $apartment->save();

        return response()->json([
            'success' => true,
            'message' => 'Apartment note updated successfully.',
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

    public function fetchEmptyApartments($buildingID)
{
    try {
        // Fetch apartments for a specific building that have at least one empty room
        $emptyApartments = Apartment::whereHas('rooms', function ($query) {
            // Filter rooms that are not fully occupied, active, and for accommodation
            $query->where('full_occupied', '!=', 1)
                  ->where('status', 'active')
                  ->where('purpose', 'accommodation');
        })
        ->where('building_id', $buildingID)
        ->select('id', 'number')
        ->get();

        // Map the results to the desired format
        $emptyApartments = $emptyApartments->map(function ($apartment) {
            return [
                'id' => $apartment->id,
                'number' => $apartment->number,
            ];
        });

        // Return the response
        return response()->json([
            'success' => true,
            'apartments' => $emptyApartments,
        ]);
    } catch (\Exception $e) {
        // Log the error and return a failure response
        Log::error('Error fetching empty apartments: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch empty apartments. Please try again later.',
        ], 500);
    }
}
}
