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

class NotRegisterTransStudentSeeder extends Seeder
{
    public function run(): void
    {
        $residentRole = Role::firstOrCreate(['name' => 'resident']);

        $filePath = database_path('data/transfered_students_not_reserved.csv');

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

            $archiveId = $this->insertArchiveData($data);

            $user = $this->createUser($data, $residentRole);

            $userNationalLinkData[] = $this->createUserNationalLinkData($user, $archiveId, $data[5]);

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
        
        

        $dataToInsert = [
           'name_en' => isset($data[1]) && strtoupper($data[1]) !== "NULL" ? $data[1] : null,
'name_ar' => isset($data[0]) && strtoupper($data[0]) !== "NULL" ? $data[0] : null,
'academic_id' =>  isset($data[3]) && strtoupper($data[3]) !== "NULL" ? $data[3] : null,
'national_id' => isset($data[5]) && strtoupper($data[5]) !== "NULL" ? $data[5] : null,
'faculty' => isset($data[22]) && strtoupper($data[22]) !== "NULL" ? $data[22] : null,
'program' => null, // No "program" field in the provided column list
'score' => isset($data[17]) && strtoupper($data[17]) !== "NULL" ? $data[17] : null,
'percent' => isset($data[18]) && is_numeric($data[18]) && strtoupper($data[18]) !== "NULL" ? $data[18] : null,
'academic_email' => isset($data[4]) && strtoupper($data[4]) !== "NULL" ? $data[4] : null,
'mobile' => isset($data[6]) && strtoupper($data[6]) !== "NULL" ? $data[6] : null,
'whatsapp' => isset($data[7]) && strtoupper($data[7]) !== "NULL" ? $data[7] : null,
'gender' => isset($data[9]) && strtoupper($data[9]) !== "NULL" ? $data[9] : null,
'governorate' => isset($data[11]) && strtoupper($data[11]) !== "NULL" ? $data[11] : null,
'city' => isset($data[12]) && strtoupper($data[12]) !== "NULL" ? $data[12] : null,
'street' => isset($data[13]) && strtoupper($data[13]) !== "NULL" ? $data[13] : null,
'parent_name' => isset($data[14]) && strtoupper($data[14]) !== "NULL" ? $data[14] : null,
'parent_mobile' => isset($data[15]) && strtoupper($data[15]) !== "NULL" ? $data[15] : null,
'parent_email' => isset($data[16]) && strtoupper($data[16]) !== "NULL" ? $data[16] : null,

         'parent_is_abroad' => isset($data[19]) && strtoupper($data[19]) !== "NULL" && $data[19] !== 'مصر' ? '1' : '0',
'parent_abroad_country' => (isset($data[19]) && strtoupper($data[19]) !== "NULL" && $data[19] !== 'مصر') 
    ? $data[19] 
    : null,


            'cert_type' => isset($data[20]) && strtoupper($data[20]) !== "NULL" ? $data[20] : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

      
            $dataToInsert['birthdate'] = null;
       

        // Insert into the database and return the ID
        return DB::table('university_archives')->insertGetId($dataToInsert);
    }

    private function createUser(array $data, $residentRole)
    {
         // Extract and format English names
        $nameEn = explode(' ', $data[1] ?? '');
        $firstNameEn = isset($nameEn[0]) ? ucwords(strtolower($nameEn[0])) : null;
        $lastNameEn = isset($nameEn) ? ucwords(strtolower(end($nameEn))) : null;

        $nameAr = explode(' ', $data[0] ?? '');
        $firstNameAr = $nameAr[0] ?? null;
        $lastNameAr = end($nameAr) ?: null;

            
            $academicEmail = isset($data[4]) && strtoupper($data[4]) !== "NULL" ? $data[4] : null;
        
        

        $user = \App\Models\User::create([
            'email' => $academicEmail,
            'password' => Hash::make($data[5]),
            'first_name_en' => $firstNameEn,
            'last_name_en' => $lastNameEn,
            'first_name_ar' => $firstNameAr,
            'last_name_ar' => $lastNameAr,
            'gender' => $data[9],
            'status' => 'active',
            'is_verified' => 1,
            'profile_completed' => 0,
            'profile_completed_at' => null,
            'can_complete_late' => 0,
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
