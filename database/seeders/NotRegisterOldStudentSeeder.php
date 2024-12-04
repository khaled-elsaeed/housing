<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotRegisterOldStudentSeeder extends Seeder
{
    public function run(): void
    {
        $residentRole = Role::firstOrCreate(['name' => 'resident']);

        // Define the file path to the CSV
        $filePath = database_path('data/old_students_not_reserved.csv');

        // Load the spreadsheet
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $userNationalLinkData = [];
        $rowCount = $sheet->getHighestRow();

        // Iterate over each row in the spreadsheet
        foreach ($sheet->getRowIterator(2) as $row) {
            // Get the data for the row
            $data = $this->getRowData($row);

            // Check for duplicates based on National ID and Academic ID before proceeding
            if ($this->findExistingUser($data)) {
                continue; // Skip if the user already exists in the database
            }

            // Insert archive data
            $archiveId = $this->insertArchiveData($data);

            // Create a new user and assign roles
            $user = $this->createUser($data, $residentRole);

            // Prepare the data for the user-national link table
            $userNationalLinkData[] = $this->createUserNationalLinkData($user, $archiveId, $data[5]);
        }

        // Insert the user-national link data in bulk after processing all rows
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

    private function findExistingUser(array $data): bool
    {
        $nationalId = $data[5] ?? null;  // National ID from data
        $academicId = $data[1] ?? null;  // Academic ID from data
        
        // If neither National ID nor Academic ID are provided, skip the row
        if (!$nationalId && !$academicId) {
            Log::warning('Skipping row: Missing both National ID and Academic ID.');
            return true; // Skip this row if both IDs are missing
        }

        // Check if any of the IDs (National ID or Academic ID) already exists in the database
        $existingUser = DB::table('university_archives')
            ->where(function ($query) use ($nationalId, $academicId) {
                if ($nationalId) {
                    $query->orWhere('national_id', $nationalId);
                }
                if ($academicId) {
                    $query->orWhere('academic_id', $academicId);
                }
            })
            ->exists(); // Check for any matching records

        if ($existingUser) {
            Log::info("Skipping row: Existing user found with National ID ($nationalId) or Academic ID ($academicId).");
            return true; // Skip this row if an existing user is found
        }

        return false; // Continue if no existing user is found
    }

    private function insertArchiveData(array $data): int
    {
        // Insert archive data, ensuring there are no duplicate academic IDs
        $academicId = $data[1] ?? null;
        if ($academicId) {
            // Check if the academic_id already exists
            $existingArchive = DB::table('university_archives')->where('academic_id', $academicId)->first();
            if ($existingArchive) {
                Log::info("Skipping insertion: Record already exists with academic ID ($academicId).");
                return $existingArchive->id;  // Return the existing archive ID
            }
        }

        // Prepare data for the university_archives table
        $dataToInsert = [
            'name_en' => isset($data[4]) && strtoupper($data[4]) !== "NULL" ? $data[4] : null,
            'name_ar' => isset($data[3]) && strtoupper($data[3]) !== "NULL" ? $data[3] : null,
            'academic_id' => $academicId,
            'national_id' => isset($data[5]) && strtoupper($data[5]) !== "NULL" ? $data[5] : null,
            'faculty' => isset($data[0]) && strtoupper($data[0]) !== "NULL" ? $data[0] : null,
            'academic_email' => isset($data[2]) && strtoupper($data[2]) !== "NULL" ? $data[2] : null,
            'gender' => $data[7],

            'created_at' => now(),
            'updated_at' => now(),
            'birthdate' => null, // Explicitly set birthdate to NULL
        ];

        // Insert the data into university_archives and return the archive ID
        return DB::table('university_archives')->insertGetId($dataToInsert);
    }

    private function createUser(array $data, $residentRole)
    {
        // Process the name and email fields for creating a new user
        $nameEn = explode(' ', $data[4] ?? '');
        $firstNameEn = ucwords(strtolower($nameEn[0] ?? ''));
        $lastNameEn = ucwords(strtolower(end($nameEn) ?? ''));

        $nameAr = explode(' ', $data[3] ?? '');
        $firstNameAr = $nameAr[0] ?? '';
        $lastNameAr = end($nameAr) ?: '';

        $academicEmail = $data[2] ?? null;

        // Create a new user record
        $user = \App\Models\User::create([
            'email' => $academicEmail,
            'password' => Hash::make($data[5]),  // Assuming data[4] is the password (or a fallback)
            'first_name_en' => $firstNameEn,
            'last_name_en' => $lastNameEn,
            'first_name_ar' => $firstNameAr,
            'last_name_ar' => $lastNameAr,
            'gender' => $data[7],
            'status' => 'active',
            'is_verified' => 1,
            'profile_completed' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign the 'resident' role to the user
        $user->assignRole($residentRole);

        return $user;
    }

    private function createUserNationalLinkData($user, int $archiveId, $nationalId)
    {
        return [
            'user_id' => $user->id,
            'university_Archive_id' => $archiveId,
            'national_id' => $nationalId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
