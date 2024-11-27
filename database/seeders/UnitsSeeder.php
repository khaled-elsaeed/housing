<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Apartment;
use App\Models\Room;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Building numbers assigned to girls and boys
        $girlBuildingNumbers = [1, 2, 4, 5, 16, 17, 18, 19];
        $maleBuildingNumbers = [9, 10, 11, 12, 13, 14];

        // Create buildings for girls
        foreach ($girlBuildingNumbers as $buildingNumber) {
            $this->createBuildingWithApartmentsAndRooms($buildingNumber, 'female');
        }

        // Create buildings for males
        foreach ($maleBuildingNumbers as $buildingNumber) {
            $this->createBuildingWithApartmentsAndRooms($buildingNumber, 'male');
        }
    }

    /**
     * Helper function to create a building with apartments and rooms.
     */
    private function createBuildingWithApartmentsAndRooms(int $buildingNumber, string $gender): void
    {
        // Create the building
        $building = Building::create([
            'number' => $buildingNumber,
            'gender' => $gender,
            'status' => 'active',
            'description' => ucfirst($gender) . " dorm building",
            'max_apartments' => 24,
        ]);

        // Create apartments for the building
        for ($apartmentNumber = 1; $apartmentNumber <= $building->max_apartments; $apartmentNumber++) {
            $apartment = Apartment::create([
                'building_id' => $building->id,
                'number' => $apartmentNumber,
                'max_rooms' => 3,
                'occupancy_status' => 'empty',
                'status' => 'active',
                'description' => "Apartment $apartmentNumber in Building {$building->number}",
            ]);

            // Create rooms for each apartment
            for ($roomNumber = 1; $roomNumber <= $apartment->max_rooms; $roomNumber++) {
                // Default max occupancy is 1 (single room)
                $maxOccupancy = 1;

                // Check if it's Building 1, Apartments 5 to 18 (except apartment 17)
                if ($buildingNumber == 1 && $apartmentNumber >= 5 && $apartmentNumber <= 18 && $apartmentNumber != 17 && $roomNumber == 3) {
                    $maxOccupancy = 2; // Double room for room 3 in apartments 5-18 (excluding 17)
                }

                // Check if it's Building 12 and specific apartments (7, 8, 9, 10, 11, 12, 13, 15, 16, 17)
                if ($buildingNumber == 12 && in_array($apartmentNumber, [7, 8, 9, 10, 11, 12, 13, 15, 16, 17]) && $roomNumber == 3) {
                    $maxOccupancy = 2; // Double room for room 3 in specific apartments in Building 12
                }

                // Create the room with the determined max occupancy
                Room::create([
                    'apartment_id' => $apartment->id,
                    'number' => $roomNumber,
                    'max_occupancy' => $maxOccupancy,
                    'current_occupancy' => 0,
                    'status' => 'active',
                    'purpose' => 'accommodation', // Purpose set to accommodation
                    'type' => $maxOccupancy == 2 ? 'double' : 'single', // Set type to double if max_occupancy is 2
                    'description' => "Room $roomNumber in Apartment {$apartment->number}, Building {$building->number}",
                ]);
            }
        }
    }
}
