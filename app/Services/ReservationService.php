<?php

namespace App\Services;

use App\Models\User;
use App\Models\Room;

class ReservationService
{
    /**
     * Retrieve applicants with a role of 'resident' and filter them based on application status and preferences.
     *
     * @return array Filtered applicants categorized by gender and preferences.
     */
    public function getApplicants()
    {
        $applicants = User::role('resident')
            ->with('student')
            ->where('application_status', 'pending')
            ->orderBy('weight', 'desc')
            ->get();

        return $this->filterApplicants($applicants);
    }

    /**
     * Filter applicants based on their preferences (e.g., staying in old room, double room).
     *
     * @param \Illuminate\Support\Collection $applicants Collection of applicants.
     * @return array Applicants categorized by gender and preferences.
     */
    public function filterApplicants($applicants)
    {
        return [
            'male' => [
                'stayingInOldRoom' => $applicants->where('stay_in_old_room', '1')->where('gender', 'male'),
                'inDoubleRoom' => $applicants->where('stay_in_double_room', '1')->where('gender', 'male'),
                'regularApplicants' => $applicants->where('stay_in_old_room', '0')->where('stay_in_double_room', '0')->where('gender', 'male'),
            ],
            'female' => [
                'stayingInOldRoom' => $applicants->where('stay_in_old_room', '1')->where('gender', 'female'),
                'inDoubleRoom' => $applicants->where('stay_in_double_room', '1')->where('gender', 'female'),
                'regularApplicants' => $applicants->where('stay_in_old_room', '0')->where('stay_in_double_room', '0')->where('gender', 'female'),
            ],
        ];
    }

    /**
     * Retrieve rooms based on availability, occupancy status, purpose, and gender preference.
     *
     * @return array Filtered rooms categorized by type and gender preference.
     */
    public function getRooms()
    {
        $maleRooms = Room::with('apartment.building')
            ->where('status', 'available')
            ->where('full_occupied', 0)
            ->where('purpose', 'accommodation')
            ->whereHas('apartment.building', function ($query) {
                $query->where('gender', 'male');
            })
            ->get();

        $femaleRooms = Room::with('apartment.building')
            ->where('status', 'available')
            ->where('full_occupied', 0)
            ->where('purpose', 'accommodation')
            ->whereHas('apartment.building', function ($query) {
                $query->where('gender', 'female');
            })
            ->get();

        return $this->filterRooms($maleRooms, $femaleRooms);
    }

    /**
     * Filter rooms based on their type (single/double) and gender preference (male/female).
     *
     * @param \Illuminate\Support\Collection $maleRooms Collection of male rooms.
     * @param \Illuminate\Support\Collection $femaleRooms Collection of female rooms.
     * @return array Rooms categorized by type and gender preference.
     */
    public function filterRooms($maleRooms, $femaleRooms)
    {
        $maleSingleRooms = $maleRooms->where('type', 'single');
        $maleDoubleRooms = $maleRooms->where('type', 'double');
        $femaleSingleRooms = $femaleRooms->where('type', 'single');
        $femaleDoubleRooms = $femaleRooms->where('type', 'double');

        return [
            'male' => [
                'singleRooms' => $maleSingleRooms,
                'doubleRooms' => $maleDoubleRooms,
            ],
            'female' => [
                'singleRooms' => $femaleSingleRooms,
                'doubleRooms' => $femaleDoubleRooms,
            ],
        ];
    }

    public function ReservationProcess(){
        $applicants = $this->getApplicants();
        $rooms = $this->getRooms();

        $applicantsStayInOldRoom = $this->ReserveOldRoom($applicants, $rooms);
    }

    private function ReserveOldRoom($applicants, $rooms)
    {
        $maleApplicantsStayInOldRoom = $applicants['male']['stayingInOldRoom'];
        $femaleApplicantsStayInOldRoom = $applicants['female']['stayingInOldRoom'];

        $maleSingleRooms = $rooms['male']['singleRooms'];
        $maleDoubleRooms = $rooms['male']['doubleRooms'];
        $femaleSingleRooms = $rooms['female']['singleRooms'];
        $femaleDoubleRooms = $rooms['female']['doubleRooms'];


        $remainingApplicants = [
            'male' => [],
            'female' => []
        ];

        // Process male applicants
        foreach ($maleApplicantsStayInOldRoom as $applicant) {
            $oldRoom = $applicant->oldReservation->room ?? null;
            
            if ($oldRoom && !$oldRoom->full_occupied) {
                if (($oldRoom->type === 'single' && $maleSingleRooms->contains($oldRoom)) ||
                    ($oldRoom->type === 'double' && $maleDoubleRooms->contains($oldRoom))) {

                        $this->createReservation($applicant, $oldRoom);
                } else {
                    $remainingApplicants['male'][] = $applicant;
                }
            } else {
                $remainingApplicants['male'][] = $applicant;
            }
        }

        // Process female applicants
        foreach ($femaleApplicantsStayInOldRoom as $applicant) {
            $oldRoom = $applicant->oldReservation->room ?? null;
            
            if ($oldRoom && !$oldRoom->full_occupied) {
                if (($oldRoom->type === 'single' && $femaleSingleRooms->contains($oldRoom)) ||
                    ($oldRoom->type === 'double' && $femaleDoubleRooms->contains($oldRoom))) {
                    $this->createReservation($applicant, $oldRoom);
                } else {
                    $remainingApplicants['female'][] = $applicant;
                }
            } else {
                $remainingApplicants['female'][] = $applicant;
            }
        }

        return $remainingApplicants;
    }

    private function createReservation($applicant, $room)
    {
        $reservation = new Reservation();
        $reservation->user_id = $applicant->id;
        $reservation->room_id = $room->id;
        $reservation->status = 'active';
        $reservation->academic_year = date('Y') . '/' . (date('Y') + 1);
        $reservation->save();

        // Update room occupancy
        $room->current_occupancy += 1;
        if ($room->current_occupancy >= $room->max_occupancy) {
            $room->full_occupied = true;
        }
        $room->save();
        $applicant->application_status = 'approved';
        $applicant->save();
    }

    
}
