<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StaffRolesSeeder extends Seeder
{



    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $roles = ['technician', 'housing_manager', 'housing_specialist', 'plumber', 'carpenter', 'electrician'];

    foreach ($roles as $role) {
        Role::firstOrCreate(['name' => $role]);
    }
}



}
