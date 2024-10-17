<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ProgramsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs_file = database_path('data\programs.json');
        $programs_json = File::get($programs_file);
        $programs = json_decode($programs_json,true);
        $programsWithTimestamps = array_map(function ($program) {
            return array_merge($program, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $programs);
        Db::table('programs')->insert($programsWithTimestamps);

    }
}
