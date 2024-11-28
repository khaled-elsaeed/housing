<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ReservationController extends Controller
{
    public function index()
    {
        return view('admin.reservation.relocation');
    }

    public function show($nationalId)
    {
        $user = User::getUserByNationalId($nationalId);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $reservation = $user->reservation;
        $location = $user->getLocationDetails();  
    
        if ($reservation) {
            // Get student details
            $student = $user->student;  // This will fetch the related student data
    
            // Append name_en and faculty to the reservation if student exists
            if ($student) {
                // Only append the necessary data
                $reservation->name_en = $student->name_en;
                $reservation->faculty = $student->faculty->name_en;
            }
    
            // Check if location details exist and append them to the reservation
            if ($location) {
                $reservation->room_number = $location['room'] ?? null; // Use null if the key doesn't exist
                $reservation->building_number = $location['building'] ?? null;
                $reservation->apartment_number = $location['apartment'] ?? null;
            }
    
            // Only return specific fields in the reservation response
            return response()->json([
                'success' => true,
                'reservation' => [
                    'id' => $reservation->id,

                    'room_number' => $reservation->room_number,
                    'building_number' => $reservation->building_number,
                    'apartment_number' => $reservation->apartment_number
                ],
                'student' => [
                   
                    'name_en' => $reservation->name_en,
                    'faculty' => $reservation->faculty,
                   
                ]
            ]);
        } else {
            return response()->json(['message' => 'Reservation not found'], 404);
        }
    }

    public function swapReservationLocation(Request $request)
    {
        // Validate the input
        $validatedData = $request->validate([
            'reservation_id_1' => 'required|exists:reservations,id',
            'reservation_id_2' => 'required|exists:reservations,id',
        ]);
    
        // Fetch the reservations using the validated IDs
        $reservation1 = Reservation::find($validatedData['reservation_id_1']);
        $reservation2 = Reservation::find($validatedData['reservation_id_2']);
    
        // Check if both reservations exist
        if (!$reservation1 || !$reservation2) {
            return response()->json(['error' => 'Reservations not found'], 404);
        }
    
        // Swap the locations of the reservations
        $tempRoomId = $reservation1->room_id;
        $reservation1->room_id = $reservation2->room_id;
        $reservation2->room_id = $tempRoomId;
    
        // Save the updated reservations
        $reservation1->save();
        $reservation2->save();
    
        // Return a success response
        return response()->json([
            'message' => 'Reservation locations swapped successfully',
            'reservation1' => $reservation1,
            'reservation2' => $reservation2,
        ]);
    }
    

    public function reallocateReservation(Request $request)
    {
    // Validate the input
    $validatedData = $request->validate([
        'reservation_id' => 'required|exists:reservations,id',
        'room_id' => 'required|exists:rooms,id',
    ]);

    // Fetch the reservation and room
    $reservation = Reservation::find($validatedData['reservation_id']);
    $newRoomId = $validatedData['room_id'];

    // Check if the reservation exists
    if (!$reservation) {
        return response()->json(['error' => 'Reservation not found'], 404);
    }

    // Check if the new room is already assigned to another reservation
    $existingReservation = Reservation::where('room_id', $newRoomId)->first();
    if ($existingReservation) {
        return response()->json([
            'error' => 'Room is already assigned to another reservation',
            'existingReservation' => $existingReservation,
        ], 400);
    }

    // Reallocate the reservation to the new room
    $reservation->room_id = $newRoomId;
    $reservation->save();

    // Return a success response
    return response()->json([
        'message' => 'Reservation reallocated successfully',
        'reservation' => $reservation,
    ]);
}

    
    
    
    

    
    

    

}

