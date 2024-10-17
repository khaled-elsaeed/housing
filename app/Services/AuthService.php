<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Check if the user is an admin.
     *
     * @param User $user
     * @return bool
     */
    public function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function isResident(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Check if the user is active.
     *
     * @param User $user
     * @return bool
     */
    public function isActive(User $user): bool
    {
        return $user->is_active; // true or false
    }

    /**
     * Check if the user is verified.
     *
     * @param User $user
     * @return bool
     */
    public function isVerified(User $user): bool
    {
        return $user->is_verified; // true or false
    }

    /**
     * Check if the user is deleted (soft deleted).
     *
     * @param User $user
     * @return bool
     */
    public function isDeleted(User $user): bool
    {
        return !is_null($user->deleted_at); // true if deleted, false if not
    }

}
