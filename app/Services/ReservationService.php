<?php

namespace App\Services;

use App\Models\{User, Room, Reservation, Invoice, InvoiceDetail, ReservationRequest};
use Illuminate\Support\Facades\{DB, Log, Queue};
use Illuminate\Support\Carbon;
use App\Events\{ReservationCreated, ReservationRequested};
use App\Jobs\ProcessReservationRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        return ReservationRequest::with(['user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Filter reservation requests by gender and room preferences.
     *
     * @param Collection $reservationRequests
     * @return array
     */
    private function filterRequests(Collection $reservationRequests): array
    {
        $filterByGenderAndPreference = function (string $gender, Collection $requests) {
            return [
                'stayInOldLastRoom' => $requests->filter(function ($request) use ($gender) {
                    return $request->stay_in_last_old_room === true && 
                           $request->user?->gender === $gender;
                })->sortByDesc('created_at'),

                'stayWithSibling' => $requests->filter(function ($request) use ($gender) {
                    return $request->stay_with_sibling === true && 
                           $request->user?->gender === $gender;
                })->sortByDesc('created_at'),

                'regular' => $requests->filter(function ($request) use ($gender) {
                    return $request->user?->gender === $gender &&
                           ($request->stay_in_last_old_room === false || $request->stay_in_last_old_room === null) &&
                           ($request->stay_with_sibling === false || $request->stay_with_sibling === null);
                })->sortByDesc('created_at')
            ];
        };

        return [
            'male' => $filterByGenderAndPreference('male', $reservationRequests),
            'female' => $filterByGenderAndPreference('female', $reservationRequests),
        ];
    }

    /**
     * Get available rooms filtered by gender and type.
     *
     * @return array
     */
    public function getAvailableRooms(): array
    {
        $rooms = Room::with(['apartment.building'])
            ->where('status', 'active')
            ->where('full_occupied', false)
            ->where('purpose', 'accommodation')
            ->get();

        return $this->filterRoomsByGender($rooms);
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
            'female' => ['single' => [], 'double' => []]
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
     * Automate the reservation process with background processing support
     *
     * @param bool $background
     * @return void
     */
    public function automateReservationProcess(bool $background = true): void
    {
        if ($background) {
            Queue::push(new ProcessReservationRequests());
            return;
        }
        
        $this->processRequests();
    }

    /**
     * Process reservation requests (to be used in job)
     *
     * @throws Exception
     */
    public function processRequests(): void
    {
        try {
            DB::beginTransaction();
            
            $totalRequests = ReservationRequest::where('status', 'pending')->count();
            Log::info("Starting reservation processing", ['total_requests' => $totalRequests]);

            $reservationRequests = $this->getReservationRequests();
            $filteredRequests = $this->filterRequests($reservationRequests);
            $availableRooms = $this->getAvailableRooms();

            foreach (['male', 'female'] as $gender) {
                $this->processGenderRequests(
                    $gender, 
                    $filteredRequests[$gender], 
                    $availableRooms[$gender]
                );
            }

            DB::commit();
            Log::info("Reservation processing completed successfully");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Reservation process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process requests for a specific gender
     * 
     * @param string $gender
     * @param array $reservationRequests
     * @param array $availableRooms
     * @return void
     */
    private function processGenderRequests(string $gender, array $reservationRequests, array $availableRooms): void
    {
        Log::info("Processing requests for gender", ['gender' => $gender]);
        
        $results = [
            'old_room' => $this->processOldRoomReservations($reservationRequests['stayInOldLastRoom']),
            'sibling' => $this->processSiblingReservations($reservationRequests['stayWithSibling'], collect($availableRooms['double'] ?? [])),
            'regular' => $this->processRegularReservations($reservationRequests['regular'], $availableRooms)
        ];
        
        Log::info("Completed processing for gender", [
            'gender' => $gender,
            'results' => $results
        ]);
    }

    /**
     * Process old room reservations
     * 
     * @param Collection $requests
     * @return array
     */
    private function processOldRoomReservations(Collection $requests): array
    {
        Log::info("Processing old room reservations", ['count' => $requests->count()]);
        $results = ['success' => [], 'failed' => []];

        foreach ($requests as $request) {
            try {
                if (!$request->last_old_room_id) {
                    throw new Exception('Old room ID not provided');
                }

                $oldRoom = Room::lockForUpdate()->findOrFail($request->last_old_room_id);

                if (!$oldRoom->full_occupied) {
                    $reservation = $this->createReservation($request, $oldRoom);
                    $results['success'][] = [
                        'request_id' => $request->id,
                        'reservation_id' => $reservation->id
                    ];
                    
                } else {
                    $results['failed'][] = [
                        'request_id' => $request->id,
                        'reason' => 'Room is fully occupied'
                    ];
                }
            } catch (Exception $e) {
                Log::error("Old room reservation failed", [
                    'request_id' => $request->id,
                    'error' => $e->getMessage()
                ]);
                
                $results['failed'][] = [
                    'request_id' => $request->id,
                    'reason' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Process sibling reservations
     * 
     * @param Collection $requests
     * @param Collection $doubleRooms
     * @return array
     */
    private function processSiblingReservations(Collection $requests, Collection $doubleRooms): array
    {
        Log::info("Processing sibling reservations", ['count' => $requests->count()]);
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
                    return $room->current_occupancy === 0;
                });

                if (!$room) {
                    throw new Exception('No suitable double room available');
                }

                DB::transaction(function () use ($request, $siblingRequest, $room) {
                    $reservation1 = $this->createReservation($request, $room);
                    $reservation2 = $this->createReservation($siblingRequest, $room);
                                    });

                $processedIds[] = $request->id;
                $processedIds[] = $siblingRequest->id;
                
                $results['success'][] = [
                    'request_id' => $request->id,
                    'sibling_request_id' => $siblingRequest->id,
                    'room_id' => $room->id
                ];

            } catch (Exception $e) {
                Log::error("Sibling reservation failed", [
                    'request_id' => $request->id,
                    'error' => $e->getMessage()
                ]);
                
                $results['failed'][] = [
                    'request_id' => $request->id,
                    'reason' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Process regular reservations
     * 
     * @param Collection $requests
     * @param array $availableRooms
     * @return array
     */
    private function processRegularReservations(Collection $requests, array $availableRooms): array
    {
        Log::info("Processing regular reservations", ['count' => $requests->count()]);
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

                $reservation = $this->createReservation($request, $room);
                
                $results['success'][] = [
                    'request_id' => $request->id,
                    'reservation_id' => $reservation->id
                ];

            } catch (Exception $e) {
                Log::error("Regular reservation failed", [
                    'request_id' => $request->id,
                    'error' => $e->getMessage()
                ]);
                
                $results['failed'][] = [
                    'request_id' => $request->id,
                    'reason' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Find an available room with lock
     * 
     * @param Collection $rooms
     * @return Room|null
     */
    private function findAvailableRoom(Collection $rooms): ?Room
{

    foreach ($rooms as $room) {
        $freshRoom = Room::lockForUpdate()->find($room->id);

        if (!$freshRoom) {
            Log::error('Room not found during lockForUpdate', ['room_id' => $room->id]);
            continue;
        }

        Log::info('Checking room occupancy:', ['room_id' => $freshRoom->id, 'current' => $freshRoom->current_occupancy, 'max' => $freshRoom->max_occupancy]);

        if ($freshRoom->current_occupancy < $freshRoom->max_occupancy) {
            return $freshRoom;
        }
    }

    Log::warning('No available room found.');
    return null;
}


    /**
 * Create reservation with atomic operations
 * 
 * @param ReservationRequest $request
 * @param Room $room
 * @return Reservation
 */
private function createReservation(ReservationRequest $request, Room $room): Reservation
{

    $reservationData = [
        'user_id' => $request->user_id,
        'room_id' => $room->id,
        'status' => 'active',
        'reservation_request_id' => $request->id,
        'period_type' => $request->period_type,
    ];

    if ($request->period_type === 'long') {
        $reservationData['academic_term_id'] = $request->academic_term_id;
    } else {
        $reservationData['start_date'] = $request->start_date;
        $reservationData['end_date'] = $request->end_date;
    }

    Log::info('Creating reservation', $reservationData);

    return DB::transaction(function () use ($room, &$reservationData,$request) {
        Room::where('id', $room->id)
            ->where('current_occupancy', '<', $room->max_occupancy)
            ->increment('current_occupancy');

        $room->refresh();

        if ($room->current_occupancy >= $room->capacity) {
            $room->update(['full_occupied' => true]);
        }
        $reservation = Reservation::create($reservationData);
        $this->createInvoice($reservation);
        $request->update(['status' => 'accepted']);
        event(new ReservationCreated($reservation));
        return  $reservation;          
    });

    
}

/**
     * Create an invoice for the reservation.
     *
     * @param Reservation $reservation
     */
    private function createInvoice(
        Reservation $reservation
    ): void {

        $invoice = Invoice::create([
            "reservation_id" => $reservation->id,
            "status" => "unpaid",
        ]);

        $roomType = $reservation->room->type;

        if ($reservation->period_type === "long") {
            $this->addLongTermInvoiceDetails($invoice,$roomType);
        } else {
            $this->addShortTermInvoiceDetails($invoice, $reservation);
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

        // Determine fee amount based on room type
        $feeAmount = $roomType === 'single' ? 10000 : 8000;

        // Add the base fee to the invoice
        InvoiceDetail::create([
            "invoice_id" => $invoice->id,
            "category" => "fee",
            "amount" => $feeAmount,
        ]);

        $user = $invoice->reservation->user;
        $academicTerm = $invoice->reservation->academicTerm;


        $lastReservation = $user->lastReservation($academicTerm->id); 

        if (!$lastReservation) {

            InvoiceDetail::create([
                "invoice_id" => $invoice->id,
                "category" => "insurance",
                "amount" => 5000,
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
    private function addShortTermInvoiceDetails(Invoice $invoice, Reservation $reservation): void
    {
        $price = $this->calculateFeePrice($reservation->start_date,$reservation->end_date,);

        InvoiceDetail::create([
            "invoice_id" => $invoice->id,
            "category" => "fee",
            "amount" => $price,
        ]);
    }

    /**
 * Calculate the fee price based on the reservation duration.
 *
 * @param string $startDate
 * @param string $endDate
 * @return int
 */
private function calculateFeePrice(string $startDate, string $endDate): int
{
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);
    $days = $start->diffInDays($end);

    return match (true) {
        $days <= 1 => 300, // Daily rate
        $days <= 7 => 2000, // Weekly rate
        $days <= 30 => 2500, // Monthly rate
        default => $days * 300, // Fallback daily rate
    };
}



}