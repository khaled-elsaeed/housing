<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
         // Create roles and assign existing permissions
         $adminRole = Role::create(['name' => 'admin']);
         $residentRole = Role::create(['name' => 'resident']);
 
      
    }
}
