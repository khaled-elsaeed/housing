<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder; 
use Database\Seeders\UsersTableSeeder; 
use Database\Seeders\CountriesTableSeeder; 
use Database\Seeders\GovernoratesTableSeeder; 
use Database\Seeders\CitiesTableSeeder; 
use Database\Seeders\FacultiesTableSeeder; 
use Database\Seeders\ProgramsTableSeeder; 
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UniversityArchiveSeeder;
use Database\Seeders\UnitsSeeder;
use Database\Seeders\NotRegisterOldStudentSeeder;
use Database\Seeders\NotRegisterTransStudentSeeder;
use Database\Seeders\NotRegisterNewStudentSeeder;
// use Database\Seeders\ReservationSeeder;
// use Database\Seeders\InvoiceAndPaymentsSeeder;



use Database\Seeders\UniversityArchivePhpspreadsheetSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(GovernoratesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(FacultiesTableSeeder::class);
        $this->call(ProgramsTableSeeder::class);
        $this->call(UnitsSeeder::class);

        $this->call(UniversityArchivePhpspreadsheetSeeder::class);
        $this->call(NotRegisterTransStudentSeeder::class);
        $this->call(NotRegisterNewStudentSeeder::class);
        $this->call(NotRegisterOldStudentSeeder::class);
        // $this->call(ReservationSeeder::class);
        // $this->call(InvoiceAndPaymentsSeeder::class);



    }
}
