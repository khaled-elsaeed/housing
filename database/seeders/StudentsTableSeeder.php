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
                DB::table('students')->insert([
            
                'user_id' => 1,                 'name_en' => 'Khaled Zahran',
                'name_ar' => 'خالد زهران',
                'national_id' => '12345678901234',                 'mobile' => '01012345678',                 'birthdate' => '2000-01-01',                 'gender' => 'male',                 'city' => 10, // Assuming city ID 1 exists in cities table
                'street' => '123 Main St',                 'profile_completed' => 1,                 'profile_completed_at' => Carbon::now(),                 'can_complete_late' => 0,                 'university_Archive_id' => 1, // Assuming university archive ID 1 exists
                'created_at' => now(),
                'updated_at' => now(),
            
            
        ]);
    }
}
