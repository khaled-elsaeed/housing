<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\BusinessRuleException;
use Exception;
use App\Events\ReservationRoomChanged;
use App\Models\ResidentRoomMovement;


class ReservationSwapService
{
    /**
     * Get user by national ID.
     *
     * @param string $nationalId
     * @return User
     * @throws Exception
     */
    public function getUserByNationalId($nationalId)
    {
        try {
            $user = User::getUserByNationalId($nationalId);

            if (!$user) {
                throw new Exception('User not found');
            }

            return $user;
        } catch (Exception $e) {
            Log::error('Failed to find user by national ID', [
                'error' => $e->getMessage(),
                'action' => 'get_user_by_national_id',
                'admin_id' => auth()->id(),
            ]);
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
    public function getActiveReservation($user)
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
            Log::error('Failed to get active reservation', [
                'error' => $e->getMessage(),
                'action' => 'get_active_reservation',
                'admin_id' => auth()->id(),
            ]);
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
    public function swapReservationLocations($reservationId1, $reservationId2)
    {
        return DB::transaction(function () use ($reservationId1, $reservationId2) {
            try {
                $reservation1 = Reservation::with(['user', 'room.apartment.building'])->find($reservationId1);
                $reservation2 = Reservation::with(['user', 'room.apartment.building'])->find($reservationId2);

                if (!$reservation1 || !$reservation2) {
                    throw new Exception('Reservations not found');
                }

                if ($reservation1->user->gender !== $reservation2->user->gender) {
                    throw new BusinessRuleException('Users must be of the same gender to swap reservations');
                }

                // Store previous room assignments
                $oldRoom1 = $reservation1->room_id;
                $oldRoom2 = $reservation2->room_id;

                // Swap room IDs
                $reservation1->room_id = $oldRoom2;
                $reservation2->room_id = $oldRoom1;

                $reservation1->save();
                $reservation2->save();

                $this->createResidentRoomMovement($reservation1, $oldRoom1, $oldRoom2, 'Room swap with another resident');
                $this->createResidentRoomMovement($reservation2, $oldRoom2, $oldRoom1, 'Room swap with another resident');

                // Dispatch events
                event(new ReservationRoomChanged($reservation1->room));
                event(new ReservationRoomChanged($reservation2->room));

                return [
                    'reservation1' => $reservation1,
                    'reservation2' => $reservation2,
                ];
            } catch (Exception $e) {
                Log::error('Failed to swap reservation locations', [
                    'error' => $e->getMessage(),
                    'action' => 'swap_reservation_locations',
                    'admin_id' => auth()->id(),
                ]);
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
    public function reallocateReservation($reservationId, $roomId)
    {
        return DB::transaction(function () use ($reservationId, $roomId) {
            try {
                $reservation = Reservation::with(['room.apartment.building', 'user'])->findOrFail($reservationId);
                $newRoom = Room::with('apartment.building')->findOrFail($roomId);

                if (Reservation::where('room_id', $newRoom->id)->exists()) {
                    throw new BusinessRuleException('Room is already assigned to another reservation');
                }

                if ($reservation->user->gender !== $newRoom->apartment->building->gender) {
                    throw new BusinessRuleException('Room gender is different than resident gender');
                }

                // Store old room ID
                $oldRoomId = $reservation->room_id;

                // Update previous room occupancy
                if ($oldRoomId) {
                    $previousRoom = Room::find($oldRoomId);
                    if ($previousRoom) {
                        $previousRoom->current_occupancy -= 1;
                        if ($previousRoom->current_occupancy != $previousRoom->max_occupancy) {
                            $previousRoom->full_occupied = 0;
                        }
                        $previousRoom->save();
                    }
                }

                // Update new room occupancy
                $newRoom->current_occupancy += 1;
                if ($newRoom->current_occupancy == $newRoom->max_occupancy) {
                    $newRoom->full_occupied = 1;
                }
                
                $newRoom->save();

                // Assign new room to reservation
                $reservation->room_id = $newRoom->id;
                $reservation->save();

                // Fix: Use oldRoomId instead of reservation->room_id which has already been updated
                $this->createResidentRoomMovement($reservation, $oldRoomId, $newRoom->id, 'Reallocated to a new room');

                // Dispatch event
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
                Log::error('Failed to reallocate reservation', [
                    'error' => $e->getMessage(),
                    'action' => 'reallocate_reservation',
                    'admin_id' => auth()->id(),
                ]);
                throw $e;
            }
        });
    }

    private function createResidentRoomMovement($reservation, $oldRoomId, $newRoomId, $reason){
        ResidentRoomMovement::create([
            'user_id' => $reservation->user->id,
            'reservation_id' => $reservation->id,
            'old_room_id' => $oldRoomId,
            'new_room_id' => $newRoomId,
            'changed_by' => auth()->id(),
            'reason' => $reason,
        ]);
    }
}