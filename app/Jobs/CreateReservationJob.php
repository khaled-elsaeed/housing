<?php

namespace App\Jobs;

use App\Models\{Room, ReservationRequest,Reservation}; 
use App\Services\CreateReservationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateReservationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ReservationRequest $request;
    protected Room $room;

    /**
     * Create a new job instance.
     *
     * @param ReservationRequest $request
     * @param Room $room
     */
    public function __construct(ReservationRequest $request, Room $room)
    {
        $this->request = $request;
        $this->room = $room;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CreateReservationService $createReservationService): void
    {
        try {
            Log::info("Processing reservation job for user {$this->request->user_id}");

            $reservation = $createReservationService->newReservation($this->request, $this->room);

            Log::info("Reservation created successfully", ['reservation_id' => $reservation->id]);
        } catch (\Exception $e) {
            Log::error("Failed to create reservation", [
                'user_id' => $this->request->user_id,
                'room_id' => $this->room->id,
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
