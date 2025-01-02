<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define first user details
        $fullNameAr1 = 'أحمد أمام';
        $fullNameEn1 = 'Ahmed Elemam';

        $namePartsAr1 = explode(' ', $fullNameAr1);
        $first_name_ar1 = $namePartsAr1[0];
        $last_name_ar1 = $namePartsAr1[1] ?? '';

        $namePartsEn1 = explode(' ', $fullNameEn1);
        $first_name_en1 = $namePartsEn1[0];
        $last_name_en1 = $namePartsEn1[1] ?? '';

        $user1 = User::create([
            'email' => 'ahmedelemam@gmail.com',
            'first_name_ar' => $first_name_ar1,
            'last_name_ar' => $last_name_ar1,
            'first_name_en' => $first_name_en1,
            'last_name_en' => $last_name_en1,
            'gender' => 'male',
            'password' => Hash::make('admin'),
            'status' => 'active',
            'profile_picture' => null,
            'last_login' => now(),
            'is_verified' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user1->assignRole('admin');

       
    }
}