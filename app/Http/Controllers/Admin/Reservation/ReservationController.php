<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Log;

class ReservationController extends Controller
{
    // Display the reservation index page
    public function index()
    {
        return view('admin.reservation.index');
    }

    // Fetch reservations with filters, pagination, and search
    public function fetchReservations(Request $request)
    {
        try {
            $query = Reservation::with(['user']); // Base query with relationships

            // Apply search filter
            if ($request->filled('customSearch')) {
                $query->whereHas('user.student', function ($q) use ($request) {
                    $q->where('name_en', 'like', '%' . $request->customSearch . '%');
                })->orWhere('status', 'like', '%' . $request->customSearch . '%');
            }

            // Clone query for filtered records count
            $filteredQuery = clone $query;
            $totalRecords = Reservation::count();
            $filteredRecords = $filteredQuery->count();

            // Pagination
            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $reservations = $query->skip($start)->take($length)->get();

            // Map response data
            return response()->json([
                'draw' => $request->get('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $reservations->map(function ($reservation) {
                    $location = $reservation->room
                        ? $reservation->room->getLocation()
                        : ['building' => 'N/A', 'apartment' => 'N/A', 'room' => 'N/A'];

                    $locationString = implode(' - ', $location);

                    return [
                        'reservation_id' => $reservation->id,
                        'name' => $reservation->user->student->name_en ?? 'N/A',
                        'location' => $locationString,
                        'start_date' => $reservation->start_date ? $reservation->start_date->format('F j, Y, g:i A') : 'N/A',
                        'end_date' => $reservation->end_date ? $reservation->end_date->format('F j, Y, g:i A') : 'N/A',
                        'status' => $reservation->status,
                        'actions' => '<button type="button" class="btn btn-round btn-info-rgba" data-reservation-id="' . $reservation->id . '" id="details-btn" title="More Details"><i class="feather icon-info"></i></button>',
                    ];
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching reservations data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch reservations data.'], 500);
        }
    }

    // Fetch reservation summary statistics
    public function getReservationsSummary()
    {
        try {
            $reservations = Reservation::with(['user'])->get();

            $summary = [
                'totalReservations' => $reservations->count(),
                'totalMaleCount' => $reservations->where('user.gender', 'male')->count(),
                'totalFemaleCount' => $reservations->where('user.gender', 'female')->count(),
                'totalPendingCount' => $reservations->where('status', 'pending')->count(),
                'malePendingCount' => $reservations->where('status', 'pending')->where('user.gender', 'male')->count(),
                'femalePendingCount' => $reservations->where('status', 'pending')->where('user.gender', 'female')->count(),
                'totalConfirmedCount' => $reservations->where('status', 'confirmed')->count(),
                'maleConfirmedCount' => $reservations->where('status', 'confirmed')->where('user.gender', 'male')->count(),
                'femaleConfirmedCount' => $reservations->where('status', 'confirmed')->where('user.gender', 'female')->count(),
                'totalCancelledCount' => $reservations->where('status', 'cancelled')->count(),
                'maleCancelledCount' => $reservations->where('status', 'cancelled')->where('user.gender', 'male')->count(),
                'femaleCancelledCount' => $reservations->where('status', 'cancelled')->where('user.gender', 'female')->count(),
            ];

            return response()->json($summary);
        } catch (Exception $e) {
            Log::error('Error fetching reservation summary: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch reservation summary.'], 500);
        }
    }

    // Display relocation view
    public function relocation()
    {
        return view('admin.reservation.relocation');
    }

    public function show($nationalId)
{
    // Fetch the user using the provided national ID
    $user = User::getUserByNationalId($nationalId);

    // Check if the user exists
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Log the user details
    Log::info('User found', [
        'nationalId' => $nationalId,
        'user' => $user->toArray(),
    ]);

    // Check if the user has a reservation
    $reservation = $user->reservation;

    if (!$reservation) {
        Log::warning('User has no reservation', ['nationalId' => $nationalId]);

        return response()->json([
            'success' => false,
            'message' => 'No active reservation found'
        ], 404);
    }

    // Fetch the location details of the reservation
    $location = $user->getLocationDetails();

    // Fetch student details if available
    $student = $user->student;

    // Return reservation and student details
    return response()->json([
        'success' => true,
        'reservation' => [
            'id' => $reservation->id,
            'room_number' => $location['room'] ?? null,
            'building_number' => $location['building'] ?? null,
            'apartment_number' => $location['apartment'] ?? null,
        ],
        'student' => [
            'name_en' => $student->name_en ?? 'N/A',
            'faculty' => $student->faculty->name_en ?? 'N/A',
        ],
    ]);
}


    public function swapReservationLocation(Request $request)
    {
        $validatedData = $request->validate([
            'reservation_id_1' => 'required|exists:reservations,id',
            'reservation_id_2' => 'required|exists:reservations,id',
        ]);

        $reservation1 = Reservation::find($validatedData['reservation_id_1']);
        $reservation2 = Reservation::find($validatedData['reservation_id_2']);

        if (!$reservation1 || !$reservation2) {
            return response()->json(['error' => 'Reservations not found'], 404);
        }

        $tempRoomId = $reservation1->room_id;
        $reservation1->room_id = $reservation2->room_id;
        $reservation2->room_id = $tempRoomId;

        $reservation1->save();
        $reservation2->save();

        return response()->json([
            'success' => true,
            'message' => 'Reservation locations swapped successfully',
            'reservation1' => $reservation1,
            'reservation2' => $reservation2,
        ]);
    }

    // Reallocate a reservation to a new room
    public function reallocateReservation(Request $request)
    {
        $validatedData = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $reservation = Reservation::find($validatedData['reservation_id']);
        $newRoomId = $validatedData['room_id'];

        if (Reservation::where('room_id', $newRoomId)->exists()) {
            return response()->json(['error' => 'Room is already assigned to another reservation'], 400);
        }

        $reservation->room_id = $newRoomId;
        $reservation->save();

        return response()->json([
            'success' => true,
            'message' => 'Reservation reallocated successfully',
            'reservation' => $reservation,
        ]);
    }
}