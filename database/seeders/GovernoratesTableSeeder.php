<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
class GovernoratesTableSeeder extends Seeder
{ 
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates_file = database_path('data/governorates.json');
        $governorates_json = File::get($governorates_file);
        $governorates = json_decode($governorates_json,true);
        $governoratesWithTimestamps = array_map(function ($governorate) {
            return array_merge($governorate, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $governorates);
        Db::table('governorates')->insert($governorates);

    }
}
