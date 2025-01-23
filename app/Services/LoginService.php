<?php
namespace App\Services;

use App\Models\User;
use App\Models\UserNationalLink;

class LoginService
{
    public function isEmailOrNationalId($value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? 'email' 
               : (preg_match('/^\d{14}$/', $value) ? 'national_id' : false);
    }

    public function findUserByEmailOrNationalId(string $identifier): ?User
    {
        $inputType = $this->isEmailOrNationalId($identifier);

        if ($inputType === false) {
            return null; 
        }

        if ($inputType === 'email') {
            return User::findUserByEmail($identifier);
        } else {
            $userNationalLink = UserNationalLink::findUserByNationalID($identifier);
            return $userNationalLink ? $userNationalLink->user : null;
        }
    }

    public function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function isResident(User $user): bool
    {
        return $user->hasRole('resident');
    }

    
    public function handleStudentAfterLogin(User $user)
    {
        $checks = [];
    
        if ($user->isDeleted()) {
            $checks['account'] = __('auth.login.account_deleted');
        }
    
        if (!$user->isActive()) {
            $checks['account'] = __('auth.login.account_inactive');
        }
    
        if (!$user->isVerified()) {
            $checks['account'] = __('auth.login.account_not_verified');
        }
    
        // Return result array
        return [
            'status' => empty($checks) ? 'success' : 'error',
            'checks' => $checks
        ];
    }
    
    
}


