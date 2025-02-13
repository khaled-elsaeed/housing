<?php

namespace App\Services;

use App\Models\{Room, ReservationRequest,Reservation}; 

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Events\ReservationCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CreateReservationService
{
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

        return DB::transaction(function () use ($room, &$reservationData, $request) {
            // Lock the room to prevent race conditions
            $room = Room::where('id', $room->id)->lockForUpdate()->first();

            if ($room->current_occupancy >= $room->max_occupancy) {
                throw new \Exception('Room is already fully occupied.');
            }

            $room->increment('current_occupancy');
            $room->refresh();

            if ($room->current_occupancy >= $room->capacity) {
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
            Log::error('Failed to create invoice', [
                'reservation_id' => $reservation->id ?? null,
                'error_message' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Add long-term invoice details.
     *
     * @param Invoice $invoice
     * @param string $roomType
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
            Log::error('Failed to add long-term invoice details', [
                'invoice_id' => $invoice->id ?? null,
                'room_type' => $roomType,
                'error_message' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to add long-term invoice details: ' . $e->getMessage());
        }
    }

    /**
     * Add short-term invoice details.
     *
     * @param Invoice $invoice
     * @param Reservation $reservation
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
                'error_message' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to add short-term invoice details: ' . $e->getMessage());
        }
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
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $days = $start->diffInDays($end);

            return match (true) {
                $days <= 1 => 300, // Daily rate
                $days <= 7 => 2000, // Weekly rate
                $days <= 30 => 2500, // Monthly rate
                default => $days * 300, // Fallback daily rate
            };
        } catch (\Exception $e) {
            Log::error('Failed to calculate fee price', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'error_message' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to calculate fee price: ' . $e->getMessage());
        }
    }
}
