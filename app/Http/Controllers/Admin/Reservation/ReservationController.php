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
        try {

            return view('admin.reservation.index');
        }
        catch (Exception $e){
            Log::error('Failed to load reservations page', [
                'error' => $e->getMessage(),
                'action' => 'show_reservation_requests_page',
                'admin_id' => auth()->id(), // Log the admin performing the action
            ]);
        }
        
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
        Log::error('Failed to fetch reservations', [
            'error' => $e->getMessage(),
            'action' => 'fetch_reservations',
            'admin_id' => auth()->id(), // Log the admin performing the action
        ]);        return response()->json(['error' => trans('errors.fetch_reservations_data')], 500);
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
            Log::error('Failed to fetch reservations summary', [
                'error' => $e->getMessage(),
                'action' => 'fetch_reservations_summary',
                'admin_id' => auth()->id(), // Log the admin performing the action
            ]);
                        return response()->json(['error' => 'Failed to fetch reservation summary.'], 500);
        }
    }
    
    
}