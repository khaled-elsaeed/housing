<?php

namespace App\Http\Controllers\Admin\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Building;
use App\Models\Apartment;
use App\Models\Room;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\App;

class ResidentController extends Controller
{
    public function index()
    {
        try {

            $buildings = Building::pluck("number")->sort()->values()->all();
            $apartments = Apartment::pluck("number")->unique()->sort()->values()->all();

            return view("admin.residents.index", compact("buildings", "apartments"));

        } catch (Exception $e) {

            Log::error("Error retrieving resident page data: " . $e->getMessage(), [
                "exception" => $e,
                "stack" => $e->getTraceAsString(),
            ]);

            return response()->view("errors.505", [
                "message" => "An error occurred while loading the data.",
            ]);
        }
    }

    public function fetchResidents(Request $request)
    {
        $currentLang = App::getLocale();

        try {

            // Base query for residents with active reservations
            $query = User::role("resident")
                ->whereHas("reservations", function ($q) {
                    $q->where("status", "active");
                })
                ->with(["reservations.room.apartment.building", "reservations.room.apartment", "student", "student.faculty"]);

            // Search by name, national ID
            if ($request->filled("customSearch")) {
                $searchTerm = $request->get("customSearch");
                $query->where(function ($q) use ($searchTerm, $currentLang) {
                    $q->whereHas("student", function ($q) use ($searchTerm, $currentLang) {
                        $q
                            ->where("name_" . ($currentLang == "ar" ? "ar" : "en"), "like", "%" . $searchTerm . "%")
                            ->orWhere("national_id", "like", "%" . $searchTerm . "%");
                    });
                });
            }

            // Filter by building
            if ($request->filled("building_number")) {
                $buildingNumber = $request->get("building_number");
                $query->whereHas("reservations.room.apartment.building", function ($q) use ($buildingNumber) {
                    $q->where("number", $buildingNumber);
                });
            }

            // Filter by apartment
            if ($request->filled("apartment_number")) {
                $apartmentNumber = $request->get("apartment_number");
                $query->whereHas("reservations.room.apartment", function ($q) use ($apartmentNumber) {
                    $q->where("number", $apartmentNumber);
                });
            }


            // Count total and filtered records
            $totalRecords = User::role("resident")
                ->whereHas("reservations", function ($q) {
                    $q->where("status", "active");
                })
                ->count();
            $filteredRecords = $query->count();

            // Pagination
            $start = $request->get("start", 0);
            $length = $request->get("length", 10);
            $residents = $query
                ->skip($start)
                ->take($length)
                ->get();

            // Format response
            return response()->json([
                "draw" => $request->get("draw"),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $residents->map(function ($resident) use ($currentLang) {
                    $location = method_exists($resident, "getLocationDetails")
                        ? $resident->getLocationDetails()
                        : [
                            "building" => "N/A",
                            "apartment" => "N/A",
                            "room" => "N/A",
                        ];

                    $locationString = "Building " . $location["building"] . " - Apartment " . $location["apartment"] . " - Room " . $location["room"];

                    return [
                        "resident_id" => $resident->id,
                        "name" => $resident->student ? $resident->student->{"name_" . ($currentLang == "ar" ? "ar" : "en")} : "N/A",
                        "national_id" => $resident->student->national_id ?? "N/A",
                        "location" => $locationString,
                        "faculty" => $resident->student && $resident->student->faculty ? $resident->student->faculty->{"name_" . ($currentLang == "ar" ? "ar" : "en")} : "N/A",
                        "mobile" => $resident->student->mobile ?? "N/A",
                    ];
                }),
            ]);
        } catch (Exception $e) {
            Log::error("Error fetching residents data: " . $e->getMessage());
            return response()->json(["error" => "Failed to fetch residents data."], 500);
        }
    }

    public function getSummary()
    {
        try {
            // Total count of residents
            $totalResidents = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->whereIn("status", ["active", "upcoming"]);
                })
                ->count();

            // Total male residents count
            $totalMaleCount = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->where("gender", "male")->whereIn("status", ["active", "upcoming"]);
                })
                ->count();

            // Total female residents count
            $totalFemaleCount = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->where("gender", "female")->whereIn("status", ["active", "upcoming"]);
                })
                ->count();

            // Get last updated timestamp for residents
            $lastUpdateOverall = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->whereIn("status", ["active", "upcoming"]);
                })
                ->latest("updated_at")
                ->value("updated_at");

            // Get last updated timestamp for male residents
            $lastUpdateMaleResidents = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->where("gender", "male")->whereIn("status", ["active", "upcoming"]);
                })
                ->latest("updated_at")
                ->value("updated_at");

            // Get last updated timestamp for female residents
            $lastUpdateFemaleResidents = User::role("resident")
                ->whereHas("reservations", function ($query) {
                    $query->where("gender", "female")->whereIn("status", ["active", "upcoming"]);
                })
                ->latest("updated_at")
                ->value("updated_at");

            // Return the summary in JSON format
            return response()->json([
                "totalResidents" => $totalResidents,
                "totalMaleCount" => $totalMaleCount,
                "totalFemaleCount" => $totalFemaleCount,
                "lastUpdateOverall" => $lastUpdateOverall,
                "lastUpdateMaleResidents" => $lastUpdateMaleResidents,
                "lastUpdateFemaleResidents" => $lastUpdateFemaleResidents,
            ]);
        } catch (Exception $e) {
            // Log error and return an appropriate response
            Log::error("Error fetching summary data: " . $e->getMessage(), [
                "exception" => $e,
            ]);
            return response()->json(["error" => "Error fetching summary data"], 500);
        }
    }
}
