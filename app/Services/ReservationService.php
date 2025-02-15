<?php

namespace App\Services;

use App\Models\{User, Room, Reservation, Invoice, InvoiceDetail, ReservationRequest};
use Illuminate\Support\Facades\{DB, Log, Cache};
use Illuminate\Support\Carbon;
use App\Events\{ReservationCreated, ReservationRequested};
use Exception;
use Illuminate\Support\Collection;

class ReservationService
{

    /**
     * Get pending reservation requests filtered by gender and preferences.
     *
     * @return Collection
     */
    public function getReservationRequests(): Collection
    {
        return ReservationRequest::with(['user.student.faculty:id,name'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->select(['id', 'user_id', 'stay_in_last_old_room', 'share_with_sibling', 'old_room_id', 'sibling_id', 'created_at'])
            ->get();
    }

    /**
     * Get available rooms filtered by gender and type.
     *
     * @return array
     */
    private function getAvailableRooms(): array
    {
        $rooms = Room::select('rooms.*')
            ->join('apartments', 'rooms.apartment_id', '=', 'apartments.id')
            ->join('buildings', 'apartments.building_id', '=', 'buildings.id')
            ->whereNotNull('buildings.gender') // Ensures gender is set
            ->whereIn('rooms.type', ['single', 'double']) // Ensures valid types
            ->get()
            ->groupBy([
                fn($room) => $room->apartment->building->gender,
                'type'
            ]);
    
        // Ensure empty structure even if no data is found
        return [
            'male' => [
                'single' => $rooms['male']['single'] ?? [],
                'double' => $rooms['male']['double'] ?? [],
            ],
            'female' => [
                'single' => $rooms['female']['single'] ?? [],
                'double' => $rooms['female']['double'] ?? [],
            ],
        ];
    }
    
    

    

    /**
     * Automate the reservation process with background processing support.
     *
     * @return void
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

            // Fetch pending requests with relationships loaded, sort by old requested first
            $reservations = ReservationRequest::where('status', 'pending')
                ->with(['user.student.faculty'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Sort faculty priority
            $sortedReservations = $reservations->sortBy(fn($request) =>
                $facultyRankings[$request->user->student->faculty->name] ?? 99 // Default to 99 if not ranked
            );

            // Process in chunks
            $sortedReservations->chunk(500)->each(function ($reservationRequests) {
                DB::transaction(function () use ($reservationRequests) {
                    $filteredRequests = $this->filterRequests($reservationRequests);
                    $availableRooms = $this->getAvailableRooms();

                    foreach (['male', 'female'] as $gender) {
                        $this->processGenderRequests(
                            $gender,
                            $filteredRequests[$gender],
                            $availableRooms[$gender]
                        );
                    }
                });

                Log::info('Processed batch of reservation requests', [
                    'batch_size' => $reservationRequests->count(),
                ]);
            });
        } catch (Exception $e) {
            Log::error('Error processing reservation requests', [
                'error' => $e->getMessage(),
                'action' => 'automate-reservation-process',
            ]);
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
            $gender = $request->user?->gender;
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
     * Filter rooms by gender and type.
     *
     * @param Collection $rooms
     * @return array
     */
    private function filterRoomsByGender(Collection $rooms): array
    {
        $filtered = [
            'male' => ['single' => [], 'double' => []],
            'female' => ['single' => [], 'double' => []],
        ];

        foreach ($rooms as $room) {
            $gender = $room->apartment?->building?->gender;
            if (!$gender || !isset($filtered[$gender][$room->type])) {
                continue;
            }

            $filtered[$gender][$room->type][] = $room;
        }

        return $filtered;
    }

    /**
     * Process requests for a specific gender.
     *
     * @param string $gender
     * @param array $reservationRequests
     * @param array $availableRooms
     * @return void
     */
    private function processGenderRequests(string $gender, array $reservationRequests, array $availableRooms): void
    {
        $results = [
            'old_room' => $this->processOldRoomReservations(collect($reservationRequests['stayInOldLastRoom'])),
            'sibling' => $this->processSiblingReservations(collect($reservationRequests['stayWithSibling']), collect($availableRooms['double'] ?? [])),
            'regular' => $this->processRegularReservations(collect($reservationRequests['regular']), $availableRooms),
        ];

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

                // Check if the room was in the last long-term reservation for the user
                $previousReservation = Reservation::where('user_id', $request->user_id)
                    ->where('period_type', 'long')
                    ->where('status', 'completed')
                    ->latest('created_at')
                    ->first();

                if (!$previousReservation || $previousReservation->room_id !== $oldRoom->id) {
                    throw new Exception('Old room was not part of the last long-term reservation.');
                }

                // Check if the old room is already reserved in the selected academic term
                $existingReservation = Reservation::where('room_id', $oldRoom->id)
                    ->where('academic_term_id', $request->academic_term_id)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->exists();

                if ($existingReservation) {
                    throw new Exception('Old room is already reserved in the selected academic term.');
                }

                // Create the reservation
                $reservation = $this->newReservation($request, $oldRoom);

                $results['success'][] = [
                    'request_id' => $request->id,
                    'room_id' => $oldRoom->id,
                ];
            } catch (Exception $e) {
                Log::error('Error processing old room reservation', [
                    'reservation_request_id' => $request->id,
                    'error' => $e->getMessage(),
                    'action' => 'process-old-room-reservations',
                ]);
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
                    throw new Exception('Sibling request not found');
                }

                if ($request->user?->gender !== $siblingRequest->user?->gender) {
                    throw new Exception('Siblings must be of the same gender');
                }

                $room = $doubleRooms->first(function ($room) {
                    return $room->current_occupancy + 2 <= $room->max_occupancy;
                });

                if (!$room) {
                    throw new Exception('No suitable double room available');
                }

                DB::transaction(function () use ($request, $siblingRequest, $room) {
                    $this->createReservationAndInvoice($request, $room);
                    $this->createReservationAndInvoice($siblingRequest, $room);
                });

                $processedIds[] = $request->id;
                $processedIds[] = $siblingRequest->id;

                $results['success'][] = [
                    'request_id' => $request->id,
                    'sibling_request_id' => $siblingRequest->id,
                    'room_id' => $room->id,
                ];
            } catch (Exception $e) {
                Log::error('Error processing sibling reservation', [
                    'reservation_request_id' => $request->id,
                    'error' => $e->getMessage(),
                    'action' => 'process-sibling-reservations',
                ]);
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
                    throw new Exception('No suitable room available');
                }

                $this->newReservation($request, $room);

                $results['success'][] = [
                    'request_id' => $request->id,
                    'room_id' => $room->id,
                ];
            } catch (Exception $e) {
                Log::error('Error processing regular reservation', [
                    'reservation_request_id' => $request->id,
                    'error' => $e->getMessage(),
                    'action' => 'process-regular-reservations',
                ]);
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
        foreach ($rooms as $room) {
            $freshRoom = Room::lockForUpdate()->find($room->id);

            if ($freshRoom && $freshRoom->current_occupancy < $freshRoom->max_occupancy) {
                return $freshRoom;
            }
        }

        return null;
    }

