<?php

namespace App\Services;

use App\Models\User;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * Retrieve applicants with a role of 'resident' and filter them based on application status and preferences.
     *
     * @return array Filtered applicants categorized by gender and preferences.
     */
    public function getApplicants()
    {
        $applicants = User::role("resident")
            ->with("student")
            ->where("application_status", "pending")
            ->orderBy("weight", "desc")
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
            "male" => [
                "stayingInOldRoom" => $applicants->where("stay_in_old_room", "1")->where("gender", "male"),
                "inDoubleRoom" => $applicants->where("stay_in_double_room", "1")->where("gender", "male"),
                "regularApplicants" => $applicants
                    ->where("stay_in_old_room", "0")
                    ->where("stay_in_double_room", "0")
                    ->where("gender", "male"),
            ],
            "female" => [
                "stayingInOldRoom" => $applicants->where("stay_in_old_room", "1")->where("gender", "female"),
                "inDoubleRoom" => $applicants->where("stay_in_double_room", "1")->where("gender", "female"),
                "regularApplicants" => $applicants
                    ->where("stay_in_old_room", "0")
                    ->where("stay_in_double_room", "0")
                    ->where("gender", "female"),
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
        $maleRooms = Room::with("apartment.building")
            ->where("status", "available")
            ->where("full_occupied", 0)
            ->where("purpose", "accommodation")
            ->whereHas("apartment.building", function ($query) {
                $query->where("gender", "male");
            })
            ->get();

        $femaleRooms = Room::with("apartment.building")
            ->where("status", "available")
            ->where("full_occupied", 0)
            ->where("purpose", "accommodation")
            ->whereHas("apartment.building", function ($query) {
                $query->where("gender", "female");
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
        $maleSingleRooms = $maleRooms->where("type", "single");
        $maleDoubleRooms = $maleRooms->where("type", "double");
        $femaleSingleRooms = $femaleRooms->where("type", "single");
        $femaleDoubleRooms = $femaleRooms->where("type", "double");

        return [
            "male" => [
                "singleRooms" => $maleSingleRooms,
                "doubleRooms" => $maleDoubleRooms,
            ],
            "female" => [
                "singleRooms" => $femaleSingleRooms,
                "doubleRooms" => $femaleDoubleRooms,
            ],
        ];
    }

    public function ReservationProcess()
    {
        $applicants = $this->getApplicants();
        $rooms = $this->getRooms();

        $applicantsStayInOldRoom = $this->ReserveOldRoom($applicants, $rooms);
    }

    private function ReserveOldRoom($applicants, $rooms)
    {
        $maleApplicantsStayInOldRoom = $applicants["male"]["stayingInOldRoom"];
        $femaleApplicantsStayInOldRoom = $applicants["female"]["stayingInOldRoom"];

        $maleSingleRooms = $rooms["male"]["singleRooms"];
        $maleDoubleRooms = $rooms["male"]["doubleRooms"];
        $femaleSingleRooms = $rooms["female"]["singleRooms"];
        $femaleDoubleRooms = $rooms["female"]["doubleRooms"];

        $remainingApplicants = [
            "male" => [],
            "female" => [],
        ];

        // Process male applicants
        foreach ($maleApplicantsStayInOldRoom as $applicant) {
            $oldRoom = $applicant->oldReservation->room ?? null;

            if ($oldRoom && !$oldRoom->full_occupied) {
                if (($oldRoom->type === "single" && $maleSingleRooms->contains($oldRoom)) || ($oldRoom->type === "double" && $maleDoubleRooms->contains($oldRoom))) {
                    $this->createReservation($applicant, $oldRoom, "long_term", $this->getCurrentAcademicTermId());
                } else {
                    $remainingApplicants["male"][] = $applicant;
                }
            } else {
                $remainingApplicants["male"][] = $applicant;
            }
        }

        // Process female applicants
        foreach ($femaleApplicantsStayInOldRoom as $applicant) {
            $oldRoom = $applicant->oldReservation->room ?? null;

            if ($oldRoom && !$oldRoom->full_occupied) {
                if (($oldRoom->type === "single" && $femaleSingleRooms->contains($oldRoom)) || ($oldRoom->type === "double" && $femaleDoubleRooms->contains($oldRoom))) {
                    $this->createReservation($applicant, $oldRoom, "long_term", $this->getCurrentAcademicTermId());
                } else {
                    $remainingApplicants["female"][] = $applicant;
                }
            } else {
                $remainingApplicants["female"][] = $applicant;
            }
        }

        return $remainingApplicants;
    }

    /**
     * Create a new room reservation
     *
     * @param User $reservationApplicant
     * @param Room $selectedRoom
     * @param string $reservationPeriodType
     * @param int $academicTermId
     * @param string $startDate
     * @param string $endDate
     * @param string $status
     * @return Reservation|null
     * @throws \Exception
     */
    private function createReservation(User $reservationApplicant, Room $selectedRoom,string $reservationPeriodType, int $academicTermId, ?string $startDate = null, ?string $endDate = null, string $status = "pending")
    {
        try {

            DB::beginTransaction();

            $newReservation = new Reservation();
            $newReservation->user_id = $reservationApplicant->id;
            $newReservation->room_id = $selectedRoom->id;
            $newReservation->status = $status;
            $newReservation->period_type = $reservationPeriodType;

            // Handle period-specific data
            if ($reservationPeriodType === "short_term") {
                // Manual or calculated dates
                $newReservation->start_date = $startDate;
                $newReservation->end_date = $endDate;
            }

            $newReservation->academic_term_id = $academicTermId;

            $newReservation->save();

            // Update room occupancy
            $selectedRoom->has_upcoming_reservation = true;
            $selectedRoom->upcoming_reservation_id = $newReservation->id;
            $selectedRoom->save();

            $invocie = $this->createInvoice($newReservation);
            $invocieDetails = $this->createInvoiceDetails($invocie);

            DB::commit();

            return $newReservation;
        } catch (\Exception $reservationError) {
            DB::rollBack();
            \Log::error("Reservation creation failed: " . $reservationError->getMessage());
            throw new \Exception("Failed to create reservation: " . $reservationError->getMessage());
        }
    }

    /**
     * Create a new reservation for a user
     *
     * @param User $reservationRequester
     * @param string $reservationPeriodType
     * @param string|null $academicTermId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function requestReservation(User $reservationRequester,string $reservationPeriodType = "long_term",?int $academicTermId = null,?string $startDate = null, ?string $endDate = null )
    {
        try {
            $previouslyReservedRoom = $reservationRequester->lastReservation()->room;

            $roomAvailabilityStatus = $this->checkRoomAvailability($previouslyReservedRoom->id);

            if ($roomAvailabilityStatus["available"]) {
                if($reservationPeriodType === "long_term"){
                    $createdReservation = $this->createReservation($reservationRequester, $previouslyReservedRoom,$reservationPeriodType,$academicTermId);
                }else if($reservationPeriodType === "short_term"){
                    $createdReservation = $this->createReservation($reservationRequester, $previouslyReservedRoom,$reservationPeriodType,$academicTermId, $startDate, $endDate);

                }
                return [
                    "success" => true,
                    "reservation" => $createdReservation,
                ];
            } else {
                return [
                    "success" => false,
                    "reason" => $roomAvailabilityStatus["reason"],
                ];
            }
        } catch (\Exception $reservationProcessingError) {
            \Log::error("New reservation failed: " . $reservationProcessingError->getMessage());
            return [
                "success" => false,
                "reason" => $reservationProcessingError->getMessage(),
            ];
        }
    }

    /**
     * Check room availability
     *
     * @param int $targetRoomId
     * @return array
     */
    private function checkRoomAvailability(int $targetRoomId): array
    {
        try {
            // Check for active or upcoming reservations
            $existingReservationConflict = Reservation::where("room_id", $targetRoomId)
                ->whereIn("status", ["active", "upcoming"])
                ->exists();

            if ($existingReservationConflict) {
                return [
                    "available" => false,
                    "reason" => "Room has active or upcoming reservations.",
                ];
            }

            // Check room status and purpose
            $roomToVerify = Room::findOrFail($targetRoomId);

            if ($roomToVerify->purpose !== "accommodation" || $roomToVerify->status !== "active") {
                return [
                    "available" => false,
                    "reason" => "Room is inactive or not for accommodation.",
                ];
            }

            return [
                "available" => true,
                "reason" => "Room is available",
            ];
        } catch (\Exception $availabilityCheckError) {
            \Log::error("Room availability check failed: " . $availabilityCheckError->getMessage());
            return [
                "available" => false,
                "reason" => "Error checking room availability: " . $availabilityCheckError->getMessage(),
            ];
        }
    }

      /**
     * Create a new invoice for a reservation
     *
     * @param Reservation $reservation Associated reservation
     * @param string $paymentMethod Payment method used
     * @param mixed $paymentImage Uploaded payment receipt
     * @return Invoice Created invoice instance
     */
    private function createInvoice($reservation)
    {
        return Invoice::create([
            "reservation_id" => $reservation->id,
            "status" => "unpaid",
        ]);
    }

    /**
     * Create itemized invoice details
     *
     * @param Invoice $invoice Invoice to add details to
     */
    private function createInvoiceDetails($invoice)
    {
        // Add accommodation fee
        InvoiceDetail::create([
            "invoice_id" => $invoice->id,
            "category" => "fee",
            "amount" => 10000,
        ]);

        // Add student insurance
        InvoiceDetail::create([
            "invoice_id" => $invoice->id,
            "category" => "insurance",
            "amount" => 5000,
        ]);
    }

    // Add this helper method
    private function getCurrentAcademicTermId()
    {
        // Implement logic to get current academic term ID
        return 1; // Temporary return value - implement actual logic
    }
}
