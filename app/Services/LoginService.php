<?php
namespace App\Services;

use App\Models\User;
use App\Models\UserNationalLink;
use App\Models\Setting;

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

    public function isTechnican(User $user): bool
    {
        return $user->hasRole('technician');
    }

    
    public function handleStudentAfterLogin(User $user)
    {
        $checks = [];
    
        $settingValue = Setting::where('key', 'under_maintenance')->value('value'); 

        if ($settingValue === null) {
            $settingValue = 1;  
        }

        if ($settingValue == 1) {
            $checks['maintenance'] = trans('The resident login is currently unavailable. It will be available later');
        }

        if ($user->isDeleted()) {
            $checks['account'] = trans('Your account has been deleted');
        }
    
        if (!$user->isActive()) {
            $checks['account'] = trans('Your account is inactive');
        }
    
        if (!$user->isVerified()) {
            $checks['account'] = trans('Your account is not verified');
        }
    
        // Return result array
        return [
            'status' => empty($checks) ? 'success' : 'error',
            'checks' => $checks
        ];
    }
    
    
}


