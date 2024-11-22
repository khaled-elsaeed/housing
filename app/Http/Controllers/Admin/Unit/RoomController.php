<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\Units\RoomsExport;



class RoomController extends Controller
{
    public function index()
    {
        // Fetch all rooms with related data
        $rooms = Room::with(['apartment.building'])->get();

        // Get unique building numbers
        $buildingNumbers = Room::with('apartment.building')
            ->get()
            ->pluck('apartment.building.number') // Adjust 'number' to match your building attribute name
            ->unique()
            ->sort()
            ->values()
            ->all();

        // Get unique apartment numbers
        $apartmentNumbers = Room::with('apartment')
            ->get()
            ->pluck('apartment.number') // Adjust 'number' to match your apartment attribute name
            ->unique()
            ->sort()
            ->values()
            ->all();

        // Initialize counts
        $singleRoomCount = 0;
        $doubleRoomCount = 0;
        $underMaintenanceCount = 0;

        // Occupancy tracking
        $singleRoomOccupiedMales = 0;
        $singleRoomOccupiedFemales = 0;
        $doubleRoomOccupiedMales = 0;
        $doubleRoomOccupiedFemales = 0;
        $singleRoomEmptyMales = 0;
        $singleRoomEmptyFemales = 0;
        $doubleRoomEmptyMales = 0;
        $doubleRoomEmptyFemales = 0;

        // Partially occupied counts for double rooms
        $doubleRoomPartiallyOccupiedMales = 0;
        $doubleRoomPartiallyOccupiedFemales = 0;

        // Maintenance counts
        $underMaintenanceSingleMales = 0;
        $underMaintenanceSingleFemales = 0;
        $underMaintenanceDoubleMales = 0;
        $underMaintenanceDoubleFemales = 0;

        foreach ($rooms as $room) {
            // Get gender information safely with optional
            $gender = optional($room->apartment->building)->gender;

            // Count total rooms by type
            if ($room->type === 'single') {
                $singleRoomCount++;
                if ($room->status === 'active') {
                    // Increment occupied counts based on gender
                    if ($gender === 'male') {
                        $singleRoomOccupiedMales++;
                    } elseif ($gender === 'female') {
                        $singleRoomOccupiedFemales++;
                    }
                } elseif ($room->status === 'empty') {
                    // Increment empty counts based on gender
                    if ($gender === 'male') {
                        $singleRoomEmptyMales++;
                    } elseif ($gender === 'female') {
                        $singleRoomEmptyFemales++;
                    }
                }
            } elseif ($room->type === 'double') {
                $doubleRoomCount++;
                if ($room->status === 'active') {
                    if ($gender === 'male') {
                        $doubleRoomOccupiedMales++;
                    } elseif ($gender === 'female') {
                        $doubleRoomOccupiedFemales++;
                    }
                } elseif ($room->status === 'partially occupied') {
                    // Increment partially occupied counts based on gender
                    if ($gender === 'male') {
                        $doubleRoomPartiallyOccupiedMales++;
                    } elseif ($gender === 'female') {
                        $doubleRoomPartiallyOccupiedFemales++;
                    }
                } elseif ($room->status === 'empty') {
                    // Increment empty counts based on gender
                    if ($gender === 'male') {
                        $doubleRoomEmptyMales++;
                    } elseif ($gender === 'female') {
                        $doubleRoomEmptyFemales++;
                    }
                }
            }

            // Count rooms under maintenance
            if ($room->status === 'under_maintenance') {
                $underMaintenanceCount++;
                if ($room->type === 'single') {
                    if ($gender === 'male') {
                        $underMaintenanceSingleMales++;
                    } elseif ($gender === 'female') {
                        $underMaintenanceSingleFemales++;
                    }
                } elseif ($room->type === 'double') {
                    if ($gender === 'male') {
                        $underMaintenanceDoubleMales++;
                    } elseif ($gender === 'female') {
                        $underMaintenanceDoubleFemales++;
                    }
                }
            }
        }

        return view('admin.unit.room', compact(
            'rooms',
            'buildingNumbers',
            'apartmentNumbers',
            'singleRoomCount',
            'doubleRoomCount',
            'underMaintenanceCount',
            'singleRoomOccupiedMales',
            'singleRoomOccupiedFemales',
            'doubleRoomOccupiedMales',
            'doubleRoomOccupiedFemales',
            'singleRoomEmptyMales',
            'singleRoomEmptyFemales',
            'doubleRoomEmptyMales',
            'doubleRoomEmptyFemales',
            'doubleRoomPartiallyOccupiedMales',
            'doubleRoomPartiallyOccupiedFemales',
            'underMaintenanceSingleMales',
            'underMaintenanceSingleFemales',
            'underMaintenanceDoubleMales',
            'underMaintenanceDoubleFemales'
        ));
    }

    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();
    
            return response()->json(['success' => true, 'message' => 'Room deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting room ' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting room.'], 500);
        }
    }
    
    public function updateStatus(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'status' => 'required|in:active,inactive,under_maintenance'
        ]);
    
        $room = Room::find($request->room_id);
        $room->status = $request->status;
        $room->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Room status updated successfully.'
        ]);
    }
    
    public function updateNote(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'note' => 'required|string'
        ]);
    
        $room = Room::find($request->room_id);
        $room->note = $request->note;
        $room->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Room note updated successfully.'
        ]);
    }
    
    public function downloadRoomsExcel()
    {
        try {
            $export = new RoomsExport();
            return $export->downloadExcel();
        } catch (\Exception $e) {
            Log::error('Error exporting rooms to Excel', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to export rooms to Excel'], 500);
        }
    }


    
}
