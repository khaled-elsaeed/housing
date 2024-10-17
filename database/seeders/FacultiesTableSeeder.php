<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class FacultiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties_file = database_path('data\faculties.json');
        $faculties_json = File::get($faculties_file);
        $faculties = json_decode($faculties_json,true);
        $facultiesWithTimestamps = array_map(function ($faculty) {
            return array_merge($faculty, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $faculties);
        Db::table('faculties')->insert($facultiesWithTimestamps);

    }
}
