<?php

namespace App\Services;

use App\Models\{User, Room, Reservation,ReservationRequest};
use Illuminate\Support\Facades\{DB, Log, Cache};
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Collection;
use App\Jobs\CreateReservationJob;

class ReservationService
{
    private $availableRoomsCache = null;

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
            ->select(['id', 'user_id', 'stay_in_last_old_room', 'stay_with_sibling', 'last_old_room_id', 'sibling_id', 'created_at'])
            ->get();
    }

    /**
     * Get available rooms filtered by gender and type.
     *
     * @return array
     */
    public function getAvailableRooms(): array
    {
        if ($this->availableRoomsCache === null) {
            $rooms = Room::with(['apartment.building'])
                ->where('status', 'active')
                ->where('full_occupied', false)
                ->where('purpose', 'accommodation')
                ->get();

            $this->availableRoomsCache = $this->filterRoomsByGender($rooms);
        }

        return $this->availableRoomsCache;
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
            
            // Fetch pending requests with relationships loaded , sort by old requested first
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
            Log::error('Error processing reservation requests: ' . $e->getMessage());
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
            } elseif ($request->stay_with_sibling) {
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

        Log::info("Processed gender requests: $gender", $results);
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
                if (!$request->last_old_room_id) {
                    throw new Exception('Old room ID not provided');
                }

                $oldRoom = Room::lockForUpdate()->findOrFail($request->last_old_room_id);

                if (!$oldRoom->full_occupied) {
                    CreateReservationJob::dispatch($request, $oldRoom)->onQueue('reservations');
                    $results['success'][] = [
                        'request_id' => $request->id,
                        'room_id' => $oldRoom->id,
                    ];
                } else {
                    $results['failed'][] = [
                        'request_id' => $request->id,
                        'reason' => 'Room is fully occupied',
                    ];
                }
            } catch (Exception $e) {
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
                    return $room->current_occupancy === 0;
                });

                if (!$room) {
                    throw new Exception('No suitable double room available');
                }

                DB::transaction(function () use ($request, $siblingRequest, $room) {
                    CreateReservationJob::dispatch($request, $room)->onQueue('reservations');
                    CreateReservationJob::dispatch($siblingRequest, $room)->onQueue('reservations');
                });

                $processedIds[] = $request->id;
                $processedIds[] = $siblingRequest->id;

                $results['success'][] = [
                    'request_id' => $request->id,
                    'sibling_request_id' => $siblingRequest->id,
                    'room_id' => $room->id,
                ];
            } catch (Exception $e) {
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

                CreateReservationJob::dispatch($request, $room)->onQueue('reservations');

                $results['success'][] = [
                    'request_id' => $request->id,
                    'room_id' => $room->id,
                ];
            } catch (Exception $e) {
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

}