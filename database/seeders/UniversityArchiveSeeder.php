<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UniversityArchive;

class UniversityArchiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UniversityArchive::create([
            'name_en' => 'khaled zahran hamed',
            'name_ar' => 'خالد زهران حامد',
            'national_id' => '12345678901234',             'mobile' => '01012345678',             'birthdate' => '2000-01-01',             'gender' => 'male',             'city' => 1,             'street' => '123 Main St',             'parent_name' => 'Mohamed Ali',
            'parent_email' => 'mohamed.ali@example.com',
            'parent_mobile' => '01234567890',
            'parent_is_abroad' => 0,
            'parent_abroad_country_id' => null,             'sibling_name' => 'Fatma Ali',             'sibling_national_id' => '98765432101234',             'sibling_faculty_id' => 1,             'sibling_mobile' => '01098765432',             'sibling_gender' => 'female',             'has_sibling' => 1,             'program_id' => 5,             'score' => 85.500,             'percent' => 80.00,             'academic_email' => 'ahmed.ali@university.edu',             'cert_type' => 'High School Diploma',             'cert_country' => 'Egypt',             'cert_year' => 2018,             'is_new_comer' => 0,         ]);
    }
}
