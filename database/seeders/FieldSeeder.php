<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Field;
class FieldSeeder extends Seeder
{
    public function run(): void
    {
        // Sample data for fields
        $fields = [
            [
                'id' => 1,
                'name' => 'GPA',
                'type' => 'numeric',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Gender',
                'type' => 'categorical',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Academic Level 2023/2024',
                'type' => 'categorical',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Sibling in University',
                'type' => 'categorical',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Previous University Accommodation',
                'type' => 'categorical',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Family Resides Outside Egypt',
                'type' => 'categorical',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'High School Grade',
                'type' => 'numeric',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Governorate',
                'type' => 'categorical',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data into fields table
        foreach ($fields as $field) {
            Field::create($field);
        }
    }
}
