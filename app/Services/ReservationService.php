<?php

namespace App\Services;

use App\Models\{User, Room, Reservation, Invoice, InvoiceDetail};
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Carbon;

class ReservationService
{
    /**
     * Get applicants with the "resident" role and "pending" application status.
     *
     * @return array
     */
    public function getApplicants(): array
    {
        $applicants = User::role("resident")
            ->with("student")
            ->where("application_status", "pending")
            ->orderBy("weight", "desc")
            ->get();

        return $this->filterApplicants($applicants);
    }

    /**
     * Filter applicants by gender and room preferences.
     *
     * @param $applicants
     * @return array
     */
    public function filterApplicants($applicants): array
    {
        $filterByGenderAndPreference = function ($gender, $applicants) {
            return [
                "stayingInOldRoom" => $applicants
                    ->where("stay_in_old_room", "1")
                    ->where("gender", $gender),
                "inDoubleRoom" => $applicants
                    ->where("stay_in_double_room", "1")
                    ->where("gender", $gender),
                "regularApplicants" => $applicants
                    ->where("stay_in_old_room", "0")
                    ->where("stay_in_double_room", "0")
                    ->where("gender", $gender),
            ];
        };

        return [
            "male" => $filterByGenderAndPreference("male", $applicants),
            "female" => $filterByGenderAndPreference("female", $applicants),
        ];
    }

    /**
     * Get available rooms by gender.
     *
     * @return array
     */
    public function getRooms(): array
    {
        $getRoomsByGender = function ($gender) {
            return Room::with("apartment.building")
                ->where("status", "available")
                ->where("full_occupied", 0)
                ->where("purpose", "accommodation")
                ->whereHas("apartment.building", function ($query) use ($gender) {
                    $query->where("gender", $gender);
                })
                ->get();
        };

        $maleRooms = $getRoomsByGender("male");
        $femaleRooms = $getRoomsByGender("female");

        return $this->filterRooms($maleRooms, $femaleRooms);
    }

    /**
     * Filter rooms by type (single or double).
     *
     * @param $maleRooms
     * @param $femaleRooms
     * @return array
     */
    public function filterRooms($maleRooms, $femaleRooms): array
    {
        $filterRoomsByType = function ($rooms) {
            return [
                "singleRooms" => $rooms->where("type", "single"),
                "doubleRooms" => $rooms->where("type", "double"),
            ];
        };

        return [
            "male" => $filterRoomsByType($maleRooms),
            "female" => $filterRoomsByType($femaleRooms),
        ];
    }

    /**
     * Process reservations for applicants staying in their old rooms.
     *
     * @return array
     */
    public function reservationProcess(): array
    {
        $applicants = $this->getApplicants();
        $rooms = $this->getRooms();

        return $this->reserveOldRoom($applicants, $rooms);
    }

    /**
     * Reserve old rooms for applicants.
     *
     * @param $applicants
     * @param $rooms
     * @return array
     */
    private function reserveOldRoom($applicants, $rooms): array
    {
        $remainingApplicants = ["male" => [], "female" => []];

        $processApplicants = function ($applicants, $rooms, $gender) use (&$remainingApplicants) {
            foreach ($applicants[$gender]["stayingInOldRoom"] as $applicant) {
                $oldRoom = $applicant->oldReservation->room ?? null;

                if ($oldRoom && !$oldRoom->full_occupied) {
                    $roomType = $oldRoom->type;
                    $roomCollection = $rooms[$gender]["{$roomType}Rooms"];

                    if ($roomCollection->contains($oldRoom)) {
                        $this->createReservation(
                            $applicant,
                            $oldRoom,
                            "long",
                            $this->getCurrentAcademicTermId()
                        );
                    } else {
                        $remainingApplicants[$gender][] = $applicant;
                    }
                } else {
                    $remainingApplicants[$gender][] = $applicant;
                }
            }
        };

        $processApplicants($applicants, $rooms, "male");
        $processApplicants($applicants, $rooms, "female");

        return $remainingApplicants;
    }

