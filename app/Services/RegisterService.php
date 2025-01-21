<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNationalLink;
use App\Models\UniversityArchiveLite;
use App\Notifications\AccountActivation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class RegisterService
{
    /**
     * Check if the registration is open.
     *
     * @return bool
     */
    public function isRegistrationOpen(): bool
    {
        $setting = \App\Models\Setting::where('key', 'registration_open')->first();
        return $setting && $setting->value == 1;
    }

    /**
     * Check if a national ID belongs to a university student.
     *
     * @param string $nationalId
     * @return bool
     */
    public function isUniversityStudent(string $nationalId): bool
    {
        return UniversityArchiveLite::where('national_id', $nationalId)->exists();
    }

    /**
     * Register a new user.
     *
     * @param array $data
     * @return User
     * @throws ValidationException
     */
    public function registerUser(array $data): User
    {
        // Check if registration is open
        if (!$this->isRegistrationOpen()) {
            throw ValidationException::withMessages([
                'registration' => __('auth.register.registration_closed'),
            ]);
        }

        // Check if the national ID belongs to a university student
        $studentRecord = UniversityArchiveLite::where('national_id', $data['national_id'])->first();
        // link to his full arhive data record
        $studentArchive = UniversityArchive::where('national_id',$data['national_id'])->first();

        if (!$studentRecord) {
            throw ValidationException::withMessages([
                'national_id' => __('auth.register.student_not_registered'),
            ]);
        }

        // Extract first and last names
        $namesEn = $this->splitFullName($studentRecord->name_en);
        $namesAr = $this->splitFullName($studentRecord->name_ar);

        // Create the user
        $user = User::create([
            'first_name_en' => $namesEn['first_name'],
            'last_name_en' => $namesEn['last_name'],
            'first_name_ar' => $namesAr['first_name'],
            'last_name_ar' => $namesAr['last_name'],
            'password' => Hash::make($data['password']),
            'email' => $data['email'],
            'activation_token' => Str::random(60),
            'is_active' => 1,
            'activation_expires_at' => Carbon::now()->addHours(2),
        ]);

        // Assign the "resident" role
        $user->assignRole('resident');

        // Link the user to the university archive record
        UserNationalLink::create([
            'user_id' => $user->id,
            'national_id' => $data['national_id'],
            'university_archive_id' => $studentArchive->id,
        ]);

        // Send account activation notification
        $user->notify(new AccountActivation($user));

        return $user;
    }

    /**
     * Split a full name into first and last names.
     *
     * @param string $fullName
     * @return array
     */
    private function splitFullName(string $fullName): array
    {
        $names = explode(' ', $fullName, 2);
        return [
            'first_name' => $names[0] ?? '',
            'last_name' => $names[count($names)- 1 ] ?? '',
        ];
    }
}
