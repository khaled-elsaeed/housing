<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries_file = database_path('data\countries.json');
        $countries_json = File::get($countries_file);
        $countries = json_decode($countries_json,true);
        $countriesWithTimestamps = array_map(function ($country) {
            return array_merge($country, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $countries);
        Db::table('countries')->insert($countriesWithTimestamps);

    }
}
