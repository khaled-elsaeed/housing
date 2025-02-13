<?php

namespace App\Services;

use App\Models\{ReservationRequest, User, AcademicTerm};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReservationRequestService
{
    private const VALID_STATUSES = ['pending', 'accepted', 'upcoming', 'complete'];
    private const PERIOD_TYPES = ['short', 'long'];

    /**
     * Create a reservation request for a user.
     *
     * @param User $user The user making the reservation request.
     * @param array $data The data for the reservation request.
     * @return ReservationRequest The created reservation request.
     * @throws \Exception If an error occurs during creation.
     */
    public function createReservationRequest(User $user, array $data): ReservationRequest
    {
        try {
            return DB::transaction(function () use ($user, $data) {
                $this->validateReservationPeriod($user->id, $data['reservation_period_type'], $data);
                return $this->newReservationRequest($user->id, $data);
            });
        } catch (\Exception $e) {
            Log::error('Error creating reservation request', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Validate reservation period based on type (short or long).
     *
     * @param int $userId The ID of the user making the request.
     * @param string $periodType The type of reservation period (short or long).
     * @param array $data The reservation data.
     * @return void
     */
    private function validateReservationPeriod(int $userId, string $periodType, array $data): void
    {
        if ($periodType === 'short') {
            $this->validateShortTermReservation($userId, $data['start_date'], $data['end_date']);
        } else {
            $this->validateLongTermReservation($userId, $data['reservation_academic_term_id']);
        }
    }

    /**
     * Validate short-term reservation dates and check for conflicts.
     *
     * @param int $userId The ID of the user making the request.
     * @param string $startDate The start date of the reservation.
     * @param string $endDate The end date of the reservation.
     * @return void
     * @throws \Exception If there is a conflict or validation fails.
     */
    private function validateShortTermReservation(int $userId, string $startDate, string $endDate): void
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $this->validateDateRange($startDate, $endDate);

        if ($this->hasConflictingShortTermReservation($userId, $startDate, $endDate)) {
            throw new \Exception('You have an overlapping short-term reservation.');
        }

        if ($this->hasConflictingLongTermReservation($userId, $startDate, $endDate)) {
            throw new \Exception('You have an overlapping long-term reservation.');
        }
    }

    /**
     * Check for conflicting short-term reservations.
     *
     * @param int $userId The ID of the user making the request.
     * @param Carbon $startDate The start date of the reservation.
     * @param Carbon $endDate The end date of the reservation.
     * @return bool True if a conflict exists, otherwise false.
     */
    private function hasConflictingShortTermReservation(int $userId, Carbon $startDate, Carbon $endDate): bool
    {
        return ReservationRequest::query()
            ->where('user_id', $userId)
            ->where('period_type', 'short')
            ->whereIn('status', self::VALID_STATUSES)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }

    /**
     * Check for conflicting long-term reservations.
     *
     * @param int $userId The ID of the user making the request.
     * @param Carbon $startDate The start date of the reservation.
     * @param Carbon $endDate The end date of the reservation.
     * @return bool True if a conflict exists, otherwise false.
     */
    private function hasConflictingLongTermReservation(int $userId, Carbon $startDate, Carbon $endDate): bool
    {
        return ReservationRequest::query()
            ->where('user_id', $userId)
            ->where('period_type', 'long')
            ->whereIn('status', self::VALID_STATUSES)
            ->whereHas('academicTerm', function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($inner) use ($startDate, $endDate) {
                            $inner->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                });
            })
            ->exists();
    }

    /**
     * Validate date range for short-term reservations.
     *
     * @param Carbon $startDate The start date of the reservation.
     * @param Carbon $endDate The end date of the reservation.
     * @return void
     * @throws \Exception If the date range is invalid.
     */
    private function validateDateRange(Carbon $startDate, Carbon $endDate): void
    {
        if ($startDate->isAfter($endDate)) {
            throw new \Exception('Start date must be before end date.');
        }

        if ($startDate->isPast()) {
            throw new \Exception('Start date cannot be in the past.');
        }
    }

    /**
     * Validate long-term reservation for academic term.
     *
     * @param int $userId The ID of the user making the request.
     * @param int $academicTermId The ID of the academic term.
     * @return void
     * @throws \Exception If the academic term is invalid or a conflict exists.
     */
    private function validateLongTermReservation(int $userId, int $academicTermId): void
    {
        $academicTerm = AcademicTerm::findOrFail($academicTermId);

        if (Carbon::parse($academicTerm->end_date)->isPast()) {
            throw new \Exception('Cannot make reservations for past academic terms.');
        }

        if ($this->hasExistingTermReservation($userId, $academicTermId)) {
            throw new \Exception('You already have a reservation for this academic term.');
        }
    }

    /**
     * Check for existing long-term reservation for the academic term.
     *
     * @param int $userId The ID of the user making the request.
     * @param int $academicTermId The ID of the academic term.
     * @return bool True if a reservation exists, otherwise false.
     */
    private function hasExistingTermReservation(int $userId, int $academicTermId): bool
    {
        return ReservationRequest::where('user_id', $userId)
            ->where('period_type', 'long')
            ->where('academic_term_id', $academicTermId)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();
    }

    /**
     * Create a new base reservation request.
     *
     * @param int $userId The ID of the user making the request.
     * @param array $data The reservation data.
     * @return ReservationRequest The created reservation request.
     */
    public function newReservationRequest(int $userId, array $data): ReservationRequest
    {
        $reservation = ReservationRequest::create([
            'user_id' => $userId,
            'period_type' => $data['reservation_period_type'],
            'stay_in_last_old_room' => $data['stay_in_last_old_room'] ?? null,
            'old_room_id' => $data['old_room_id'] ?? null,
            'sibling_id' => $data['sibling_id'] ?? null,
            'share_with_sibling' => $data['share_with_sibling'] ?? null,
            'status' => 'pending',
        ]);

        $this->addPeriodSpecificData($reservation, $data);
        return $reservation;
    }

    /**
     * Add period-specific data to the reservation.
     *
     * @param ReservationRequest $reservation The reservation to update.
     * @param array $data The reservation data.
     * @return void
     */
    private function addPeriodSpecificData(ReservationRequest $reservation, array $data): void
    {
        $updateData = $data['reservation_period_type'] === 'long'
            ? ['academic_term_id' => $data['reservation_academic_term_id']]
            : ['start_date' => $data['start_date'], 'end_date' => $data['end_date']];

        $reservation->update($updateData);
    }
}