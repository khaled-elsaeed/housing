<?php

namespace App\Http\Controllers\Admin\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Program;
use App\Models\Faculty;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Reservation;
use App\Models\UniversityArchive;
use App\Models\UserNationalLink;
use App\Models\Student;
use App\Models\Building;
use App\Models\Room;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Exception;
use Illuminate\Support\Facades\App;


class ResidentController extends Controller
{
    /**
     * Display the residents index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            return view('admin.residents.index');
        } catch (Exception $e) {
            Log::error('Error retrieving resident page data: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->view('errors.505');
        }
    }

    /**
     * Fetch residents with search, pagination, and filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchResidents(Request $request)
    {
        $currentLang = App::getLocale(); // Get current locale

        try {
            $query = User::role('resident')
                ->whereHas('student', function ($q) {
                    $q->where('application_status', 'final_accepted');
                })
                ->with(['student', 'student.faculty']);

                if ($request->filled('customSearch')) {
                    $searchTerm = $request->get('customSearch');
                    $query->where(function ($q) use ($searchTerm, $currentLang) {
                        $q->whereHas('student', function ($q) use ($searchTerm, $currentLang) {
                            $q->where('name_' . ($currentLang == 'ar' ? 'ar' : 'en'), 'like', '%' . $searchTerm . '%')
                              ->orWhere('national_id', 'like', '%' . $searchTerm . '%')
                              ->orWhere('mobile', 'like', '%' . $searchTerm . '%');
                        });
                    });
                }
                

            $filteredQuery = clone $query;
            $totalRecords = User::role('resident')->count();
            $filteredRecords = $filteredQuery->count();

            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $residents = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->get('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $residents->map(function ($resident) use ($currentLang) {
                    $location = method_exists($resident, 'getLocationDetails') 
                        ? $resident->getLocationDetails() 
                        : ['building' => 'N/A', 'apartment' => 'N/A', 'room' => 'N/A'];
                    
                    // Construct the location string
                    $locationString = __(
                        'pages.admin.apartment.building'
                    ) . ' ' . $location['building'] . ' - ' . __(
                        'pages.admin.rooms.apartment'
                    ) . ' ' .$location['apartment'] . ' - ' . __(
                        'pages.admin.rooms.room'
                    ) . ' ' . $location['room'];

                    return [
                        'resident_id' => $resident->id,
                        'name' => $resident->student->{'name_' . ($currentLang == 'ar' ? 'ar' : 'en')} ?? 'N/A',  
                        'national_id' => $resident->student->national_id ?? 'N/A',
                        'location' => $locationString,
                        'faculty' => $resident->student->faculty->{'name_' . ($currentLang == 'ar' ? 'ar' : 'en')} ?? 'N/A',  
                        'mobile' => $resident->student->mobile ?? 'N/A',
                        'registration_date' => $resident->created_at ? $resident->created_at->format('F j, Y, g:i A') : 'N/A',
                    ];
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching residents data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch residents data.'], 500);
        }
    }

    /**
     * Fetch summary data for residents.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSummary()
    {
        try {
            $totalResidents = User::role('resident')
                ->whereHas('student', function($query) {
                    $query->where('application_status', 'final_accepted');
                })
                ->count();
    
            $totalMaleCount = User::role('resident')
                ->whereHas('student', function($query) {
                    $query->where('gender', 'male')->where('application_status', 'final_accepted');
                })
                ->count();
    
            $totalFemaleCount = User::role('resident')
                ->whereHas('student', function($query) {
                    $query->where('gender', 'female')->where('application_status', 'final_accepted');
                })
                ->count();
    
            return response()->json([
                'totalResidents' => $totalResidents,
                'totalMaleCount' => $totalMaleCount,
                'totalFemaleCount' => $totalFemaleCount,
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching summary data: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Error fetching summary data'], 500);
        }
    }

    /**
     * Download residents data in Excel format.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadResidentsExcel()
    {
        try {
            $export = new ResidentsExport();
            return $export->downloadExcel();
        } catch (Exception $e) {
            Log::error('Error exporting residents to Excel', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to export residents to Excel'], 500);
        }
    }

    /**
     * Download residents data in PDF format.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadResidentsPDF()
    {
        try {
            $export = new ResidentsExport();
            $pdf = $export->downloadPDF();
            return $pdf->download('residents_report.pdf');
        } catch (Exception $e) {
            Log::error('Error exporting residents to PDF', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to export residents to PDF'], 500);
        }
    }

    /**
     * Get more detailed information for a specific resident.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResidentMoreDetails($id)
    {
        try {
            $resident = User::find($id);

            if (!$resident) {
                return response()->json(['error' => 'Resident not found', 'user' => $id], 404);
            }

            $details = [
                'faculty' => optional($resident->student->faculty)->name_en ?? 'Not available',
                'program' => optional($resident->student->program)->name_en ?? 'Not available',
                'score' => optional($resident->student->universityArchive)->score ?? 'Not available',
                'percent' => optional($resident->student->universityArchive)->percent ?? 'Not available',
                'governorate' => optional($resident->student->governorate)->name_ar ?? 'Not available',
                'city' => optional($resident->student->city)->name_ar ?? 'Not available',
                'street' => $resident->student->street ?? 'Not available',
            ];

            return response()->json([
                'success' => true,
                'data' => $details
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching resident details', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'resident_id' => $id
            ]);
            return response()->json(['error' => 'Resident not found or an error occurred', 'user' => $id], 404);
        }
    }

    /**
     * Display the resident creation page with required data.
     *
     * @return \Illuminate\View\View
     */
    public function create()
{
    try {
        // Fetch the necessary data for the form
        $governorates = Governorate::all();
        $cities = City::all();
        $faculties = Faculty::all();
        $programs = Program::all();
        
        // Correcting the Building query and ensuring you retrieve buildings with empty rooms
        $buildings = Building::whereHas('apartments.rooms', function($query) {
            $query->where('full_occupied', '!=', 1); // Assuming 'full_occupied' indicates if a room is not fully occupied
        })->get(); // Add get() to execute the query and retrieve the results

        // Pass all necessary data to the view
        return view('admin.residents.create', compact('governorates', 'cities', 'faculties', 'programs', 'buildings'));
    } catch (Exception $e) {
        // Log the error if something goes wrong
        Log::error('Error retrieving resident create page data: ' . $e->getMessage(), [
            'exception' => $e,
            'stack' => $e->getTraceAsString(),
        ]);

        // Return an error page if an exception occurs
        return response()->view('errors.505', [], 505);
    }
}


