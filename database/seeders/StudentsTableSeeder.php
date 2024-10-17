<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample data for seeding
        DB::table('students')->insert([
            
                'user_id' => 1, // Assuming this user ID exists in the users table
                'name_en' => 'Khaled Zahran',
                'name_ar' => 'خالد زهران',
                'national_id' => '12345678901234', // Unique National ID
                'mobile' => '01012345678', // Unique mobile number
                'birthdate' => '2000-01-01', // Example birthdate
                'gender' => 'male', // Gender
                'city' => 10, // Assuming city ID 1 exists in cities table
                'street' => '123 Main St', // Example street
                'profile_completed' => 1, // Indicates if profile is completed
                'profile_completed_at' => Carbon::now(), // Current timestamp
                'can_complete_late' => 0, // Indicates if can complete late
                'university_archieve_id' => 1, // Assuming university archive ID 1 exists
                'created_at' => now(),
                'updated_at' => now(),
            
            
        ]);
    }
}
