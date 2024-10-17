<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CitiesTableSeeder extends Seeder
{ 
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities_file = database_path('data\cities.json');
        $cities_json = File::get($cities_file);
        $cities = json_decode($cities_json,true);
        $citiesWithTimestamps = array_map(function ($city) {
            return array_merge($city, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $cities);
        Db::table('cities')->insert($citiesWithTimestamps);

    }
}
