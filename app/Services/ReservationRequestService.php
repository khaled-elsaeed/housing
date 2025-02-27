<?php

namespace App\Services;

use App\Models\{ReservationRequest, User, AcademicTerm};
use App\Events\ReservationRequested;
use App\Exceptions\BusinessRuleException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

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
     * @throws Exception
     */
    public function createReservationRequest(User $user, array $data): ReservationRequest
    {
        try {
            return DB::transaction(function () use ($user, $data) {
                $this->validateReservationPeriod($user->id, $data['reservation_period_type'], $data);
                return $this->newReservationRequest($user->id, $data);
            });
        } catch (Exception $e) {
            logError('Error creating reservation request', 'create_reservation_request', $e);
            throw $e;
        }
    }

    /**
     * Validate reservation period based on type and data.
     *
     * @param int $userId
     * @param string $periodType
     * @param array $data
     * @throws BusinessRuleException
     */
    private function validateReservationPeriod(int $userId, string $periodType, array $data): void
    {
        if (!in_array($periodType, self::PERIOD_TYPES)) {
            throw new BusinessRuleException('Invalid reservation period type.');
        }

        if ($periodType === 'short') {
            if (($data['short_period_duration'] ?? '') === 'day') {
                $this->validateSingleDayReservation($userId, $data['start_date']);
            } else {
                $this->validateShortTermReservation($userId, $data['start_date'], $data['end_date']);
            }
        } else {
            $this->validateLongTermReservation($userId, $data['reservation_academic_term_id']);
        }
    }

    /**
     * Validate single-day reservation and check for conflicts.
     *
     * @param int $userId
     * @param string $startDate
     * @throws BusinessRuleException
     */
    private function validateSingleDayReservation(int $userId, string $startDate): void
    {
        $startDate = Carbon::parse($startDate);

        if ($startDate->isPast()) {
            throw new BusinessRuleException('Start date cannot be in the past.');
        }

        if ($this->hasConflictingShortTermReservation($userId, $startDate, $startDate)) {
            throw new BusinessRuleException('You have an overlapping short-term reservation.');
        }

        if ($this->hasConflictingLongTermReservation($userId, $startDate, $startDate)) {
            throw new BusinessRuleException('You have an overlapping long-term reservation.');
        }
    }

    /**
     * Validate short-term reservation dates and check for conflicts.
     *
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @throws BusinessRuleException
     */
    private function validateShortTermReservation(int $userId, string $startDate, string $endDate): void
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $this->validateDateRange($startDate, $endDate);

        if ($this->hasConflictingShortTermReservation($userId, $startDate, $endDate)) {
            throw new BusinessRuleException('You have an overlapping short-term reservation.');
        }

        if ($this->hasConflictingLongTermReservation($userId, $startDate, $endDate)) {
            throw new BusinessRuleException('You have an overlapping long-term reservation.');
        }
    }

    /**
     * Validate date range for short-term reservations.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @throws BusinessRuleException
     */
    private function validateDateRange(Carbon $startDate, Carbon $endDate): void
    {
        if ($startDate->isAfter($endDate)) {
            throw new BusinessRuleException('Start date must be before end date.');
        }

        if ($startDate->isPast()) {
            throw new BusinessRuleException('Start date cannot be in the past.');
        }
    }

    /**
     * Validate long-term reservation for academic term.
     *
     * @param int $userId
     * @param int $academicTermId
     * @throws BusinessRuleException
     */
    private function validateLongTermReservation(int $userId, int $academicTermId): void
    {
        try {
            $academicTerm = AcademicTerm::findOrFail($academicTermId);

            if (Carbon::parse($academicTerm->end_date)->isPast()) {
                throw new BusinessRuleException('Cannot make reservations for past academic terms.');
            }

            if ($this->hasExistingTermReservationRequest($userId, $academicTermId)) {
                throw new BusinessRuleException('You already have a request reservation for this academic term.');
            }
        } catch (ModelNotFoundException $e) {
            throw new BusinessRuleException('Invalid academic term ID.');
        }
    }

    /**
     * Check for conflicting short-term reservations.
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return bool
     */
    private function hasConflictingShortTermReservation(int $userId, Carbon $startDate, Carbon $endDate): bool
    {
        try {
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
        } catch (Exception $e) {
            logError('Failed to check short-term reservation conflicts', 'check_short_term_conflicts', $e);
            return false; // Default to no conflict on error
        }
    }

    /**
     * Check for conflicting long-term reservations.
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return bool
     */
    private function hasConflictingLongTermReservation(int $userId, Carbon $startDate, Carbon $endDate): bool
    {
        try {
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
        } catch (Exception $e) {
            logError('Failed to check long-term reservation conflicts', 'check_long_term_conflicts', $e);
            return false; // Default to no conflict on error
        }
    }

    /**
     * Check for existing long-term reservation for the academic term.
     *
     * @param int $userId
     * @param int $academicTermId
     * @return bool
     */
    private function hasExistingTermReservationRequest(int $userId, int $academicTermId): bool
    {
        try {
            return ReservationRequest::where('user_id', $userId)
                ->where('period_type', 'long')
                ->where('academic_term_id', $academicTermId)
                ->whereIn('status', ['pending', 'accepted'])
                ->exists();
        } catch (Exception $e) {
            logError('Failed to check existing term reservation request', 'check_existing_term_reservation', $e);
            return false; // Default to no existing request on error
        }
    }

    /**
     * Create a new reservation request.
     *
     * @param int $userId
     * @param array $data
     * @return ReservationRequest
     * @throws Exception
     */
    private function newReservationRequest(int $userId, array $data): ReservationRequest
    {
        try {
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

            // Trigger event and log activity
            event(new ReservationRequested($reservation));
            return $reservation;
        } catch (Exception $e) {
            logError('Failed to create new reservation request', 'new_reservation_request', $e);
            throw $e;
        }
    }

    /**
     * Add period-specific data to the reservation.
     *
     * @param ReservationRequest $reservation
     * @param array $data
     * @throws Exception
     */
    private function addPeriodSpecificData(ReservationRequest $reservation, array $data): void
    {
        try {
            $updateData = $data['reservation_period_type'] === 'long'
                ? ['academic_term_id' => $data['reservation_academic_term_id']]
                : ['start_date' => $data['start_date'], 'end_date' => $data['end_date']];

            $reservation->update($updateData);
        } catch (Exception $e) {
            logError('Failed to add period-specific data to reservation', 'add_period_specific_data', $e);
            throw $e;
        }
    }
}