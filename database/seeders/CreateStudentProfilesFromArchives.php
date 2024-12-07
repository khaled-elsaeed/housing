<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Country;
use App\Models\Faculty;

class CreateStudentProfilesFromArchives extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch users with role 'resident', incomplete profiles, and existing archive data
        $users = User::role('resident')
            ->where('profile_completed', '0')
            ->with('universityArchive') // Assuming a relationship to universityArchive
            ->get();

        foreach ($users as $user) {
            $archive = $user->universityArchive;

            // Check if archive data exists
            if ($archive) {
                DB::transaction(function () use ($user, $archive) {
                    // Fetch governorate, city, and faculty based on name_ar
                    $governorate = Governorate::where('name_ar', $archive->governorate)->first();
                    $city = City::where('name_ar', $archive->city)->first();
                    $faculty = Faculty::where('name_ar', $archive->faculty)->first();

                    // Create a new student profile using the archive data
                    Student::create([
                        'user_id' => $user->id,
                        'name_en' => $archive->name_en,
                        'name_ar' => $archive->name_ar,
                        'national_id' => $archive->national_id,
                        'academic_id' => $archive->academic_id,
                        'mobile' => $user->mobile ?: null,
                        'birthdate' => $user->birthdate ?: null,
                        'gender' => $user->gender,
                        'governorate_id' => $governorate?->id,
                        'city_id' => $city?->id,
                        'street' => $archive->street ?? null,
                        'faculty_id' => $faculty?->id,
                        'program_id' => $archive->program_id ?? null,
                        'university_archive_id' => $archive->id,
                        'application_status' => 'final_accepted',
                    ]);

                    // Fetch the country based on the abroad country code
                    $abroadCountry = Country::where('code', $archive->parent_abroad_country)->first();

                    // Check if parent data exists and insert it using DB facade
                    if (isset($archive->parent_name) && strtoupper($archive->parent_name) !== "NULL") {
                        DB::table('parents')->insert([
                            'user_id' => $user->id, // Associate the parent with the user

                            'name' => $archive->parent_name,
                            'mobile' => strtoupper($archive->parent_mobile) !== "NULL" ? $archive->parent_mobile : null,
                            'email' => strtoupper($archive->parent_email) !== "NULL" ? $archive->parent_email : null,
                            'living_abroad' => strtoupper($archive->parent_is_abroad) !== "NULL" ? $archive->parent_is_abroad : null,
                            'abroad_country_id' => $abroadCountry?->id,
                            'created_at' => now(), // Ensure timestamps are handled
                            'updated_at' => now(),
                        ]);
                    }

                    // Log info for successful profile creation
                    $this->command->info("Student profile created for User ID: {$user->id}");

                    // Mark the user's profile as completed
                    $user->update(['profile_completed' => '1']);
                });
            } else {
                // Log if no archive data is found
                $this->command->warn("No archive data found for User ID: {$user->id}");
            }
        }
    }
}