    /**
     * Create a new reservation.
     *
     * @param User $reservationRequester
     * @param Room|null $selectedRoom
     * @param string $reservationPeriodType
     * @param int|null $academicTermId
     * @param string|null $shortTermDuration
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string $status
     * @return Reservation|null
     * @throws \Exception
     */
    private function createReservation(
        User $reservationRequester,
        ?Room $selectedRoom,
        string $reservationPeriodType,
        ?int $academicTermId,
        ?string $shortTermDuration = null,
        ?string $startDate = null,
        ?string $endDate = null,
        string $status = "pending"
    ): ?Reservation {
        try {
            DB::beginTransaction();

            $newReservation = new Reservation();
            $newReservation->user_id = $reservationRequester->id;
            $newReservation->room_id = $selectedRoom?->id;
            if($this->getCurrentAcademicTermId() === $academicTermId){
                $newReservation->status = $status;
            }else{
                $newReservation->status = 'upcoming';

            }
            $newReservation->period_type = $reservationPeriodType;

            if ($reservationPeriodType === "short") {
                $newReservation->start_date = $startDate;
                $newReservation->end_date = $endDate;
            }

            $newReservation->academic_term_id = $academicTermId;
            $newReservation->save();

            if ($selectedRoom) {
                $selectedRoom->has_upcoming_reservation = true;
                $selectedRoom->upcoming_reservation_id = $newReservation->id;
                $selectedRoom->save();
            }

            $this->createInvoice($newReservation, $reservationPeriodType, $shortTermDuration);

            DB::commit();

            return $newReservation;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Reservation creation failed: " . $e->getMessage());
            throw new \Exception(trans('Failed to create reservation: ') . $e->getMessage());
        }
    }

    /**
     * Create an invoice for the reservation.
     *
     * @param Reservation $reservation
     * @param string $reservationPeriodType
     * @param string|null $shortTermDuration
     */
    private function createInvoice(
        Reservation $reservation,
        string $reservationPeriodType,
        ?string $shortTermDuration = null
    ): void {

        $invoice = Invoice::create([
            "reservation_id" => $reservation->id,
            "status" => "unpaid",
        ]);

        $roomType = $reservation->room->type;

        if ($reservationPeriodType === "long") {
            $this->addLongTermInvoiceDetails($invoice,$roomType);
        } else {
            $this->addShortTermInvoiceDetails($invoice, $shortTermDuration);
        }
    }

