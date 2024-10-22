<?php

namespace App\Services;

use App\Models\User;
use App\Models\UniversityArchive;
use App\Models\UserNationalLink;
use App\Models\HousingExpulsion;

class RegisterService
{
    public function isUniversityStudent(string $nationalId): bool
    {
        return UniversityArchive::where('national_id', $nationalId)->exists();
    }

    public function isNationalIdRegistered(string $nationalId): bool
    {
        return UserNationalLink::where('national_id', $nationalId)->exists();
    }

    public function isEmailRegistered(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function isNewComerStudent(User $user): bool
    {
        return $user->student->universityArchive->is_new_comer ?? false;
    }

    public function isOldStudent(User $user): bool
    {
        return !$this->isNewComerStudent($user);
    }

    public function getStudentData(string $nationalId)
    {
        $student = UniversityArchive::where('national_id', $nationalId)->first();
        $nameParts_ar = explode(' ', $student['name_ar']);
        $firstName_ar = $nameParts_ar[0];
        $lastName_ar = end($nameParts_ar);
        $updated_name_ar = $firstName_ar . ' ' . $lastName_ar;

        $nameParts_en = explode(' ', $student['name_en']);
        $firstName_en = $nameParts_en[0];
        $lastName_en = end($nameParts_en);
        $updated_name_en = $firstName_en . ' ' . $lastName_en;

        $student['updated_name_ar'] = $updated_name_ar;
        $student['updated_name_en'] = $updated_name_en;
        return $student;
    }
}
