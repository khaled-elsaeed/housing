<?php

namespace App\Services;

use App\Models\{User, Room, Reservation, Invoice, InvoiceDetail, ReservationRequest};
use App\Events\{ReservationCreated, ReservationRequested};
use Illuminate\Support\Facades\{DB, Cache};
use App\Exceptions\BusinessRuleException;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use Exception;

class ReservationService
{
    /**
     * Get pending reservation requests filtered by gender and preferences.
     *
     * @return Collection
     */
    public function getReservationRequests(): Collection
    {
        try {
            return ReservationRequest::with(['user.student.faculty:id,name'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->select(['id', 'user_id', 'stay_in_last_old_room', 'share_with_sibling', 'old_room_id', 'sibling_id', 'created_at'])
                ->get();
        } catch (Exception $e) {
            logError('Failed to get reservation requests', 'get_reservation_requests', $e);
            return collect();
        }
    }

    /**
     * Get available rooms filtered by gender and type.
     *
     * @return array
     */
    private function getAvailableRooms(): array
    {
        try {
            $rooms = Room::select('rooms.*')
                ->join('apartments', 'rooms.apartment_id', '=', 'apartments.id')
                ->join('buildings', 'apartments.building_id', '=', 'buildings.id')
                ->whereNotNull('buildings.gender')
                ->whereIn('rooms.type', ['single', 'double'])
                ->get()
                ->groupBy([
                    fn($room) => $room->apartment->building->gender,
                    'type'
                ]);

            return [
                'male' => [
                    'single' => $rooms['male']['single'] ?? collect(),
                    'double' => $rooms['male']['double'] ?? collect(),
                ],
                'female' => [
                    'single' => $rooms['female']['single'] ?? collect(),
                    'double' => $rooms['female']['double'] ?? collect(),
                ],
            ];
        } catch (Exception $e) {
            logError('Failed to get available rooms', 'get_available_rooms', $e);
            return [
                'male' => ['single' => collect(), 'double' => collect()],
                'female' => ['single' => collect(), 'double' => collect()],
            ];
        }
    }

    /**
     * Automate the reservation process with background processing support.
     *
     * @return void
     * @throws Exception
     */
    public function automateReservationProcess(): void
    {
        try {
            $facultyRankings = [
                'Medicine' => 1,
                'Dentistry' => 2,
                'Pharmacy' => 3,
                'Applied Health Sciences Technology' => 4,
                'Nursing' => 5,
                'Engineering' => 6,
                'Computer Science & Engineering' => 7,
                'Textile Science Engineering' => 8,
                'Business' => 9,
                'Law' => 10,
                'Science' => 11,
                'Social & Human Sciences' => 12,
                'Mass Media & Communication' => 13,
            ];

            $reservations = ReservationRequest::where('status', 'pending')
                ->with(['user.student.faculty'])
                ->orderBy('created_at', 'desc')
                ->get();

            $sortedReservations = $reservations->sortBy(fn($request) =>
                $facultyRankings[$request->user->student->faculty->name] ?? 99
            );

            $sortedReservations->chunk(500)->each(function ($reservationRequests) {
                DB::transaction(function () use ($reservationRequests) {
                    $filteredRequests = $this->filterRequests($reservationRequests);
                    $availableRooms = $this->getAvailableRooms();

                    foreach (['male', 'female'] as $gender) {
                        $this->processGenderRequests($gender, $filteredRequests[$gender], $availableRooms[$gender]);
                    }
                });
            });
        } catch (Exception $e) {
            logError('Error processing reservation requests', 'automate_reservation_process', $e);
            throw $e;
        }
    }

    /**
     * Filter reservation requests by gender and room preferences.
     *
     * @param Collection $reservationRequests
     * @return array
     */
    private function filterRequests(Collection $reservationRequests): array
    {
        $filtered = [
            'male' => ['stayInOldLastRoom' => [], 'stayWithSibling' => [], 'regular' => []],
            'female' => ['stayInOldLastRoom' => [], 'stayWithSibling' => [], 'regular' => []],
        ];

        foreach ($reservationRequests as $request) {
            $gender = $request->user?->student?->gender;
            if (!$gender) {
                continue;
            }

            if ($request->stay_in_last_old_room) {
                $filtered[$gender]['stayInOldLastRoom'][] = $request;
            } elseif ($request->share_with_sibling) {
                $filtered[$gender]['stayWithSibling'][] = $request;
            } else {
                $filtered[$gender]['regular'][] = $request;
            }
        }

        return $filtered;
    }

    /**
     * Process requests for a specific gender.
     *
     * @param string $gender
     * @param array $reservationRequests
     * @param array $availableRooms
     */
    private function processGenderRequests(string $gender, array $reservationRequests, array $availableRooms): void
    {
        $this->processOldRoomReservations(collect($reservationRequests['stayInOldLastRoom']));
        $this->processSiblingReservations(collect($reservationRequests['stayWithSibling']), collect($availableRooms['double'] ?? []));
        $this->processRegularReservations(collect($reservationRequests['regular']), $availableRooms);
    }

    /**
     * Process old room reservations.
     *
     * @param Collection $requests
     * @return array
     */
    private function processOldRoomReservations(Collection $requests): array
    {
        $results = ['success' => [], 'failed' => []];

        foreach ($requests as $request) {
            try {
                if (!$request->old_room_id) {
                    throw new Exception('Old room ID not provided');
                }

                $oldRoom = Room::lockForUpdate()->findOrFail($request->old_room_id);

                $previousReservation = Reservation::where('user_id', $request->user_id)
                    ->where('period_type', 'long')
                    ->where('status', 'completed')
                    ->latest('created_at')
                    ->first();

                if (!$previousReservation || $previousReservation->room_id !== $oldRoom->id) {
                    throw new BusinessRuleException('Old room was not part of the last long-term reservation.');
                }

                $existingReservation = Reservation::where('room_id', $oldRoom->id)
                    ->where('academic_term_id', $request->academic_term_id)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->exists();

                if ($existingReservation) {
                    throw new BusinessRuleException('Old room is already reserved in the selected academic term.');
                }

                $reservation = $this->newReservation($request, $oldRoom);

                $results['success'][] = [
                    'request_id' => $request->id,
                    'room_id' => $oldRoom->id,
                ];
            } catch (Exception $e) {
                logError('Error processing old room reservation', 'process_old_room_reservations', $e);
                $results['failed'][] = [
                    'request_id' => $request->id,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Process sibling reservations.
     *
     * @param Collection $requests
     * @param Collection $doubleRooms
     * @return array
     */
    private function processSiblingReservations(Collection $requests, Collection $doubleRooms): array
    {
        $results = ['success' => [], 'failed' => []];
        $processedIds = [];

        foreach ($requests as $request) {
            if (in_array($request->id, $processedIds)) {
                continue;
            }

            try {
                if (!$request->sibling_id) {
                    throw new Exception('Sibling ID not provided');
                }

                $siblingRequest = $requests->firstWhere('user_id', $request->sibling_id);

                if (!$siblingRequest) {
                    throw new BusinessRuleException('Sibling request not found');
                }

                if ($request->user?->student?->gender !== $siblingRequest->user?->student?->gender) {
                    throw new BusinessRuleException('Siblings must be of the same gender');
                }

                $room = $doubleRooms->first(function ($room) {
                    return $room->current_occupancy + 2 <= $room->max_occupancy;
                });

                if (!$room) {
                    throw new BusinessRuleException('No suitable double room available');
                }

                DB::transaction(function () use ($request, $siblingRequest, $room) {
                    $this->newReservation($request, $room);
                    $this->newReservation($siblingRequest, $room);
                });

                $processedIds[] = $request->id;
                $processedIds[] = $siblingRequest->id;

                $results['success'][] = [
                    'request_id' => $request->id,
                    'sibling_request_id' => $siblingRequest->id,
                    'room_id' => $room->id,
                ];
            } catch (Exception $e) {
                logError('Error processing sibling reservation', 'process_sibling_reservations', $e);
                $results['failed'][] = [
                    'request_id' => $request->id,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Process regular reservations.
     *
     * @param Collection $requests
     * @param array $availableRooms
     * @return array
     */
    private function processRegularReservations(Collection $requests, array $availableRooms): array
    {
        $results = ['success' => [], 'failed' => []];

        foreach ($requests as $request) {
            try {
                $roomType = 'single';

                if (!isset($availableRooms[$roomType])) {
                    throw new Exception("Invalid room type: {$roomType}");
                }

                $room = $this->findAvailableRoom(collect($availableRooms[$roomType]));

                if (!$room) {
                    throw new BusinessRuleException('No suitable room available');
                }

                $reservation = $this->newReservation($request, $room);
                $results['success'][] = [
                    'request_id' => $request->id,
                    'room_id' => $room->id,
                ];
            } catch (Exception $e) {
                logError('Error processing regular reservation', 'process_regular_reservations', $e);
                $results['failed'][] = [
                    'request_id' => $request->id,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Find an available room with lock.
     *
     * @param Collection $rooms
     * @return Room|null
     */
    private function findAvailableRoom(Collection $rooms): ?Room
    {
        try {
            foreach ($rooms as $room) {
                $freshRoom = Room::lockForUpdate()->find($room->id);
                if ($freshRoom && $freshRoom->current_occupancy < $freshRoom->max_occupancy) {
                    return $freshRoom;
                }
            }
            return null;
        } catch (Exception $e) {
            logError('Failed to find available room', 'find_available_room', $e);
            return null;
        }
    }

   /**
 * Create reservation with atomic operations.
 *
 * @param ReservationRequest $request
 * @param Room $room
 * @return Reservation
 * @throws BusinessRuleException|Exception
 */
public function newReservation(ReservationRequest $request, Room $room): Reservation
{
    try {
        // Check room availability and throw exception if unavailable
        $this->checkRoomAvailability($room, $request->user);
        
        return DB::transaction(function () use ($room, $request) {
            // Lock the room to prevent simultaneous reservations
            $room = Room::where('id', $room->id)->lockForUpdate()->firstOrFail();

            if ($room->full_occupied) {
                throw new BusinessRuleException(trans('The room became fully occupied while processing your request.'));
            }

            $room->increment('current_occupancy');
            if ($room->current_occupancy >= $room->max_occupancy) {
                $room->update(['full_occupied' => true]);
            }

            // Create reservation manually
            $reservation = new Reservation();
            $reservation->user_id = $request->user_id;
            $reservation->room_id = $room->id;
            $reservation->status = 'pending';
            $reservation->period_type = $request->period_type;

            if ($request->period_type === 'long') {
                $reservation->academic_term_id = $request->academic_term_id;
            } else if ($request->period_type === 'short') {
                $reservation->start_date = $request->start_date;
                $reservation->end_date = $request->end_date;
            }

            // Save the reservation
            $reservation->save();

            // Create invoice and update request status
            $this->createInvoice($reservation);
            $request->update(['status' => 'accepted']);
            event(new ReservationCreated($reservation));

            return $reservation;
        });
    } catch (Exception $e) {
        logError('Failed to create new reservation', 'new_reservation', $e);
        throw $e;
    }
}


/**
 * Check if a room is available for reservation and throw exceptions if unavailable.
 *
 * @param Room $room
 * @param User $user
 * @throws BusinessRuleException
 */
private function checkRoomAvailability(Room $room, User $user): void
{
    if ($room->full_occupied) {
        throw new BusinessRuleException(trans('The room is fully occupied and cannot accommodate more residents.'));
    }

    if ($room->status !== 'active') {
        throw new BusinessRuleException(trans('This room is currently inactive and unavailable for reservation.'));
    }

    if ($room->purpose !== 'accommodation') {
        throw new BusinessRuleException(trans('This room is not designated for accommodation purposes.'));
    }

    if ($room->apartment->building->gender !== $user->gender) {
        throw new BusinessRuleException(trans('This room does not match resident gender and cannot be reserved.'));
    }
}




    /**
     * Create an invoice for the reservation.
     *
     * @param Reservation $reservation
     * @throws Exception
     */
    private function createInvoice(Reservation $reservation): void
    {
        try {
            $invoice = Invoice::create([
                'reservation_id' => $reservation->id,
                'status' => 'unpaid',
            ]);

            $roomType = $reservation->room->type;

            if ($reservation->period_type === 'long') {
                $this->addLongTermInvoiceDetails($invoice, $roomType);
            } else {
                $this->addShortTermInvoiceDetails($invoice, $reservation);
            }
        } catch (Exception $e) {
            logError('Error creating invoice for reservation', 'create_invoice', $e);
            throw $e;
        }
    }

    /**
     * Add long-term invoice details.
     *
     * @param Invoice $invoice
     * @param string $roomType
     * @throws Exception
     */
    private function addLongTermInvoiceDetails(Invoice $invoice, string $roomType): void
    {
        try {
            $feeAmount = $roomType === 'single' ? 10000 : 8000;

            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'category' => 'fee',
                'amount' => $feeAmount,
            ]);

            $user = $invoice->reservation->user;
            $academicTerm = $invoice->reservation->academicTerm;

            if (!$user->lastReservation($academicTerm->id)) {
                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'category' => 'insurance',
                    'amount' => 5000,
                ]);
            }
        } catch (Exception $e) {
            logError('Failed to add long-term invoice details', 'add_long_term_invoice_details', $e);
            throw $e;
        }
    }

    /**
     * Add short-term invoice details.
     *
     * @param Invoice $invoice
     * @param Reservation $reservation
     * @throws Exception
     */
    private function addShortTermInvoiceDetails(Invoice $invoice, Reservation $reservation): void
    {
        try {
            $price = $this->calculateFeePrice($reservation->start_date, $reservation->end_date);

            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'category' => 'fee',
                'amount' => $price,
            ]);
        } catch (Exception $e) {
            logError('Failed to add short-term invoice details', 'add_short_term_invoice_details', $e);
            throw $e;
        }
    }

    /**
     * Calculate the fee price based on the reservation duration.
     *
     * @param $startDate
     * @param $endDate
     * @return int
     * @throws Exception
     */
    private function calculateFeePrice($startDate,$endDate): int
    {
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $days = $start->diffInDays($end);

            return match (true) {
                $days <= 1 => 300, // Daily rate
                $days <= 7 => 2000, // Weekly rate
                $days <= 30 => 2500, // Monthly rate
                default => $days * 300,
            };
        } catch (Exception $e) {
            logError('Failed to calculate fee price', 'calculate_fee_price', $e);
            throw $e;
        }
    }
}