    /**
     * Add long-term invoice details.
     *
     * @param Invoice $invoice
     */
    private function addLongTermInvoiceDetails(Invoice $invoice, $roomType): void
{
    try {
        Log::info('Adding long-term invoice details.', [
            'invoice_id' => $invoice->id,
            'room_type' => $roomType,
        ]);

        // Determine fee amount based on room type
        $feeAmount = $roomType === 'single' ? 10000 : 8000;

        // Add the base fee to the invoice
        InvoiceDetail::create([
            "invoice_id" => $invoice->id,
            "category" => "fee",
            "amount" => $feeAmount,
        ]);

        Log::info('Base fee added to invoice.', [
            'invoice_id' => $invoice->id,
            'amount' => $feeAmount,
        ]);

        $user = $invoice->reservation->user;
        $academicTerm = $invoice->reservation->academicTerm;

        Log::info('Fetching user and academic term details.', [
            'user_id' => $user->id,
            'academic_term_id' => $academicTerm->id,
        ]);

        $lastReservation = $user->lastReservation($academicTerm->id - 1); 

        if (!$lastReservation) {

            InvoiceDetail::create([
                "invoice_id" => $invoice->id,
                "category" => "insurance",
                "amount" => 5000,
            ]);

            Log::info('Insurance fee added to invoice.', [
                'invoice_id' => $invoice->id,
                'amount' => 5000,
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Failed to add long-term invoice details.', [
            'invoice_id' => $invoice->id ?? null,
            'room_type' => $roomType,
            'error_message' => $e->getMessage(),
        ]);

        throw new \Exception(trans('Failed to add long-term invoice details: ') . $e->getMessage());
    }
}

    /**
     * Add short-term invoice details.
     *
     * @param Invoice $invoice
     * @param string $shortTermDuration
     */
    private function addShortTermInvoiceDetails(Invoice $invoice, string $shortTermDuration): void
    {
        $price = $this->calculateFeePrice($shortTermDuration);

        InvoiceDetail::create([
            "invoice_id" => $invoice->id,
            "category" => "fee",
            "amount" => $price,
        ]);
    }

    /**
     * Calculate the fee price based on the short-term duration.
     *
     * @param string $shortTermDuration
     * @return int
     */
    private function calculateFeePrice(string $shortTermDuration): int
    {
        return match ($shortTermDuration) {
            "day" => 300,
            "week" => 2000,
            "month" => 2500,
            default => 0,
        };
    }

  
    /**
     * Request a reservation.
     *
     * @param User $reservationRequester
     * @param string $reservationPeriodType
     * @param int|null $academicTermId
     * @param string|null $shortTermDuration
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function requestReservation(
        User $reservationRequester,
        string $reservationPeriodType = "long",
        ?int $academicTermId = null,
        ?string $shortTermDuration = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        try {

            // Check if the user already has an active or upcoming reservation
            if ($this->hasExistingReservation($reservationRequester, $academicTermId)) {
                return [
                    "success" => false,
                    "reason" => trans('You already have an active or upcoming reservation in this period.'),
                ];
            }

            // Check if the user already has pending  reservation requests
            if ($this->hasExistingReservationRequests($reservationRequester, $academicTermId)) {
                return [
                    "success" => false,
                    "reason" => trans('You already have a pending reservation request.'),
                ];
            }

            if ($reservationPeriodType === "long") {
                return $this->handleLongTermPeriodReservation($reservationRequester, $academicTermId);
            }

            if ($reservationPeriodType === "short") {
                return $this->handleShortTermPeriodReservation(
                    $reservationRequester,
                    $academicTermId,
                    $shortTermDuration,
                    $startDate,
                    $endDate
                );
            }

            return [
                "success" => false,
                "reason" => trans('Invalid reservation period type.'),
            ];
        } catch (\Exception $e) {
            Log::error("New reservation failed: " . $e->getMessage());
            return [
                "success" => false,
                "reason" => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle long-term reservation requests.
     *
     * @param User $reservationRequester
     * @param int|null $academicTermId
     * @return array
     */
    private function handleLongTermPeriodReservation(User $reservationRequester, ?int $academicTermId): array
    {
        $roomAvailabilityStatus = $this->checkLastReservedRoomAvailability($reservationRequester, $academicTermId);

        if (!$roomAvailabilityStatus["available"]) {
            $this->createReservationRequest(
                $reservationRequester,
                $academicTermId,
                $reservationRequester->gender,
                "long",
                null,
                null,
                null
            );

            return [
                "success" => true,
                "message" => trans('Your reservation request has been submitted for admin approval.'),
            ];
        }

        $createdReservation = $this->createReservation(
            $reservationRequester,
            Room::find($roomAvailabilityStatus['roomId']),
            "long",
            $academicTermId
        );

        return [
            "success" => true,
            "reservation" => $createdReservation,
        ];
    }

    /**
     * Handle short-term reservation requests.
     *
     * @param User $reservationRequester
     * @param int|null $academicTermId
     * @param string|null $shortTermDuration
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    private function handleShortTermPeriodReservation(
        User $reservationRequester,
        ?int $academicTermId,
        ?string $shortTermDuration,
        ?string $startDate,
        ?string $endDate
    ): array {
        $this->createReservationRequest(
            $reservationRequester,
            $academicTermId,
            $reservationRequester->gender,
            "short",
            $shortTermDuration,
            $startDate,
            $endDate
        );

        return [
            "success" => true,
            "message" => trans('Your short-term reservation request has been submitted for admin approval.'),
        ];
    }

    /**
     * Create a reservation request.
     *
     * @param User $user
     * @param int|null $academicTermId
     * @param string $gender
     * @param string $periodType
     * @param string|null $shortTermDuration
     * @param string|null $startDate
     * @param string|null $endDate
     * @throws \Exception
     */
    private function createReservationRequest(
        User $user,
        ?int $academicTermId,
        string $gender,
        string $periodType,
        ?string $shortTermDuration,
        ?string $startDate = null,
        ?string $endDate = null
    ): void {
        try {
            DB::table('reservation_requests')->insert([
                'user_id' => $user->id,
                'academic_term_id' => $academicTermId,
                'gender' => $gender,
                'period_type' => $periodType,
                'period_duration' => $shortTermDuration,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create reservation request: " . $e->getMessage());
            throw new \Exception(trans('Failed to create reservation request: ') . $e->getMessage());
        }
    }

    /**
     * Check the availability of the last reserved room.
     *
     * @param User $reservationRequester
     * @param int|null $academicTermId
     * @return array
     */
    private function checkLastReservedRoomAvailability(User $reservationRequester, ?int $academicTermId): array
    {
        // check if he was with us the term before 
        $lastReservedRoom = $reservationRequester->lastReservation($academicTermId)?->room;

        if (!$lastReservedRoom) {
            return [
                "available" => false,
                "reason" => trans('No previous room found for the user.'),
            ];
        }

        $roomAvailabilityStatus = $this->checkRoomAvailability($lastReservedRoom->id, $academicTermId);

        if ($roomAvailabilityStatus["available"]) {
            $roomAvailabilityStatus["roomId"] = $lastReservedRoom->id;
        }

        return $roomAvailabilityStatus;
    }

    /**
     * Check the availability of a room.
     *
     * @param int $targetRoomId
     * @param int|null $academicTermId
     * @return array
     */
    private function checkRoomAvailability(int $targetRoomId, ?int $academicTermId): array
    {
        try {

            $activeAcademicTermId = $this->getCurrentAcademicTermId();


            if($activeAcademicTermId === $academicTermId){

                $existingReservationConflict = Reservation::where("room_id", $targetRoomId)
                    ->whereIn("status", ["active", "pending"])
                    ->where("academic_term_id", $activeAcademicTermId)
                    ->exists();
            }
            else{
                $existingReservationConflict = Reservation::where('room_id',$targetRoomId)
                ->where("status",['upcoming'])
                ->where("academic_term_id", $activeAcademicTermId)
                ->exists();
            }
            

            if ($existingReservationConflict) {
                return [
                    "available" => false,
                    "reason" => trans('Room has active or upcoming reservations.'),
                ];
            }

            $roomToVerify = Room::findOrFail($targetRoomId);

            if ($roomToVerify->purpose !== "accommodation" || $roomToVerify->status !== "active") {
                return [
                    "available" => false,
                    "reason" => trans('Room is inactive or not for accommodation.'),
                ];
            }

            return [
                "available" => true,
                "reason" => trans('Room is available.'),
            ];
        } catch (\Exception $e) {
            Log::error("Room availability check failed: " . $e->getMessage());
            return [
                "available" => false,
                "reason" => trans('Error checking room availability: ') . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if the user already has an active or upcoming reservation.
     *
     * @param User $user
     * @param int|null $academicTermId
     * @return bool
     */
    public function hasExistingReservation(User $user, ?int $academicTermId = null): bool
{
    try {
        Log::info('Checking existing reservation for user.', [
            'user_id' => $user->id,
            'academic_term_id' => $academicTermId,
        ]);

        $existingReservation = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['active', 'upcoming'])
            ->when($academicTermId, function ($query, $academicTermId) {
                return $query->where('academic_term_id', $academicTermId);
            })
            ->exists();

        Log::info('Reservation check completed.', [
            'user_id' => $user->id,
            'academic_term_id' => $academicTermId,
            'exists' => $existingReservation,
        ]);

        return $existingReservation;
    } catch (\Exception $e) {
        Log::error('Failed to check existing reservations.', [
            'user_id' => $user->id,
            'academic_term_id' => $academicTermId,
            'error_message' => $e->getMessage(),
        ]);

        throw new \Exception(trans('Failed to check existing reservations: ') . $e->getMessage());
    }
}

    /**
     * Check if the user already has pending or active reservation requests.
     *
     * @param User $user
     * @param int|null $academicTermId
     * @return bool
     */
    public function hasExistingReservationRequests(User $user, ?int $academicTermId = null): bool
    {
        try {
            
            $existingRequests = DB::table('reservation_requests')
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending'])
                ->when($academicTermId, function ($query, $academicTermId) {
                    return $query->where('academic_term_id', $academicTermId);
                })
                ->exists();

            return $existingRequests;
        } catch (\Exception $e) {
            Log::error("Failed to check existing reservation requests: " . $e->getMessage());
            throw new \Exception(trans('Failed to check existing reservation requests: ') . $e->getMessage());
        }
    }


    /**
     * Get the current academic term ID.
     *
     * @return int|null
     */
    private function getCurrentAcademicTermId(): ?int
    {
        return DB::table('academic_terms')
            ->where('status', 'active')
            ->value('id');
    }
}