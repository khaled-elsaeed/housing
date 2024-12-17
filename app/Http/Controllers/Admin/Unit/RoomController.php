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
        try {
            $rooms = Room::with(['apartment.building'])->get();

            // Get unique building and apartment numbers
            $buildingNumbers = $rooms
                ->pluck('apartment.building.number')
                ->unique()
                ->sort()
                ->values();
            $apartmentNumbers = $rooms
                ->pluck('apartment.number')
                ->unique()
                ->sort()
                ->values();
            $totalRoomsCount = $rooms->count();
            // Count rooms by type
            $singleRoomCount = $rooms->where('type', 'single')->count();
            $doubleRoomCount = $rooms->where('type', 'double')->count();

            // Count under maintenance rooms
            $underMaintenanceCount = $rooms->where('status', 'under_maintenance')->count();

            // Filter rooms by type and gender
            $singleRooms = $rooms->where('type', 'single');
            $doubleRooms = $rooms->where('type', 'double');

            $maleSingleRooms = $singleRooms->filter(fn($room) => optional($room->apartment->building)->gender === 'male');
            $femaleSingleRooms = $singleRooms->filter(fn($room) => optional($room->apartment->building)->gender === 'female');

            $maleDoubleRooms = $doubleRooms->filter(fn($room) => optional($room->apartment->building)->gender === 'male');
            $femaleDoubleRooms = $doubleRooms->filter(fn($room) => optional($room->apartment->building)->gender === 'female');

            // Count occupancy statuses
            $singleRoomOccupiedMales = $maleSingleRooms->where('status', 'active')->count();
            $singleRoomOccupiedFemales = $femaleSingleRooms->where('status', 'active')->count();

            $singleRoomEmptyMales = $maleSingleRooms->where('status', 'empty')->count();
            $singleRoomEmptyFemales = $femaleSingleRooms->where('status', 'empty')->count();

            $doubleRoomOccupiedMales = $maleDoubleRooms->where('status', 'active')->count();
            $doubleRoomOccupiedFemales = $femaleDoubleRooms->where('status', 'active')->count();

            $doubleRoomPartiallyOccupiedMales = $maleDoubleRooms->where('status', 'partially occupied')->count();
            $doubleRoomPartiallyOccupiedFemales = $femaleDoubleRooms->where('status', 'partially occupied')->count();

            $doubleRoomEmptyMales = $maleDoubleRooms->where('status', 'empty')->count();
            $doubleRoomEmptyFemales = $femaleDoubleRooms->where('status', 'empty')->count();

            // Count under maintenance by type and gender
            $underMaintenanceSingleMales = $maleSingleRooms->where('status', 'under_maintenance')->count();
            $underMaintenanceSingleFemales = $femaleSingleRooms->where('status', 'under_maintenance')->count();

            $underMaintenanceDoubleMales = $maleDoubleRooms->where('status', 'under_maintenance')->count();
            $underMaintenanceDoubleFemales = $femaleDoubleRooms->where('status', 'under_maintenance')->count();

            return view(
                'admin.unit.room',
                compact(
                    'rooms',
                    'totalRoomsCount',
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
                )
            );
        } catch (Exception $e) {
            Log::error('Error retrieving admin room page data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->view('errors.505');
        }
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

    public function updateRoomDetails(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'status' => 'required|in:active,inactive,under_maintenance',
            'type' => 'required|in:single,double',
            'purpose' => 'required|in:accommodation,office,utility',
        ]);

        $room = Room::find($request->room_id);
        $room->status = $request->status;
        $room->type = $request->type;
        $room->purpose = $request->purpose;

        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Room status updated successfully.',
        ]);
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'note' => 'required|string',
        ]);

        $room = Room::find($request->room_id);
        $room->note = $request->note;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Room note updated successfully.',
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

    public function fetchEmptyRooms($apartmentID)
    {
        $emptyRooms = Room::where('full_occupied', '!=', 1)
            ->where('status', 'active')
            ->where('purpose', 'accommodation') 
            ->where('apartment_id', $apartmentID) 
            ->select('id', 'number') 
            ->get();

        $emptyRooms = $emptyRooms->map(function ($room) {
            return [
                'id' => $room->id,
                'number' => $room->number,
            ];
        });

        // Return the response
        return response()->json([
            'success' => true,
            'rooms' => $emptyRooms,
        ]);
    }
}
