<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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
        $currentLang = App::getLocale();

        try {
            $query = Reservation::with(['user', 'room', 'academicTerm'])
                ->select('*')
                ->orderByRaw("
                    CASE 
                        WHEN status = 'pending' THEN 1
                        WHEN status = 'upcoming' THEN 2
                        WHEN status = 'complete' THEN 3
                        WHEN status = 'reject' THEN 4
                        ELSE 5 
                    END
                ") // Order by custom status
                ->orderBy('created_at', 'asc'); // Then order by created_at

            // Apply search filter
            if ($request->filled('customSearch')) {
                $query->where(function ($q) use ($request, $currentLang) {
                    $q->whereHas('user.student', function ($q) use ($request, $currentLang) {
                        $q->where('name_' . ($currentLang == 'ar' ? 'ar' : 'en'), 'like', '%' . $request->customSearch . '%')
                          ->orWhere('national_id', 'like', '%' . $request->customSearch . '%');
                    });
                });
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
                'data' => $reservations->map(function ($reservation) use ($currentLang) {
                    $startDate = Carbon::parse($reservation->start_date)->format('d-m-Y'); // Day-Month-Year format
                    $endDate = Carbon::parse($reservation->end_date)->format('d-m-Y'); // Day-Month-Year format
                    $location = $reservation->room
                        ? $reservation->room->getLocation()
                        : [
                            'building' => 'N/A',
                            'apartment' => 'N/A',
                            'room' => 'N/A',
                        ];

                    // Construct the location string
                    $locationString = trans('building') . ' ' . $location['building'] . ' - ' .
                                      trans('apartment') . ' ' . $location['apartment'] . ' - ' .
                                      trans('room') . ' ' . $location['room'];

                    return [
                        'name' => $reservation->user->student->{'name_' . ($currentLang == 'ar' ? 'ar' : 'en')} ?? 'N/A',
                        'national_id' => $reservation->user->student->national_id,
                        'location' => $locationString,
                        'period' => $reservation->period_type == 'long' ? 'Term' : 'Short',
                        'duration' => $reservation->period_type == 'long'
                            ? $reservation->academicTerm->name . ' ' . $reservation->academicTerm->academic_year
                            : $startDate . ' To ' . $endDate,
                        'status' => $reservation->status,
                    ];
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching reservations data: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['error' => 'Failed to fetch reservations data. Please check the logs for more details.'], 500);
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
                'malePendingCount' => $reservations
                    ->where('status', 'pending')
                    ->where('user.gender', 'male')
                    ->count(),
                'femalePendingCount' => $reservations
                    ->where('status', 'pending')
                    ->where('user.gender', 'female')
                    ->count(),
                'totalConfirmedCount' => $reservations->where('status', 'confirmed')->count(),
                'maleConfirmedCount' => $reservations
                    ->where('status', 'confirmed')
                    ->where('user.gender', 'male')
                    ->count(),
                'femaleConfirmedCount' => $reservations
                    ->where('status', 'confirmed')
                    ->where('user.gender', 'female')
                    ->count(),
                'totalCancelledCount' => $reservations->where('status', 'cancelled')->count(),
                'maleCancelledCount' => $reservations
                    ->where('status', 'cancelled')
                    ->where('user.gender', 'male')
                    ->count(),
                'femaleCancelledCount' => $reservations
                    ->where('status', 'cancelled')
                    ->where('user.gender', 'female')
                    ->count(),
            ];

            return response()->json($summary);
        } catch (Exception $e) {
            Log::error('Error fetching reservation summary: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch reservation summary.'], 500);
        }
    }

    // Display view to reallocate residents to another room
    public function relocation()
    {
        return view('admin.reservation.relocation');
    }

    // Show resident info
    public function show($nationalId)
    {
        $user = User::getUserByNationalId($nationalId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        Log::info('User found', [
            'nationalId' => $nationalId,
            'user' => $user->toArray(),
        ]);

        $reservation = $user->reservations()
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$reservation) {
            Log::warning('User has no reservation', ['nationalId' => $nationalId]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'No active reservation found',
                ],
                404
            );
        }

        $location = $user->getLocationDetails();

        $student = $user->student;

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
        // Validate the request data
        $validatedData = $request->validate([
            'reservation_id_1' => 'required|exists:reservations,id',
            'reservation_id_2' => 'required|exists:reservations,id',
        ]);
    
        // Find the reservations
        $reservation1 = Reservation::with(['user', 'room.apartment.building'])->find($validatedData['reservation_id_1']);
        $reservation2 = Reservation::with(['user', 'room.apartment.building'])->find($validatedData['reservation_id_2']);
    
        // Check if reservations exist
        if (!$reservation1 || !$reservation2) {
            return response()->json(['error' => 'Reservations not found'], 404);
        }
    
        // Validate user gender
        if ($reservation1->user->gender !== $reservation2->user->gender) {
            return response()->json(['error' => 'Users must be of the same gender to swap reservations'], 400);
        }
    
        // Validate building gender for reservation 1
        if ($reservation1->user->gender !== $reservation2->room->apartment->building->gender) {
            return response()->json(['error' => 'Building gender for Reservation 2 does not match the user gender of Reservation 1'], 400);
        }
    
        // Validate building gender for reservation 2
        if ($reservation2->user->gender !== $reservation1->room->apartment->building->gender) {
            return response()->json(['error' => 'Building gender for Reservation 1 does not match the user gender of Reservation 2'], 400);
        }
    
        // Swap room IDs
        $tempRoomId = $reservation1->room_id;
        $reservation1->room_id = $reservation2->room_id;
        $reservation2->room_id = $tempRoomId;
    
        // Save the changes
        $reservation1->save();
        $reservation2->save();
    
        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Reservation locations swapped successfully',
            'reservation1' => $reservation1,
            'reservation2' => $reservation2,
        ]);
    }
    public function reallocateReservation(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'room_id' => 'required|exists:rooms,id',
        ]);

        try {
            // Find the reservation and new room
            $reservation = Reservation::findOrFail($validatedData['reservation_id']);

            $newRoom = Room::findOrFail($validatedData['room_id']);

            // Check if the new room is already assigned to another reservation
            if (Reservation::where('room_id', $newRoom->id)->exists()) {
                return response()->json(['error' => 'Room is already assigned to another reservation'], 400);
            }

            // Check if the room's gender matches the resident's gender
            if ($reservation->user->gender !== $newRoom->apartment->building->gender) {
                return response()->json(['error' => 'Room gender is different than resident gender'], 400);
            }

            // Begin the reallocation process
            DB::beginTransaction();

            // Update the previous room's occupancy if it exists
            if ($reservation->room_id) {
                $previousRoom = Room::find($reservation->room_id);
                if ($previousRoom) {
                    $previousRoom->current_occupancy -= 1;
                    if ($previousRoom->current_occupancy != $previousRoom->max_occupancy) {
                        $previousRoom->full_occupied = 0;
                    }
                    $previousRoom->save();
                }
            }

            // Update the new room's occupancy
            $newRoom->current_occupancy += 1;
            if ($newRoom->current_occupancy == $newRoom->max_occupancy) {
                $newRoom->full_occupied = 1;
            }
            $newRoom->save();

            // Assign the new room to the reservation
            $reservation->room_id = $newRoom->id;
            $reservation->save();

            // Commit the transaction
            DB::commit();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Reservation reallocated successfully',
                'reservation' => $reservation,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle case where reservation or room is not found
            DB::rollBack();
            return response()->json(['error' => 'Reservation or Room not found'], 404);

        } catch (\Exception $e) {
            // Handle any other exceptions
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while reallocating the reservation: ' . $e->getMessage()], 500);
        }
    }
}