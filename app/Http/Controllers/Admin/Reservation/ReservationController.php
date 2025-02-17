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
use Yajra\DataTables\Facades\DataTables;


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
        // Base query for reservations with status "active" or "pending"
        $query = Reservation::with(['user', 'room', 'academicTerm'])
            ->whereIn('status', ['active', 'pending'])
            ->orderBy('created_at', 'asc'); 

        // Apply search filter by student name or national ID
        if ($request->filled('customSearch')) {
            $search = $request->input('customSearch');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user.student', function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%");
                });
            });
        }

        // Apply building number filter
        if ($request->filled("building_number")) {
            $buildingNumber = $request->input("building_number");
            $query->whereHas("room.apartment.building", function ($q) use ($buildingNumber) {
                $q->where("number", $buildingNumber);
            });
        }

        // Apply apartment number filter
        if ($request->filled("apartment_number")) {
            $apartmentNumber = $request->input("apartment_number");
            $query->whereHas("room.apartment", function ($q) use ($apartmentNumber) {
                $q->where("number", $apartmentNumber);
            });
        }

        // Format data for DataTables
        return DataTables::of($query)
            ->editColumn("name", function ($reservation) {
                return $reservation->user
                    ? $reservation->user->student->{"name_" . (App::getLocale() == "ar" ? "ar" : "en")} ?? trans("N/A")
                    : trans("N/A");
            })
            ->editColumn("national_id", function ($reservation) {
                return $reservation->user->student->national_id ?? trans("N/A");
            })
            ->editColumn("location", function ($reservation) {
                // Get the room and apartment information for the first reservation
                $room = $reservation->room;
                if ($room && $room->apartment && $room->apartment->building) {
                    $buildingNumber = $room->apartment->building->number ?? trans("N/A");
                    $apartmentNumber = $room->apartment->number ?? trans("N/A");
                    $roomNumber = $room->number ?? trans("N/A");

                    return trans('Building') . " " . $buildingNumber . " - " .
                           trans('Apartment') . " " . $apartmentNumber . " - " .
                           trans('Room') . " " . $roomNumber;
                }

                return trans("N/A");
            })
            ->editColumn("period", function ($reservation) {
                return $reservation->period_type == 'long' ? trans('term') : trans('Short');
            })
            ->editColumn("duration", function ($reservation) {
                if ($reservation->period_type == 'long') {
                    $academicYear = App::getLocale() == 'ar' 
                        ? arabicNumbers($reservation->academicTerm->academic_year) 
                        : $reservation->academicTerm->academic_year;

                    return trans($reservation->academicTerm->name) . ' ' . $academicYear;
                } else {
                    $startDate = Carbon::parse($reservation->start_date)->format('d-m-Y');
                    $endDate = Carbon::parse($reservation->end_date)->format('d-m-Y');
                    return $startDate . ' ' . trans('To') . ' ' . $endDate;
                }
            })
            ->editColumn("status", function ($reservation) {
                return trans($reservation->status); 
            })
            ->make(true);
    } catch (Exception $e) {
        Log::error('Error fetching reservations data: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
        return response()->json(['error' => trans('errors.fetch_reservations_data')], 500);
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
                'totalActiveCount' => $reservations->where('status', 'active')->count(),
                'maleActiveCount' => $reservations
                    ->where('status', 'active')
                    ->where('user.gender', 'male')
                    ->count(),
                'femaleActiveCount' => $reservations
                    ->where('status', 'active')
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