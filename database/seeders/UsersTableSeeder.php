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
        $fullNameAr1 = 'خالد زهران';
        $fullNameEn1 = 'Khaled Zahran';

        $namePartsAr1 = explode(' ', $fullNameAr1);
        $first_name_ar1 = $namePartsAr1[0];
        $last_name_ar1 = $namePartsAr1[1] ?? '';

        $namePartsEn1 = explode(' ', $fullNameEn1);
        $first_name_en1 = $namePartsEn1[0];
        $last_name_en1 = $namePartsEn1[1] ?? '';

        $user1 = User::create([
            'email' => 'khaled.elsaeidzahran@gmail.com',
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

        // Define second user details
        $fullNameAr2 = 'Mohamed Abdullah';
        $fullNameEn2 = 'محمد عبدالله';

        $namePartsAr2 = explode(' ', $fullNameAr2);
        $first_name_ar2 = $namePartsAr2[0];
        $last_name_ar2 = $namePartsAr2[1] ?? '';

        $namePartsEn2 = explode(' ', $fullNameEn2);
        $first_name_en2 = $namePartsEn2[0];
        $last_name_en2 = $namePartsEn2[1] ?? '';

        $user2 = User::create([
            'email' => 'm.abdullah@nmu.edu.eg',
            'first_name_ar' => $first_name_ar2,
            'last_name_ar' => $last_name_ar2,
            'first_name_en' => $first_name_en2,
            'last_name_en' => $last_name_en2,
            'gender' => 'male',
            'password' => Hash::make('admin'),
            'status' => 'active',
            'profile_picture' => null,
            'last_login' => now(),
            'is_verified' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user2->assignRole('admin'); 
    }
}