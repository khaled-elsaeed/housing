<?php

namespace App\Services;

use App\Models\User;
use App\Models\Setting;
use App\Models\UserNationalLink;
use App\Models\UniversityArchive;
use App\Models\UniversityArchiveLite;
use App\Notifications\AccountActivation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Jobs\SendMailJob;

class RegisterService
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return User
     * @throws ValidationException
     */
    public function registerUser(array $data): User
    {
        $this->checkRegistrationOpen();

        return DB::transaction(function () use ($data) {
            $studentRecord = $this->getAndValidateStudentRecord($data['national_id']);
            $names = $this->getNamesFromRecord($studentRecord);

            $user = $this->createUser($data, $names,$studentRecord);
            $this->assignUserRole($user);
            $archiveRecord = $this->createArchiveRecord($studentRecord);
            $this->linkUserToArchive($user, $studentRecord->national_id, $archiveRecord);
            // $this->sendActivationNotification($user);

            return $user;
        });
    }

    /**
     * Check if registration is open.
     *
     * @throws ValidationException
     */
    private function checkRegistrationOpen(): void
    {
        $setting = Setting::where('key', 'registration_open')->first();
        if (!$setting || $setting->value != 1) {
            throw ValidationException::withMessages([
                'registration' => trans('Registration is currently closed'),
            ]);
        }
    }

    /**
     * Get and validate student record.
     *
     * @param string $nationalId
     * @return UniversityArchiveLite
     * @throws ValidationException
     */
    private function getAndValidateStudentRecord(string $nationalId): UniversityArchiveLite
    {
        $studentRecord = UniversityArchiveLite::where('national_id', $nationalId)->first();

        if (!$studentRecord) {
            throw ValidationException::withMessages([
                'national_id' => trans('Student not found in university records'),
            ]);
        }

        return $studentRecord;
    }

    /**
     * Create new user.
     *
     * @param array $data
     * @param array $names
     * @param object $studentRecord
     * @return User
     */
    private function createUser(array $data, array $names, $studentRecord): User
    {
        $gender = $this->getGenderFromNationalId($studentRecord?->national_id);

        return User::create([
            'first_name_en' => $names['en']['first_name'] ?? null,
            'last_name_en' => $names['en']['last_name'] ?? null,
            'first_name_ar' => $names['ar']['first_name'] ?? null,
            'last_name_ar' => $names['ar']['last_name'] ?? null,
            'password' => Hash::make($data['password']),
            'email' => $studentRecord->academic_email,
            'gender' => $gender,
            'status' => 'active',
            'profile_completed' => 0,
            'is_verified' => 1,
        ]);
    }

    /**
     * Extract gender from National ID.
     *
     * @param string $nationalId
     * @return string
     */
    private function getGenderFromNationalId(string $nationalId): string
    {
        // Ensure the national ID is exactly 14 digits long
        if (strlen($nationalId) !== 14 || !ctype_digit($nationalId)) {
            return 'unknown'; 
        }
    
        // Extract the 13th digit (index 12 in zero-based indexing)
        $genderDigit = (int)$nationalId[12];
    
        return ($genderDigit % 2 === 0) ? 'female' : 'male';
    }


    /**
     * Assign role to user.
     *
     * @param User $user
     */
    private function assignUserRole(User $user): void
    {
        $user->assignRole('resident');
    }

    /**
     * Create archive record.
     *
     * @param UniversityArchiveLite $studentRecord
     * @return UniversityArchive
     */
    private function createArchiveRecord(UniversityArchiveLite $studentRecord): UniversityArchive
{
    return UniversityArchive::updateOrCreate(
        ['national_id' => $studentRecord->national_id], // Search by national_id
        [
            'name_en' => $studentRecord->name_en,
            'name_ar' => $studentRecord->name_ar,
            'academic_id' => $studentRecord->academic_id,
            'academic_email' => $studentRecord->academic_email,
        ]
    );
}

    /**
     * Link user to archive record.
     *
     * @param User $user
     * @param string $nationalId
     * @param UniversityArchive $archiveRecord
     */
    private function linkUserToArchive(User $user, string $nationalId, UniversityArchive $archiveRecord): void
    {
        UserNationalLink::create([
            'user_id' => $user->id,
            'national_id' => $nationalId,
            'university_archive_id' => $archiveRecord->id,
        ]);
    }

    /**
     * Send activation notification asynchronously.
     *
     * @param User $user
     */
    private function sendActivationNotification(User $user): void
    {
        SendMailJob::dispatch($user, new AccountActivation($user));
    }

    /**
     * Get both Arabic and English names from student record.
     *
     * @param UniversityArchiveLite $studentRecord
     * @return array
     */
    private function getNamesFromRecord(UniversityArchiveLite $studentRecord): array
    {
        return [
            'en' => $this->splitFullName($studentRecord->name_en),
            'ar' => $this->splitFullName($studentRecord->name_ar),
        ];
    }

    /**
     * Split a full name into first and last names.
     *
     * @param string $fullName
     * @return array
     */
    private function splitFullName(string $fullName): array
    {
        $fullName = trim($fullName);
        if (empty($fullName)) {
            return [
                'first_name' => '',
                'last_name' => '',
            ];
        }

        $names = explode(' ',$fullName);
        return [
            'first_name' => $names[0] ?? '',
            'last_name' => $names[count($names)-1] ?? '',
        ];
    }
}
