<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define full Arabic and English names
        $fullNameAr = 'خالد زهران';
        $fullNameEn = 'Khaled Zahran';

        // Split the Arabic name into first and last names
        $namePartsAr = explode(' ', $fullNameAr);
        $first_name_ar = $namePartsAr[0]; // First name in Arabic
        $last_name_ar = isset($namePartsAr[1]) ? $namePartsAr[1] : ''; // Last name in Arabic

        // Split the English name into first and last names
        $namePartsEn = explode(' ', $fullNameEn);
        $first_name_en = $namePartsEn[0]; // First name in English
        $last_name_en = isset($namePartsEn[1]) ? $namePartsEn[1] : ''; // Last name in English

        // Create the user and store first and last names separately
        $user = User::create([
            'email' => 'khaled@gmail.com',
            'first_name_ar' => $first_name_ar,
            'last_name_ar' => $last_name_ar,
            'first_name_en' => $first_name_en,
            'last_name_en' => $last_name_en,
            'password' => bcrypt('password'),
            'is_active' => true,
            'profile_picture' => null,
            'last_login' => now(),
            'is_verified' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign the 'admin' role to the user
        $user->assignRole('admin');
    }
}
