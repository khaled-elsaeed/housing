<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        // Define file paths for male and female reservation files
        $files = [
            'females' => database_path('data/final_females_reservations_housing.csv'),
            'males' => database_path('data/final_males_reservations_housing.csv'),
        ];

        foreach ($files as $gender => $filePath) {
            $this->processReservationsFile($filePath, $gender);
        }

        Log::info('All reservations have been seeded.');
    }

    private function processReservationsFile($filePath, $gender)
    {
        Log::info("Starting to seed {$gender} reservations from file: {$filePath}");

        try {
            $spreadsheet = IOFactory::load($filePath);
            Log::info("Spreadsheet for {$gender} loaded successfully.");
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            Log::error("Failed to load spreadsheet for {$gender}: " . $e->getMessage());
            return;
        }

        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator(2) as $row) {
            $user_id = $sheet->getCell('B' . $row->getRowIndex())->getValue();
            $room_id = $sheet->getCell('C' . $row->getRowIndex())->getValue();

            if (empty($user_id) || empty($room_id)) {
                Log::warning("Missing user_id or room_id at row {$row->getRowIndex()} for {$gender}. Skipping.");
                continue;
            }

            $userExists = DB::table('users')->where('id', $user_id)->exists();
            $roomExists = DB::table('rooms')->where('id', $room_id)->exists();

            if (!$userExists || !$roomExists) {
                Log::warning("Invalid user_id ({$user_id}) or room_id ({$room_id}) for {$gender}. Skipping.");
                continue;
            }

            $existingReservation = DB::table('reservations')
                ->where('user_id', $user_id)
                ->where('status', '!=', 'canceled')
                ->exists();

            if ($existingReservation) {
                Log::warning("User ID {$user_id} already has a reservation for {$gender}. Skipping.");
                continue;
            }

            $currentYear = Carbon::now()->year;
            $term = 'first_term';

            $room = DB::table('rooms')->where('id', $room_id)->first();

            if ($room && $room->full_occupied !== 1) {
                DB::table('reservations')->insert([
                    'user_id' => $user_id,
                    'room_id' => $room_id,
                    'year' => $currentYear,
                    'term' => $term,
                    'status' => 'confirmed',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $newOccupancy = $room->current_occupancy + 1;
                DB::table('rooms')->where('id', $room_id)->update(['current_occupancy' => $newOccupancy]);

                if ($newOccupancy >= $room->max_occupancy) {
                    DB::table('rooms')->where('id', $room_id)->update(['full_occupied' => 1]);
                }

                DB::table('students')->where('user_id', $user_id)->update([
                    'application_status' => 'final_accepted',
                    'updated_at' => Carbon::now(),
                ]);

                Log::info("Reservation created for User ID {$user_id}, Room ID {$room_id} ({$gender}).");
            } else {
                Log::warning("Room ID {$room_id} is full for {$gender}. Skipping.");
            }
        }

        Log::info("Finished processing {$gender} reservations.");
    }
}
