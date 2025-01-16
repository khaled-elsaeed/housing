<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CompleteProfileService
{

    public function getUserData()
    {
        return auth()->user();
    }
}