    public function getStudentData(Request $request)
    {
        $national_id = $request->input('national_id');
        $user = DB::table('nmu_archive')->where('national_id', $national_id)->first();
    
        if ($user) {
            return response()->json(['success' => true, 
        'resident' => [
                'name_en' => $user->name_en,
                'name_ar' => $user->name_ar,
                'academic_id' => $user->academic_id,
                'academic_email' => $user->academic_email,
            ],]);
            
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Resident details not found.',
        ]);
    }

    public function createResident(Request $request)
    {
        DB::beginTransaction(); 
    
        try {

            $validateIsUniversityStudent = $this->isUniversityStudent($request->national_id);

            $userNationalLinkExists = UserNationalLink::where('national_id', $request->national_id)->exists();

            if ($userNationalLinkExists) {
                DB::rollBack();
                return response()->json(['error' => 'National ID is already linked to a user.'], 400);
            }

            if (!$validateIsUniversityStudent['success']) {
                DB::rollBack(); 
                return response()->json(['error' => $validateIsUniversityStudent['message']], 404);
            }
    
            $names = $this->extractNamesPart($validateIsUniversityStudent['data']);
    
            $user = $this->createUser($validateIsUniversityStudent['data'], $names, $request);
    
            $universityArchive = $this->createUniversityArchive($validateIsUniversityStudent['data']);
    
            $this->createUserNationalLink($user->id, $validateIsUniversityStudent['data']->national_id, $universityArchive->id);
    
            $student = $this->createStudent($user->id, $validateIsUniversityStudent['data'], $request, $universityArchive->id);
    
            $reservation = $this->createReservation($user->id, $request->room_id);
    
            $this->createFeeInvoice($reservation->id);
    
            DB::commit();
    
            return response()->json(['success' => true], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            \Log::error('Error creating resident: ' . $e->getMessage());
    
            return response()->json(['error' => 'An error occurred while creating the resident.'], 500);
        }
    }
    
    private function isUniversityStudent($national_id)
    {
        $user = DB::table('nmu_archive')->where('national_id', $national_id)->first();
    
        if ($user) {
            return ['success' => true, 'data' => $user];
        }
    
        return ['success' => false, 'message' => 'Student not found'];
    }
    
    private function extractNamesPart($name)
    {
        $name_en = array_filter(explode(' ', trim($name->name_en))); 
        $name_ar = array_filter(explode(' ', trim($name->name_ar))); 
    
        $first_name_en = $name_en[0] ?? ''; 
        $last_name_en = count($name_en) > 1 ? end($name_en) : ''; 
    
        $first_name_ar = $name_ar[0] ?? ''; 
        $last_name_ar = count($name_ar) > 1 ? end($name_ar) : ''; 
    
        $names = [
            'first_name_en' => $first_name_en,
            'last_name_en' => $last_name_en,
            'first_name_ar' => $first_name_ar,
            'last_name_ar' => $last_name_ar,
        ];
        
        return $names;
    }
    
    
    private function createUser($studentData, $names, $request)
    {
        $user = User::create([
            'email' => $studentData->academic_email,
            'password' => Hash::make($studentData->national_id), 
            'status' => 'active',
            'is_verified' => 1,
            'profile_completed' => 1,
            'can_complete_late' => 0,
            'gender' => $this->parseNationalID($studentData->national_id)['gender'],
            'first_name_ar' => $names['first_name_ar'],
            'last_name_ar' => $names['last_name_ar'],
            'first_name_en' => $names['first_name_en'],
            'last_name_en' => $names['last_name_en'],
        ]);
        $user->assignRole('resident');
        
        return $user;
    }
    
    
    private function createUniversityArchive($studentData)
    {
        return UniversityArchive::create([
            'academic_id' => $studentData->academic_id,
            'national_id' => $studentData->national_id,
            'name_en' => $studentData->name_en, 
            'name_ar' => $studentData->name_ar,
            'academic_email' => $studentData->academic_email,
        ]);
    }
    
    private function createUserNationalLink($userId, $nationalId, $universityArchiveId)
    {
        \Log::error('archive resident: ' . $universityArchiveId);

        UserNationalLink::create([
            'user_id' => $userId,
            'national_id' => $nationalId,
            'university_archive_id' => $universityArchiveId,
        ]);
    }
    
    private function createStudent($userId, $studentData, $request, $universityArchiveId)
    {
        return Student::create([
            'user_id' => $userId,
            'name_en' => $studentData->name_ar, // Assuming this is swapped, please check
            'name_ar' => $studentData->name_en,
            'national_id' => $studentData->national_id,
            'academic_id' => $studentData->academic_id,
            'mobile' => $request->mobile,
            'birthdate' => $this->parseNationalID($studentData->national_id)['birthDate'],
            'gender' => $this->parseNationalID($studentData->national_id)['gender'],
            'governorate_id' => $request->governorate_id,
            'city_id' => $request->city_id,
            'street' => $request->street,
            'faculty_id' => $request->faculty_id,
            'program_id' => $request->program_id,
            'university_archive_id' => $universityArchiveId,
            'application_status' => 'final_accepted',
        ]);
    }

    private function parseNationalID($nationalID) {

        if (strlen($nationalID) !== 14) {
            return ["error" => "Invalid National ID length. It must be 14 digits."];
        }
    
        $centuryCode = intval(substr($nationalID, 0, 1)); 
        $birthDatePart = substr($nationalID, 1, 6); 
        $serialNumber = intval(substr($nationalID, 9, 4)); 
    
        $century = ($centuryCode === 2) ? 1900 : 2000;
    
        $year = $century + intval(substr($birthDatePart, 0, 2));
        $month = intval(substr($birthDatePart, 2, 2));
        $day = intval(substr($birthDatePart, 4, 2));
    
        if (!checkdate($month, $day, $year)) {
            return ["error" => "Invalid birth date in the National ID."];
        }
    
        $birthDate = sprintf("%04d-%02d-%02d", $year, $month, $day); 
    
        $gender = ($serialNumber % 2 === 0) ? "Female" : "Male";
    
        return [
            "birthDate" => $birthDate,
            "gender" => $gender
        ];
    }
    
    private function createReservation($userId, $roomId)
    {
        $room = Room::find($roomId);
    
        if (!$room) {
            throw new \Exception('Room not found.');
        }
    
        if ($room->current_occupancy >= $room->max_occupancy) {
            throw new \Exception('Room is fully occupied.');
        }
    
        $reservation = Reservation::create([
            'user_id' => $userId,
            'room_id' => $roomId,
            'year' => 2024,
            'term' => 'first_term',
            'status' => 'confirmed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        $room->increment('current_occupancy');
        if($room->current_occupancy == $room->max_occupancy) {
            $room->update(['full_occupied' => 1]);
        }
            return $reservation;
    }
    

    
    private function createFeeInvoice($reservationId)
    {
        return DB::table('invoices')->insertGetId([
            'reservation_id' => $reservationId,
            'amount' => 15000,  
            'status' => 'unpaid',  
            'category' => 'fee',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    
}
