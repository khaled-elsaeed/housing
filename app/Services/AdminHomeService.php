<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Room;
use Carbon\Carbon;
use App\Models\Building;

class AdminHomeService
{
    public function getHomeData()
    {
        // Retrieve all students and calculate gender-specific counts
        $students = Student::select('gender', 'created_at')->get();
        
        // Filter male and female students
        $maleStudents = $this->filterStudentsByGender($students, 'male');
        $femaleStudents = $this->filterStudentsByGender($students, 'female');

        // Calculate total student counts
        $totalStudents = $students->count();
        $totalMaleStudents = $maleStudents->count();
        $totalFemaleStudents = $femaleStudents->count();

        // Retrieve all rooms and calculate occupancy rate
        $rooms = Room::select('id', 'status', 'purpose', 'updated_at','created_at')->get();
        $occupancyRate = $this->calculateOccupancyRate($rooms);
        $lastUpdatedRoom = $rooms->max('updated_at');

        // Retrieve last created students by gender
        $lastCreatedStudent = $students->max('created_at');
        $lastCreatedMaleStudent = $this->getLastCreatedStudentByGender($students, 'male');
        $lastCreatedFemaleStudent = $this->getLastCreatedStudentByGender($students, 'female');

        // Get building data
        $buildingsWithRoomStats = $this->getBuildingsWithRoomStats();

        return [
            'total_students' => $totalStudents,
            'male_students' => $totalMaleStudents,
            'female_students' => $totalFemaleStudents,
            'occupancy_rate' => round($occupancyRate, 2),
            'last_create_student' => $this->formatLastUpdated($lastCreatedStudent),
            'last_updated_room' => $this->formatLastUpdated($lastUpdatedRoom),
            'last_created_male_student' => $this->formatLastUpdated($lastCreatedMaleStudent ? $lastCreatedMaleStudent->created_at : null),
            'last_created_female_student' => $this->formatLastUpdated($lastCreatedFemaleStudent ? $lastCreatedFemaleStudent->created_at : null),
            'buildings' => $buildingsWithRoomStats,
        ];

    }

    private function getBuildingsWithRoomStats()
    {
        
        $buildings = Building::with(['apartments.rooms'])->get();
    
        return $buildings->map(function ($building) {
            
            $rooms = $building->apartments->flatMap->rooms->where('purpose', 'accommodation');
    
            
            $totalCount = $rooms->count();
            $occupiedCount = $rooms->sum('current_occupancy'); 
            $emptyCount = $rooms->sum('max_occupancy') - $occupiedCount; 
    
            return [
                'name' => $building->number, 
                'occupied' => $occupiedCount,
                'total' => $totalCount,
                'empty' => $emptyCount,
            ];
        });
    }
    


    // Filter students by gender
    private function filterStudentsByGender($students, $gender)
    {
        return $students->filter(function ($student) use ($gender) {
            return $student->gender === $gender;
        });
    }

    // Calculate occupancy rate of rooms
    private function calculateOccupancyRate($rooms)
    {
        $totalRooms = $rooms->count();

        $occupiedRooms = $rooms->filter(function ($room) {
            return $room->status === 'active' 
                && $room->purpose === 'accommodation' 
                && $room->reservations()->where('status', 'active')->count() > 0;
        })->count();

        return $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;
    }

    // Retrieve the last created student of a specific gender
    private function getLastCreatedStudentByGender($students, $gender)
    {
        return $students->filter(function ($student) use ($gender) {
            return $student->gender === $gender;
        })->sortByDesc('created_at')->first();
    }

    // Format the last updated timestamp for display
    private function formatLastUpdated($lastUpdated)
    {
        if (!$lastUpdated) {
            return 'Never';
        }

        $lastUpdated = Carbon::parse($lastUpdated);
        $diffInMinutes = $lastUpdated->diffInMinutes(now());

        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' minutes ago';
        }

        $diffInHours = (int)$lastUpdated->diffInHours(now());

        if ($diffInHours < 24) {
            $minutes = $diffInMinutes % 60;
            return $diffInHours . ' hours' . ($minutes > 0 ? ' and ' . $minutes . ' minutes ago' : ' ago');
        }

        $diffInDays = (int)$lastUpdated->diffInDays(now());
        return $diffInDays . ' days ago';
    }
}
