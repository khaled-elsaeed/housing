<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'email' => 'khaled@gmail.com', 
            'username_ar' => 'خالد زهران', 
            'username_en' => 'Khaled Zahran', 
            'password' => bcrypt('password'), 
            'is_active' => true, 
            'profile_picture' => null, 
            'last_login' => now(), 
            'is_verified' => true, 
            'created_at' => now(), 
            'updated_at' => now(), 
        ]);
    }
}
