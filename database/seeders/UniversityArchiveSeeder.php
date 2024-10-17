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
            'national_id' => '12345678901234', // Example Egyptian National ID
            'mobile' => '01012345678', // Example mobile number
            'birthdate' => '2000-01-01', // Example birthdate
            'gender' => 'male', // Gender
            'city' => 1, // Assuming there's a city with ID 1
            'street' => '123 Main St', // Example street
            'parent_name' => 'Mohamed Ali',
            'parent_email' => 'mohamed.ali@example.com',
            'parent_mobile' => '01234567890',
            'parent_is_abroad' => 0,
            'parent_abroad_country_id' => null, // Not applicable since parent is not abroad
            'sibling_name' => 'Fatma Ali', // Example sibling
            'sibling_national_id' => '98765432101234', // Example sibling national ID
            'sibling_faculty_id' => 1, // Example sibling faculty
            'sibling_mobile' => '01098765432', // Example sibling mobile number
            'sibling_gender' => 'female', // Sibling gender
            'has_sibling' => 1, // Indicates this student has a sibling
            'program_id' => 5, // Assuming there's a program with ID 1
            'score' => 85.500, // Example score
            'percent' => 80.00, // Example percent
            'academic_email' => 'ahmed.ali@university.edu', // Example academic email
            'cert_type' => 'High School Diploma', // Example certificate type
            'cert_country' => 'Egypt', // Example certificate country
            'cert_year' => 2018, // Example certificate year
            'is_new_comer' => 0, // Indicates if the student is a newcomer
        ]);
    }
}