    /**
     * Create reservation with atomic operations.
     *
     * @param ReservationRequest $request
     * @param Room $room
     * @return Reservation
     * @throws \Exception
     */
    public function newReservation(ReservationRequest $request, Room $room): Reservation
    {
        $reservationData = [
            'user_id' => $request->user_id,
            'room_id' => $room->id,
            'status' => 'pending',
            'reservation_request_id' => $request->id,
            'period_type' => $request->period_type,
        ];

        if ($request->period_type === 'long') {
            $reservationData['academic_term_id'] = $request->academic_term_id;
        } else {
            $reservationData['start_date'] = $request->start_date;
            $reservationData['end_date'] = $request->end_date;
        }

        return DB::transaction(function () use ($room, &$reservationData, $request) {
            $room = Room::where('id', $room->id)->lockForUpdate()->first();

            if ($room->current_occupancy >= $room->max_occupancy) {
                throw new Exception('Room is already full');
            }

            $room->increment('current_occupancy');
            if ($room->current_occupancy >= $room->max_occupancy) {
                $room->update(['full_occupied' => true]);
            }

            $reservation = Reservation::create($reservationData);
            $this->createInvoice($reservation);
            $request->update(['status' => 'accepted']);

            event(new ReservationCreated($reservation));

            return $reservation;
        });
    }

    /**
     * Create an invoice for the reservation.
     *
     * @param Reservation $reservation
     * @return void
     * @throws \Exception
     */
    private function createInvoice(Reservation $reservation): void
    {
        try {
            $invoice = Invoice::create([
                "reservation_id" => $reservation->id,
                "status" => "unpaid",
            ]);

            $roomType = $reservation->room->type;

            if ($reservation->period_type === "long") {
                $this->addLongTermInvoiceDetails($invoice, $roomType);
            } else {
                $this->addShortTermInvoiceDetails($invoice, $reservation);
            }
        } catch (\Exception $e) {
            Log::error('Error creating invoice for reservation', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
                'action' => 'create-invoice',
            ]);
            throw $e;
        }
    }

    /**
     * Add long-term invoice details.
     *
     * @param Invoice $invoice
     * @param string $roomType
     * @return void
     * @throws \Exception
     */
    private function addLongTermInvoiceDetails(Invoice $invoice, string $roomType): void
    {
        try {
            $feeAmount = $roomType === 'single' ? 10000 : 8000;

            InvoiceDetail::create([
                "invoice_id" => $invoice->id,
                "category" => "fee",
                "amount" => $feeAmount,
            ]);

            $user = $invoice->reservation->user;
            $academicTerm = $invoice->reservation->academicTerm;

            if (!$user->lastReservation($academicTerm->id)) {
                InvoiceDetail::create([
                    "invoice_id" => $invoice->id,
                    "category" => "insurance",
                    "amount" => 5000,
                ]);
            }
        } catch (\Exception $e) {

            Log::error('Error to add long-term invoice details', [
                'invoice_id' => $invoice->id ?? null,
                'error' => $e->getMessage(),
                'action' => 'add-long-invoice-details',
            ]);
            throw $e;
        }
    }

    /**
     * Add short-term invoice details.
     *
     * @param Invoice $invoice
     * @param Reservation $reservation
     * @return void
     * @throws \Exception
     */
    private function addShortTermInvoiceDetails(Invoice $invoice, Reservation $reservation): void
    {
        try {
            $price = $this->calculateFeePrice($reservation->start_date, $reservation->end_date);

            InvoiceDetail::create([
                "invoice_id" => $invoice->id,
                "category" => "fee",
                "amount" => $price,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to add short-term invoice details', [
                'invoice_id' => $invoice->id ?? null,
                'error' => $e->getMessage(),
                'action' => 'add-short-invoice-details',
            ]);
            throw $e;
        }
    }

    /**
     * Calculate the fee price based on the reservation duration.
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     * @throws \Exception
     */
    private function calculateFeePrice(string $startDate, string $endDate): int
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
        } catch (\Exception $e) {
            Log::error('Failed to calculate fee price', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'error' => $e->getMessage(),
                'action' => 'calculate-fee-price',
            ]);
            throw $e;
        }
    }
}