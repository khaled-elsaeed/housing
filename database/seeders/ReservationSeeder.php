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
        $filePath = database_path('data' . DIRECTORY_SEPARATOR . 'male_reservations.csv');
        
        Log::info("Starting to seed reservations from file: {$filePath}");
        try {
            $spreadsheet = IOFactory::load($filePath);
            Log::info("Spreadsheet loaded successfully.");
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            Log::error("Failed to load spreadsheet: " . $e->getMessage());
            return;
        }

        // Get the first sheet (you can change it if needed)
        $sheet = $spreadsheet->getActiveSheet();
        
        // Iterate over the rows in the sheet (starting from row 2 to skip headers)
        foreach ($sheet->getRowIterator(2) as $row) { // Start from row 2 (skipping header)
            
            // Log row processing
            Log::info("Processing row: {$row->getRowIndex()}");

            // Get user_id and room_id from the row
            $user_id = $sheet->getCell('B' . $row->getRowIndex())->getValue();  // Assuming user_id is in column A
            $room_id = $sheet->getCell('C' . $row->getRowIndex())->getValue();  // Assuming room_id is in column B

            // Log the user_id and room_id being processed
            Log::info("User ID: {$user_id}, Room ID: {$room_id}");

            // Check if the user and room exist
            $userExists = DB::table('users')->where('id', $user_id)->exists();
            $roomExists = DB::table('rooms')->where('id', $room_id)->exists();

            if ($userExists && $roomExists) {
                // Check if the user already has a reservation
                $existingReservation = DB::table('reservations')
                    ->where('user_id', $user_id)
                    ->where('status', '!=', 'canceled') // Exclude canceled reservations
                    ->exists();  // Checks if any reservation already exists for the user

                if ($existingReservation) {
                    Log::warning("User ID {$user_id} already has a reservation. Skipping.");
                    continue; // Skip this iteration if the user already has a reservation
                }

                // Get the current year and set term to 'first_term'
                $currentYear = Carbon::now()->year;
                $term = 'first_term';

                // Check the current occupancy of the room
                $room = DB::table('rooms')->where('id', $room_id)->first();

                if ($room) {
                    // Log the room details
                    Log::info("Room details - Current occupancy: {$room->current_occupancy}, Max occupancy: {$room->max_occupancy}");

                    // If room is not full (current occupancy < max occupancy), proceed with reservation
                    if ($room->full_occupied !== 1) {
                        // Insert the reservation data into the reservations table
                        DB::table('reservations')->insert([
                            'user_id' => $user_id,
                            'room_id' => $room_id,
                            'year' => $currentYear,  
                            'term' => $term,         
                            'status' => 'confirmed',    
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);

                        // Log reservation creation
                        Log::info("Reservation created: User ID {$user_id}, Room ID {$room_id}");

                        // Increment the current occupancy of the room
                        $newOccupancy = $room->current_occupancy + 1;

                        // Update the current occupancy
                        DB::table('rooms')->where('id', $room_id)->update([
                            'current_occupancy' => $newOccupancy,
                        ]);

                        // Log updated room occupancy
                        Log::info("Updated room occupancy: Room ID {$room_id}, New occupancy: {$newOccupancy}");

                        // Check if the room is full and update the full_occupied flag
                        if ($newOccupancy >= $room->max_occupancy) {
                            DB::table('rooms')->where('id', $room_id)->update([
                                'full_occupied' => 1,  // Mark room as full
                            ]);

                            // Log when room is marked as full
                            Log::info("Room ID {$room_id} marked as full.");
                        }

                        // Update the student's application status to "final accepted"
                        DB::table('students')->where('user_id', $user_id)->update([
                            'application_status' => 'final_accepted',
                            'updated_at' => Carbon::now(),
                        ]);

                        // Log application status update
                        Log::info("Application status updated to 'final accepted' for User ID {$user_id}");
                    } else {
                        // Log if the room is full and skip reservation
                        Log::warning("Room ID {$room_id} is full. Skipping reservation for User ID {$user_id}.");
                    }
                }
            } else {
                // Log if either the user or room doesn't exist
                Log::warning("User ID {$user_id} or Room ID {$room_id} does not exist. Skipping.");
            }
        }

        // Log the end of the seeding process
        Log::info('All reservations have been seeded.');
    }
}
