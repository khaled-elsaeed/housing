<?php
namespace App\Services;

use App\Models\User;

class LoginService
{
    public function isEmailOrNationalId($value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? 'email' 
               : (preg_match('/^\d{14}$/', $value) ? 'national_id' : false);
    }

    public function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function isResident(User $user): bool
    {
        return $user->hasRole('resident');
    }

    public function isActive(User $user): bool
    {
        return (bool) $user->is_active;
    }

    public function isVerified(User $user): bool
    {
        return (bool) $user->is_verified;
    }

    public function isDeleted(User $user): bool
    {
        return !is_null($user->deleted_at);
    }

    public function hasStudentProfile(User $user): bool
    {
        return $user->student()->exists();
    }

    public function allowLateProfileCompletion(User $user): bool
    {
        return optional($user->student)->can_complete_late ?? false;
    }

    public function handleStudentAfterLogin(User $user)
    {
        if ($this->isDeleted($user)) {
            return [
                'account' => __('auth.account_deleted'),
            ];
        }

        if (!$this->isActive($user)) {
            return [
                'account' => __('auth.account_inactive'),
            ];
        }

        if (!$this->isVerified($user)) {
            return [
                'account' => __('auth.account_not_verified'),
            ];
        }

        if (!$this->hasStudentProfile($user)) {
            return [
                'profile' => __('auth.profile_incomplete'),
            ];
        }

        return true;
    }
}
