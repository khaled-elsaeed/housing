<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNationalLink;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class LoginService
{
    /**
     * Determine if the input is an email or national ID.
     *
     * @param string $value
     * @return string|false 'email', 'national_id', or false if invalid
     */
    public function isEmailOrNationalId($value): string|false
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        if (preg_match('/^\d{14}$/', $value)) {
            return 'national_id';
        }
        return false;
    }

    /**
     * Find a user by email or national ID.
     *
     * @param string $identifier
     * @return User|null
     */
    public function findUserByEmailOrNationalId(string $identifier): ?User
    {
        $inputType = $this->isEmailOrNationalId($identifier);

        if ($inputType === false) {
            return null;
        }

        if ($inputType === 'email') {
            return User::findUserByEmail($identifier);
        }

        $userNationalLink = UserNationalLink::findUserByNationalID($identifier);
        return $userNationalLink ? $userNationalLink->user : null;
    }

    /**
     * Check if the user is an admin or housing manager.
     *
     * @param User $user
     * @return bool
     */
    public function isAdmin(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('housing_manager');
    }

    /**
     * Check if the user is a resident.
     *
     * @param User $user
     * @return bool
     */
    public function isResident(User $user): bool
    {
        return $user->hasRole('resident');
    }

    /**
     * Check if the user is a technician.
     *
     * @param User $user
     * @return bool
     */
    public function isTechnician(User $user): bool
    {
        return $user->hasRole('technician'); // Fixed typo
    }

    /**
     * Handle post-login checks for student/resident users.
     *
     * @param User $user
     * @return array
     */
    public function handleStudentAfterLogin(User $user): array
    {
        $checks = [];

        $settingValue = Setting::where('key', 'under_maintenance')->value('value');
        if ($settingValue === null) {
            Log::warning('Maintenance setting not found, defaulting to off');
            $settingValue = 0; // Default to off instead of on
        }

        if ($settingValue == 1) {
            $checks['maintenance'] = trans('under_maintenance');
        }

        if ($user->isDeleted()) {
            $checks['account'] = trans('account_deleted');
        }

        if (!$user->isActive()) {
            $checks['account'] = trans('account_inactive');
        }

        if (!$user->isVerified()) {
            $checks['account'] = trans('not_verified');
        }

        return [
            'status' => empty($checks) ? 'success' : 'error',
            'checks' => $checks
        ];
    }
}