<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        DB::table('settings')->insert([
            ['key' => 'reservation_start', 'value' => '08:00'],
            ['key' => 'reservation_end', 'value' => '18:00'],
            ['key' => 'reservation_status', 'value' => 'open'],
            ['key' => 'allowed_students', 'value' => 'both'],
           
        ]);
    }
}
