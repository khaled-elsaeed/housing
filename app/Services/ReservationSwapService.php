<?php

namespace App\Services;

use App\Models\{Reservation, Room, User, ResidentRoomMovement};
use App\Events\ReservationRoomChanged;
use App\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\{DB, Auth};
use Exception;

class ReservationSwapService
{
    /**
     * Get user by national ID.
     *
     * @param string $nationalId
     * @return User
     * @throws Exception
     */
    public function getUserByNationalId(string $nationalId): User
    {
        try {
            $user = User::getUserByNationalId($nationalId);
            if (!$user) {
                throw new Exception('User not found');
            }
            return $user;
        } catch (Exception $e) {
            logError('Failed to find user by national ID', 'get_user_by_national_id', $e);
            throw $e;
        }
    }

    /**
     * Get the active reservation for a user.
     *
     * @param User $user
     * @return Reservation
     * @throws BusinessRuleException|Exception
     */
    public function getActiveReservation(User $user): Reservation
    {
        try {
            $reservation = $user->reservations()
                ->where('status', 'active')
                ->latest()
                ->first();

            if (!$reservation) {
                throw new BusinessRuleException('No active reservation found');
            }

            return $reservation;
        } catch (Exception $e) {
            logError('Failed to get active reservation', 'get_active_reservation', $e);
            throw $e;
        }
    }

    /**
     * Swap reservation locations for two reservations.
     *
     * @param int $reservationId1
     * @param int $reservationId2
     * @return array
     * @throws BusinessRuleException|Exception
     */
    public function swapReservationLocations(int $reservationId1, int $reservationId2): array
    {
        return DB::transaction(function () use ($reservationId1, $reservationId2) {
            try {
                $reservation1 = Reservation::with(['user.student', 'room.apartment.building'])
                    ->findOrFail($reservationId1);
                $reservation2 = Reservation::with(['user.student', 'room.apartment.building'])
                    ->findOrFail($reservationId2);

                if ($reservation1->user->student->gender !== $reservation2->user->student->gender) {
                    throw new BusinessRuleException('Users must be of the same gender to swap reservations');
                }

                $oldRoom1 = $reservation1->room_id;
                $oldRoom2 = $reservation2->room_id;

                $reservation1->room_id = $oldRoom2;
                $reservation2->room_id = $oldRoom1;

                $reservation1->save();
                $reservation2->save();

                $this->createResidentRoomMovement($reservation1, $oldRoom1, $oldRoom2, 'Room swap with another resident');
                $this->createResidentRoomMovement($reservation2, $oldRoom2, $oldRoom1, 'Room swap with another resident');

                userActivity($reservation1->user_id, 'room_swap', "Swapped room from {$oldRoom1} to {$oldRoom2}");
                userActivity($reservation2->user_id, 'room_swap', "Swapped room from {$oldRoom2} to {$oldRoom1}");

                event(new ReservationRoomChanged($reservation1->room));
                event(new ReservationRoomChanged($reservation2->room));

                return [
                    'reservation1' => $reservation1,
                    'reservation2' => $reservation2,
                ];
            } catch (Exception $e) {
                logError('Failed to swap reservation locations', 'swap_reservation_locations', $e);
                throw $e;
            }
        });
    }

    /**
     * Reallocate a reservation to a new room.
     *
     * @param int $reservationId
     * @param int $roomId
     * @return array
     * @throws BusinessRuleException|Exception
     */
    public function reallocateReservation(int $reservationId, int $roomId): array
    {
        return DB::transaction(function () use ($reservationId, $roomId) {
            try {
                $reservation = Reservation::with(['room.apartment.building', 'user.student'])
                    ->findOrFail($reservationId);
                $newRoom = Room::with('apartment.building')->findOrFail($roomId);

                if (Reservation::where('room_id', $newRoom->id)->exists()) {
                    throw new BusinessRuleException('Room is already assigned to another reservation');
                }

                if ($reservation->user->student->gender !== $newRoom->apartment->building->gender) {
                    throw new BusinessRuleException('Room gender does not match resident gender');
                }

                $oldRoomId = $reservation->room_id;

                if ($oldRoomId) {
                    $previousRoom = Room::find($oldRoomId);
                    if ($previousRoom) {
                        $previousRoom->current_occupancy -= 1;
                        $previousRoom->full_occupied = $previousRoom->current_occupancy >= $previousRoom->max_occupancy;
                        $previousRoom->save();
                    }
                }

                $newRoom->current_occupancy += 1;
                $newRoom->full_occupied = $newRoom->current_occupancy >= $newRoom->max_occupancy;
                $newRoom->save();

                $reservation->room_id = $newRoom->id;
                $reservation->save();

                $this->createResidentRoomMovement($reservation, $oldRoomId, $newRoom->id, 'Reallocated to a new room');
                userActivity($reservation->user_id, 'room_reallocation', "Reallocated from room {$oldRoomId} to {$newRoom->id}");

                event(new ReservationRoomChanged($newRoom));

                return [
                    'reservation' => $reservation,
                    'new_room_details' => [
                        'room_number' => $newRoom->number,
                        'apartment_number' => $newRoom->apartment->number,
                        'building_number' => $newRoom->apartment->building->number,
                    ],
                ];
            } catch (Exception $e) {
                logError('Failed to reallocate reservation', 'reallocate_reservation', $e);
                throw $e;
            }
        });
    }

    /**
     * Create a resident room movement record.
     *
     * @param Reservation $reservation
     * @param int|null $oldRoomId
     * @param int $newRoomId
     * @param string $reason
     * @throws Exception
     */
    private function createResidentRoomMovement(Reservation $reservation, ?int $oldRoomId, int $newRoomId, string $reason): void
    {
        try {
            ResidentRoomMovement::create([
                'user_id' => $reservation->user->id,
                'reservation_id' => $reservation->id,
                'old_room_id' => $oldRoomId,
                'new_room_id' => $newRoomId,
                'changed_by' => Auth::id(),
                'reason' => $reason,
            ]);
        } catch (Exception $e) {
            logError('Failed to create resident room movement', 'create_resident_room_movement', $e);
            throw $e;
        }
    }
}