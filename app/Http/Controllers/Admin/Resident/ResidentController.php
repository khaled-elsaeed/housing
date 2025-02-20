<?php

namespace App\Http\Controllers\Admin\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Building;
use App\Models\Apartment;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\Facades\DataTables;

class ResidentController extends Controller
{
    /**
     * Display the residents index page with building and apartment data.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $buildings = Building::pluck("number")->sort()->values()->all();
            $apartments = Apartment::pluck("number")->unique()->sort()->values()->all();

            return view("admin.residents.index", compact("buildings", "apartments"));
        } catch (Exception $e) {
            Log::error('Failed to load residents page', [
                'error' => $e->getMessage(),
                'action' => 'show_residents_page',
                'admin_id' => auth()->id(), // Log the admin performing the action
            ]);
            return response()->view("errors.500");
        }
    }

    /**
     * Fetch residents data for DataTables with optional search and filters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchResidents(Request $request)
{
    try {
        // Base query for residents with active or pending reservations
        $query = User::role("resident")
            ->whereHas("reservations", function ($q) {
                $q->whereIn("status", ["active", "pending"]);
            })
            ->with(["reservations.room.apartment.building", "reservations.room.apartment", "student", "student.faculty"])
            ->orderBy('created_at', 'asc');

        // Apply search by name or national ID
        if ($request->filled("customSearch")) {
            $search = $request->input("customSearch");
            $query->where(function ($q) use ($search) {
                $q->whereHas("student", function ($q) use ($search) {
                    $q->where("name_en", "like", "%{$search}%")
                        ->orWhere("name_ar", "like", "%{$search}%")
                        ->orWhere("national_id", "like", "%{$search}%");
                });
            });
        }

        // Apply building filter
        if ($request->filled("building_number")) {
            $buildingNumber = $request->input("building_number");
            $query->whereHas("reservations.room.apartment.building", function ($q) use ($buildingNumber) {
                $q->where("number", $buildingNumber);
            });
        }

        // Apply apartment filter
        if ($request->filled("apartment_number")) {
            $apartmentNumber = $request->input("apartment_number");
            $query->whereHas("reservations.room.apartment", function ($q) use ($apartmentNumber) {
                $q->where("number", $apartmentNumber);
            });
        }

        // Format data for DataTables
        return DataTables::of($query)
            ->editColumn("name", function ($resident) {
                return $resident->student
                    ? ($resident->student->{"name_" . (App::getLocale() == "ar" ? "ar" : "en")} ?? trans("N/A"))
                    : trans("N/A");
            })
            ->editColumn("national_id", function ($resident) {
                return $resident->student->national_id ?? trans("N/A");
            })
            ->editColumn("location", function ($resident) {
                // Get the first reservation (assuming a resident has at least one reservation)
                $reservation = $resident->reservations->first();

                if ($reservation && $reservation->room && $reservation->room->apartment && $reservation->room->apartment->building) {
                    $buildingNumber = $reservation->room->apartment->building->number ?? trans("N/A");
                    $apartmentNumber = $reservation->room->apartment->number ?? trans("N/A");
                    $roomNumber = $reservation->room->number ?? trans("N/A");

                    return trans('Building') . " " . $buildingNumber . " - " .
                    trans('Apartment') . " " . $apartmentNumber . " - " .
                    trans('Room') . " " . $roomNumber;                }

                return trans("N/A");
            })            

            ->editColumn("faculty", function ($resident) {
                return $resident->student && $resident->student->faculty
                    ? $resident->student->faculty->{"name_" . (App::getLocale() == "ar" ? "ar" : "en")}
                    : trans("N/A");
            })
            ->editColumn("phone", function ($resident) {
                return $resident->student->phone ?? trans("N/A");
            })
            
            ->make(true);
    } catch (Exception $e) {
        Log::error("Error fetching residents data", [
            "error" => $e->getMessage(),
            "action" => "fetch_residents",
            "admin_id" => auth()->id(),
        ]);
        return response()->json(["error" => "Failed to fetch residents data."], 500);
    }
}

    /**
     * Get summary statistics for residents, including counts and last update timestamps.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSummary()
    {
        try {
            // Total count of residents
            $totalResidents = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->whereIn("status", ["pending","active", "upcoming"]);
                })
                ->count();

            // Total male residents count
            $totalMaleCount = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->where("gender", "male")->whereIn("status", ["pending","active", "upcoming"]);
                })
                ->count();

            // Total female residents count
            $totalFemaleCount = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->where("gender", "female")->whereIn("status", ["pending","active", "upcoming"]);
                })
                ->count();

            // Last updated timestamp for all residents
            $lastUpdateOverall = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->whereIn("status", ["pending","active", "upcoming"]);
                })
                ->latest("updated_at")
                ->value("updated_at");

            // Last updated timestamp for male residents
            $lastUpdateMaleResidents = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->where("gender", "male")->whereIn("status", ["pending","active", "upcoming"]);
                })
                ->latest("updated_at")
                ->value("updated_at");

            // Last updated timestamp for female residents
            $lastUpdateFemaleResidents = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->where("gender", "female")->whereIn("status", ["pending","active", "upcoming"]);
                })
                ->latest("updated_at")
                ->value("updated_at");

            return response()->json([
                "totalResidents" => $totalResidents,
                "totalMaleCount" => $totalMaleCount,
                "totalFemaleCount" => $totalFemaleCount,
                "lastUpdateOverall" => formatLastUpdated($lastUpdateOverall),
                "lastUpdateMaleResidents" => formatLastUpdated($lastUpdateMaleResidents),
                "lastUpdateFemaleResidents" => formatLastUpdated($lastUpdateFemaleResidents),
            ]);
        } catch (Exception $e) {
            Log::error("Error fetching residents summary data", [
                "error" => $e->getMessage(),
                "action" => "get_residents_summary_data",
                "admin_id" => auth()->id(),
            ]);
            return response()->json(["error" => "Error fetching summary data"], 500);
        }
    }

}