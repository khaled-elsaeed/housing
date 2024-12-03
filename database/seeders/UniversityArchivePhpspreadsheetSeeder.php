<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;
use App\Models\Student;
use App\Models\Reservation;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class UniversityArchivePhpspreadsheetSeeder extends Seeder
{
    public function run(): void
    {
        $residentRole = Role::firstOrCreate(['name' => 'resident']);

        $filePath = database_path('data/reservations.csv');

        $spreadsheet = $this->loadSpreadsheet($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $userNationalLinkData = [];
        $rowCount = $sheet->getHighestRow();

        foreach ($sheet->getRowIterator(2) as $row) {
            $data = $this->getRowData($row);


            $existingUser = $this->findExistingUser($data);

            if ($existingUser) {
                continue;
            }

            $importantDataFound = $this->impData($data);

            if (!$importantDataFound) {
                continue; // Skip the rest of the loop and move to the next iteration
            }
            

            $archiveId = $this->insertArchiveData($data);

            $user = $this->createUser($data, $residentRole);

            $userNationalLinkData[] = $this->createUserNationalLinkData($user, $archiveId, $data[5]);

            $this->createStudentRecord($user, $data, $archiveId);

            $this->insertAdditionalData($user, $data);
        }

        DB::table('user_national_link')->insert($userNationalLinkData);
    }

    private function loadSpreadsheet(string $filePath)
    {
        return IOFactory::load($filePath);
    }

    private function getRowData($row): array
    {
        $cells = $row->getCellIterator();
        $cells->setIterateOnlyExistingCells(false);

        $data = [];
        foreach ($cells as $cell) {
            $data[] = $cell->getValue();
        }

        return $data;
    }

    private function findExistingUser(array $data)
    {
        return DB::table('university_archives')
            ->where('national_id', $data[5]) 
            ->first(); 
    }

    private function impData(array $data)
{
    // Check if $data[25] satisfies the condition and $data[5] is set
    if ((strlen($data[25]) > 9 || $data[25] == "NULL") && isset($data[5])) {
        
        // Retrieve the academic email
        $academicEmail = $this->getStudentAcademicEmail($data[5]);
        
        // If the email is null, return false
        if ($academicEmail === null) {
            return false;
        }
    }
    
    return true;
}

    
    
    

    

    private function insertArchiveData(array $data): int
    {
        if (((strlen($data[25]) > 9 || $data[25] == "NULL") && isset($data[5]))) {
            $academicId = $this->getStudentAcademicId($data[5]);
            $academicEmail = $this->getStudentAcademicEmail($data[5]);
        } else {
            
            $academicId = isset($data[34]) && strtoupper($data[34]) !== "NULL" ? $data[34] : null;
            $academicEmail = isset($data[40]) && strtoupper($data[40]) !== "NULL" ? $data[40] : null;
        }
        

        $dataToInsert = [
            'name_en' => isset($data[32]) && strtoupper($data[32]) !== "NULL" ? $data[32] : null,
            'name_ar' => isset($data[33]) && strtoupper($data[33]) !== "NULL" ? $data[33] : null,
            'academic_id' => $academicId,
            'national_id' => isset($data[5]) && strtoupper($data[5]) !== "NULL" ? $data[5] : null,
            'faculty' => isset($data[13]) && strtoupper($data[13]) !== "NULL" ? $data[13] : null,
            'program' => isset($data[14]) && strtoupper($data[14]) !== "NULL" ? $data[14] : null,
            'score' => isset($data[38]) && strtoupper($data[38]) !== "NULL" ? $data[38] : null,
            'percent' => isset($data[39]) && is_numeric($data[39]) && strtoupper($data[39]) !== "NULL" ? $data[39] : null,
            'academic_email' => $academicEmail,
            'mobile' => isset($data[44]) && strtoupper($data[44]) !== "NULL" ? $data[44] : null,
            'whatsapp' => isset($data[45]) && strtoupper($data[45]) !== "NULL" ? $data[45] : null,
            'gender' => isset($data[4]) && strtoupper($data[4]) !== "NULL" ? $data[4] : null,
            'governorate' => isset($data[6]) && strtoupper($data[6]) !== "NULL" ? $data[6] : null,
            'city' => isset($data[7]) && strtoupper($data[7]) !== "NULL" ? $data[7] : null,
            'street' => isset($data[8]) && strtoupper($data[8]) !== "NULL" ? $data[8] : null,
            'parent_name' => isset($data[70]) && strtoupper($data[70]) !== "NULL" ? $data[70] : null,
            'parent_mobile' => isset($data[73]) && strtoupper($data[73]) !== "NULL" ? $data[73] : null,
            'parent_email' => isset($data[72]) && strtoupper($data[72]) !== "NULL" ? $data[72] : null,
            'parent_is_abroad' => isset($data[74]) && strtoupper($data[74]) !== "NULL" ? $data[74] : null,
            'parent_abroad_country' => (isset($data[74]) && $data[74] == '1' && isset($data[75]) && strtoupper($data[75]) !== "NULL") 
                ? $data[75] 
                : null,

            'cert_type' => isset($data[56]) && strtoupper($data[56]) !== "NULL" ? $data[56] : null,
            'cert_country' => isset($data[57]) && strtoupper($data[57]) !== "NULL" ? $data[57] : null,
            'cert_year' => isset($data[58]) && is_numeric($data[58]) && strtoupper($data[58]) !== "NULL" ? (int) $data[58] : null,
            'sibling_name' => isset($data[83]) && strtoupper($data[83]) !== "NULL" ? $data[83] : null,
            'sibling_faculty' => isset($data[85]) && strtoupper($data[85]) !== "NULL" ? $data[85] : null,
            'has_sibling' => strtoupper($data[10]) !== "NULL" ? $data[10] : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (isset($data[46]) && $data[46] !== null && $data[46] !== 'NULL') {
            

            $dataToInsert['birthdate'] = $data[46]; // Insert the birthdate if it's not null
        } else {
            $dataToInsert['birthdate'] = null; // Explicitly set it to NULL
        }

        // Insert into the database and return the ID
        return DB::table('university_archives')->insertGetId($dataToInsert);
    }

    private function createUser(array $data, $residentRole): \App\Models\User
    {
         // Extract and format English names
        $nameEn = explode(' ', $data[32] ?? '');
        $firstNameEn = isset($nameEn[0]) ? ucwords(strtolower($nameEn[0])) : null;
        $lastNameEn = isset($nameEn) ? ucwords(strtolower(end($nameEn))) : null;

        $nameAr = explode(' ', $data[33] ?? '');
        $firstNameAr = $nameAr[0] ?? null;
        $lastNameAr = end($nameAr) ?: null;

        if (((strlen($data[25]) > 9 || $data[25] == "NULL") && isset($data[5]))) {
            $academicEmail = $this->getStudentAcademicEmail($data[5]);
        } else {
            
            $academicEmail = isset($data[40]) && strtoupper($data[40]) !== "NULL" ? $data[40] : null;
        }
        

        $user = \App\Models\User::create([
            'email' => $academicEmail,
            'password' => Hash::make($data[5]),
            'first_name_en' => $firstNameEn,
            'last_name_en' => $lastNameEn,
            'first_name_ar' => $firstNameAr,
            'last_name_ar' => $lastNameAr,
            'gender' => $data[4],
            'status' => 'active',
            'is_verified' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->assignRole($residentRole);

        return $user;
    }

    private function createUserNationalLinkData($user, int $archiveId, $nationalId): array
    {
        return [
            'user_id' => $user->id,
            'university_Archive_id' => $archiveId,
            'national_id' => $nationalId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function createStudentRecord($user, array $data, int $archiveId)
    {
        $faculty = $this->getFacultyId(isset($data[13]) && strtoupper(trim($data[13])) !== "NULL" ? trim($data[13]) : null);
        $program = $this->getProgramId(isset($data[14]) && strtoupper(trim($data[14])) !== "NULL" ? trim($data[14]) : null);
        $governorateId = $this->getGovernorateId(isset($data[6]) && strtoupper(trim($data[6])) !== "NULL" ? trim($data[6]) : null);
        $cityId = $this->getCityId(isset($data[7]) && strtoupper(trim($data[7])) !== "NULL" ? trim($data[7]) : null);

        if (((strlen($data[25]) > 9 || $data[25] == "NULL") && isset($data[5]))) {
            $academicId = $this->getStudentAcademicId($data[5]);
        } else {
            $academicId = isset($data[34]) && strtoupper($data[34]) !== "NULL" ? $data[34] : null;
        }

        Student::create([
            'user_id' => $user->id,
            'name_en' => ucwords(strtolower($data[1])),
            'name_ar' => $data[2],
            'national_id' => $data[5],
            'academic_id' => $academicId,
            'mobile' => $data[9],
            'birthdate' => $data[3],
            'gender' => $data[4],
            'governorate_id' => $governorateId,
            'city_id' => $cityId,
            'street' => $data[8],
            'faculty_id' => $faculty,
            'program_id' => $program,
            'profile_completed' => 1,
            'profile_completed_at' => now(),
            'can_complete_late' => 0,
            'university_Archive_id' => $archiveId,
            'application_status' => 'preliminary_accepted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertAdditionalData($user, array $data)
    {
        if (isset($data[70]) && $data[70] !== null && $data[70] !== 'NULL') {
            $this->insertParentData($user, $data);
        }

        if (isset($data[10]) && $data[10]) {
            $this->insertSiblingData($user, $data);
        }

        if (isset($data[91]) && $data[91] !== null && $data[91] !== 'NULL') {
            $this->insertEmergencyContactData($user, $data);
        }
    }

    private function insertParentData($user, array $data)
    {
        // Check living_with condition before resolving governorate and city IDs
        $governorateId = (isset($data[76]) && trim($data[76]) == '0' && isset($data[77]) && strtoupper(trim($data[77])) !== "NULL") 
        ? $this->getGovernorateId(trim($data[77])) 
        : null;

        $cityId = (isset($data[76]) && trim($data[76]) == '0' && isset($data[78]) && strtoupper(trim($data[78])) !== "NULL") 
        ? $this->getCityId(trim($data[78])) 
        : null;

        $abroadCountryId = (isset($data[74]) && trim($data[74]) == '1' && isset($data[75]) && strtoupper(trim($data[75])) !== "NULL") 
        ? $this->getCountryId(trim($data[75])) 
        : null;

    
        // Insert data into the 'parents' table
        DB::table('parents')->insert([
            'user_id' => $user->id,                     // User ID (foreign key)
            'name' => $data[70],                        // Parent's name
            'relation' => $data[71] ?? null,            // Relation to the user
            'email' => $data[72] ?? null,               // Parent's email
            'mobile' => $data[73] ?? null,              // Parent's mobile number
            'living_abroad' => isset($data[74]) && strtoupper($data[74]) !== "NULL" 
                ? $data[74] 
                : null,                                 // Is the parent living abroad
            'abroad_country_id' => $abroadCountryId,    // Resolved country ID if living abroad
            'living_with' => $data[76] ?? null,         // Living with whom
            'governorate_id' => $governorateId,         // Governorate ID from the name if living_with is 0
            'city_id' => $cityId,                       // City ID from the name if living_with is 0
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    private function insertSiblingData($user, array $data)
    {
        $faculty = $this->getFacultyId(isset($data[85]) && strtoupper(trim($data[85])) !== "NULL" ? trim($data[85]) : null);

        DB::table('siblings')->insert([
            'user_id' => $user->id,
            'name' => $data[83],
            'national_id' => $data[86],
            'faculty_id' => $faculty,
            'gender' => $data[84],
            'share_room' => $data[87],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertEmergencyContactData($user, array $data)
    {
        DB::table('emergency_contacts')->insert([
            'user_id' => $user->id,
            'relation' => $data[92],
            'name' => $data[93],
            'phone' => $data[94],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }



   // Get Faculty ID by Arabic name
   private function getFacultyId(?string $facultyName): ?int
   {
       if (!$facultyName) {
           return null;
       }

       return DB::table('faculties')
           ->where('name_ar', $facultyName)
           ->value('id');
   }

   // Get Program ID by Arabic name
   private function getProgramId(?string $programName): ?int
   {
       if (!$programName) {
           return null;
       }

       return DB::table('programs')
           ->where('name_ar', $programName)
           ->value('id');
   }

   // Get Country ID by Arabic name
   private function getCountryId(?string $countryName): ?int
   {
       if (!$countryName) {
           return null;
       }

       return DB::table('countries')
           ->where('code', $countryName)
           ->value('id');
   }

   // Get Governorate ID by Arabic name
   private function getGovernorateId(?string $governorateName): ?int
   {
       if (!$governorateName) {
           return null;
       }

       return DB::table('governorates')
           ->where('name_ar', $governorateName)
           ->value('id');
   }

   // Get City ID by Arabic name
   private function getCityId(?string $cityName): ?int
   {
       if (!$cityName) {
           return null;
       }

       return DB::table('cities')
           ->where('name_ar', $cityName)
           ->value('id');
   }


   private function getStudentAcademicEmail(?string $nationalId): ?string
   {
       // Check if the national ID is provided
       if (!$nationalId) {
           Log::info('No national ID provided for academic email lookup.');
           return null;  // If no National ID is provided, return null.
       }
   
       // Log the attempt to query the academic email using the national ID
       Log::info('Attempting to retrieve academic email for National ID: ' . $nationalId);
   
       // Query the students table to get the academic email using the National ID.
       $email = DB::table('new_student_last')
           ->where('national_id', $nationalId)
           ->value('academic_email');  // Assuming the column storing academic email is 'academic_email'.
       
       // Log the result or lack thereof
       if ($email) {
           Log::info('Found academic email: ' . $email . ' for National ID: ' . $nationalId);
       } else {
           Log::warning('No academic email found for National ID: ' . $nationalId);
       }
   
       return $email;
   }
   
   private function getStudentAcademicId(?string $nationalId): ?int
   {
       // Check if the national ID is provided
       if (!$nationalId) {
           Log::info('No national ID provided for academic ID lookup.');
           return null;  // If no National ID is provided, return null.
       }
   
       // Log the attempt to query the academic ID using the national ID
       Log::info('Attempting to retrieve academic ID for National ID: ' . $nationalId);
   
       // Query the students table to get the academic ID using the National ID.
       $academicId = DB::table('new_student_last')
           ->where('national_id', $nationalId)
           ->value('academic_id');  // Assuming the column storing academic ID is 'academic_id'.
       
       // Log the result or lack thereof
       if ($academicId) {
           Log::info('Found academic ID: ' . $academicId . ' for National ID: ' . $nationalId);
       } else {
           Log::warning('No academic ID found for National ID: ' . $nationalId);
       }
   
       return $academicId;
   }
   



}
