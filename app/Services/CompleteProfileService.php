<?php

namespace App\Services;

use App\Models\User;
use App\Models\UniversityArchive;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class CompleteProfileService
{
    /**
     * Get user profile data based on their profile status
     *
     * @return array
     * @throws Exception
     */
    public function getUserProfileData($user): array
    {
        try {          
            
            if (!$user) {
                throw new Exception('User not authenticated');
            }

            // Check if user has 0 profile
            if (!$user->student()->exists()) {
                return $this->getNoProfileData($user);
            }

            // Check if user has an incomplete profile
            if (!$user->profile_completed) {
                return $this->getIncompleteProfileData($user->id);
            }
            


            return [];
        } catch (Exception $e) {
            $this->logError('getUserProfileData', $e, ['user_id' => $user->id ?? null]);
            throw $e;
        }
    }

    /**
     * Get data for incomplete profile
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function getIncompleteProfileData(int $userId): array
    {
        try {
            return [
                'personalInformation' => $this->getPersonalInformation($userId),
                'contactInformation' => $this->getContactInformation($userId),
                'academicInformation' => $this->getAcademicInformation($userId),
                'parentInformation' => $this->getParentInformation($userId),
                'siblingInformation' => $this->getSiblingInformation($userId),
                'emergencyContact' => $this->getEmergencyContact($userId)
            ];
        } catch (Exception $e) {
            $this->logError('getIncompleteProfileData', $e, ['user_id' => $userId]);
            throw $e;
        }
    }

    /**
     * Get data for 0 profile
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function getNoProfileData($user): array
{
    try {
        \Log::info('Fetching no-profile data for user', [
            'user_id'  => $user->id ?? 'Guest',
            'email'    => $user->email ?? 'No Email',
            'name'     => $user->name ?? 'No Name',
        ]);

        $archiveData = $user->UniversityArchive;


        if (!$archiveData) {
            \Log::warning('User has no archive data', ['user_id' => $user->id]);
            return $this->getEmptyProfileData();
        }

        return $this->formatArchiveData($archiveData);
    } catch (\Exception $e) {
        \Log::error('Error in getNoProfileData', [
            'message'  => $e->getMessage(),
            'user_id'  => $user->id ?? 'Guest',
            'email'    => $user->email ?? 'No Email',
            'name'     => $user->name ?? 'No Name',
            'trace'    => $e->getTraceAsString(),
        ]);
        throw $e;
    }
}


    /**
     * Get empty profile data structure
     *
     * @return array
     */
    private function getEmptyProfileData(): array
    {
        return [
            'personalInformation' => [
                'nameEn' => null,
                'nameAr' => null,
                'nationalId' => null,
                'birthDate' => null,
                'gender' => null,
            ],
            'contactDetails' => [
                'governorate' => null,
                'city' => null,
                'street' => null,
                'phone' => null,
            ],
            'academicInformation' => [
                'faculty' => null,
                'program' => null,
                'academicId' => null,
                'academicEmail' => null,
                'score' => null,
            ],
            'parentInformation' => $this->getEmptyParentData(),
            'siblingInformation' => $this->getEmptySiblingData(),
            'emergencyContact' => $this->getEmptyEmergencyContactData(),
        ];
    }

    /**
     * Get empty parent data structure
     *
     * @return array
     */
    private function getEmptyParentData(): array
    {
        return [
            'parentRelationship' => null,
            'parentName' => null,
            'parentPhone' => null,
            'parentEmail' => null,
            'isParentAbroad' => null,
            'abroadCountry' => null,
            'livingWithParent' => null,
            'parentGovernorate' => null,
            'parentCity' => null,
        ];
    }

    /**
     * Get empty sibling data structure
     *
     * @return array
     */
    private function getEmptySiblingData(): array
    {
        return [
            'siblingGender' => null,
            'siblingName' => null,
            'siblingFaculty' => null,
            'siblingNationalId' => null,
            'siblingGender' => null,
        ];
    }

    /**
     * Get empty emergency contact data structure
     *
     * @return array
     */
    private function getEmptyEmergencyContactData(): array
    {
        return [
            'emergencyContactRelationship' => null,
            'emergencyContactName' => null,
            'emergencyContactPhone' => null,
        ];
    }

    /**
     * Format archive data into standardized structure
     *
     * @param UniversityArchive $archiveData
     * @return array
     */
    private function formatArchiveData(UniversityArchive $archiveData): array
    {
        $city = City::where('name_ar', $archiveData->city)->first();
        $governorate = Governorate::where('name_ar', $archiveData->governorate)->first();
        $faculty = Faculty::where('name_ar', $archiveData->faculty)->first();
        $program = Program::where('name_ar', $archiveData->program)->first();

        return [
            'personalInformation' => [
                'nameEn' => $archiveData->name_en,
                'nameAr' => $archiveData->name_ar,
                'nationalId' => $archiveData->national_id,
                'birthDate' => $archiveData->birthDate,
                'gender' => $archiveData->gender,
            ],
            'contactInformation' => [
                'governorate' => $governorate,
                'city' => $city,
                'street' => $archiveData->street,
                'phone' => $archiveData->phone,
            ],
            'academicInformation' => [
                'faculty' => $faculty,
                'program' => $program,
                'academicId' => $archiveData->academic_id,
                'academicEmail' => $archiveData->academic_email,
                'score' => $archiveData->score,
            ],
            'parentInformation' => [
                'parentRelationship' => $archiveData->parentRelationship,
                'parentName' => $archiveData->parent_name,
                'parentPhone' => $archiveData->parent_phone,
                'parentEmail' => $archiveData->parent_email,
                'isParentAbroad' => $archiveData->parent_is_abroad,
                'abroadCountry' => $archiveData->parent_abroad_country,
            ],
            'siblingInformation' => [
                'hasSibling' => $archiveData->has_sibling,
                'siblingName' => $archiveData->sibling_name,
                'siblingFaculty' => $archiveData->sibling_faculty,
            ],
            'emergencyContact' => $this->getEmptyEmergencyContactData(),
        ];
    }

    /**
     * Get personal information
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function getPersonalInformation(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            return [
                'nameEn' => $user->student->name_en,
                'nameAr' => $user->student->name_ar,
                'nationalId' => $user->student->national_id,
                'birthDate' => $user->student->birth_date,
                'gender' => $user->student->gender,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get contact details
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function getContactInformation(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            $student = $user->student;

            return [
                'governorate' => $student?->governorate->id,
                'city' => $student?->city->id,
                'street' => $student?->street,
                'phone' => $student?->phone,
            ];
        } catch (Exception $e) {
            $this->logError('getContactInformation', $e, ['user_id' => $userId]);
            throw $e;
        }
    }

    /**
     * Get academic information
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function getAcademicInformation(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            $student = $user->student;

            return [
                'faculty' => $student?->faculty->id,
                'program' => $student?->program->id,
                'level' => $student?->level,
                'academicId' => $student?->academic_id,
                'academicEmail' => $user->universityArchive?->academic_email,
                'score' => $user->universityArchive?->score,
            ];
        } catch (Exception $e) {
            $this->logError('getAcademicInformation', $e, ['user_id' => $userId]);
            throw $e;
        }
    }

    /**
     * Get parent information
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function getParentInformation(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            $parent = $user->parent;

            if (!$parent) {
                return $this->getEmptyParentData();
            }

            return [
                'parentRelationship' => $parent->relation,
                'parentName' => $parent->name,
                'parentPhone' => $parent->phone,
                'parentEmail' => $parent->email,
                'isParentAbroad' => $parent->living_abroad,
                'abroadCountry' => $parent->abroad_country_id,
                'livingWithParent' => $parent->living_with,
                'parentGovernorate' => $parent->governorate,
                'parentCity' => $parent->city,
            ];
        } catch (Exception $e) {
            $this->logError('getParentInformation', $e, ['user_id' => $userId]);
            throw $e;
        }
    }

    /**
     * Get sibling information
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function getSiblingInformation(int $userId): array
{
    try {
        $user = User::findOrFail($userId);
        $sibling = $user->sibling;

        // Check if sibling exists and return sibling data or empty data
        if (!$sibling) {
            return array_merge($this->getEmptySiblingData(), ['hasSiblingInDorm' => false]);
        }

        return [
            'hasSiblingInDorm' => true, 
            'siblingGender' => $sibling->gender,
            'siblingFaculty' => $sibling->faculty_id,
            'siblingName' => $sibling->name,
            'siblingNationalId' => $sibling->national_id,
            'siblingGender' => $sibling->gender,
        ];
        
    } catch (Exception $e) {
        $this->logError('getSiblingInformation', $e, ['user_id' => $userId]);
        throw $e;
    }
}


    /**
     * Get emergency contact information
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function getEmergencyContact(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            $emergency = $user->emergencyContact;

            if (!$emergency) {
                return $this->getEmptyEmergencyContactData();
            }

            return [
                'emergencyContactRelationship' => $emergency->relation,
                'emergencyContactName' => $emergency->name,
                'emergencyContactPhone' => $emergency->phone,
            ];
        } catch (Exception $e) {
            $this->logError('getEmergencyContact', $e, ['user_id' => $userId]);
            throw $e;
        }
    }



    /**
     * Log error with consistent format
     *
     * @param string $method
     * @param \Exception $exception
     * @param array $context
     * @return void
     */
    private function logError(string $method, \Exception $exception, array $context = []): void
    {
        Log::error(sprintf(
            'Error in %s::%s - %s',
            self::class,
            $method,
            $exception->getMessage()
        ), array_merge($context, [
            'trace' => $exception->getTraceAsString(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]));
    }


public function storeProfileData(User $user, array $data)
{
    try {
        DB::beginTransaction();

        // Log the incoming data for debugging
        Log::info('Storing profile data', [
            'user_id' => $user->id,
            'data'    => $data
        ]);

        // Check if student record exists
        $existingStudent = $user->student()->first();

        // Prepare student data
        $studentData = [
            'name_en'               => $data['nameEn'] ?? null,
            'name_ar'               => $data['nameAr'] ?? null,
            'national_id'           => $data['nationalId'] ?? null,
            'academic_id'           => $data['academicId'] ?? null,
            'phone'                => $data['phone'] ?? null,
            'birth_date'             => $data['birthDate'] ?? null,
            'gender'                => $data['gender'] ?? null,
            'governorate_id'        => $data['governorate'] ?? null,
            'city_id'               => $data['city'] ?? null,
            'street'                => $data['street'] ?? null,
            'faculty_id'            => $data['faculty'] ?? null,
            'program_id'            => $data['program'] ?? null,
            'university_archive_id' => $user->universityArchive->id,
        ];

        if (!$existingStudent) {
            $studentData['application_status'] = 'pending';
        }

        // Update or create student record
        $user->student()->updateOrCreate(
            ['user_id' => $user->id],
            $studentData
        );

        // Store parent information
        $parentData = [
            'relation'          => $data['parentRelationship'] ?? null,
            'name'              => $data['parentName'] ?? null,
            'phone'            => $data['parentPhone'] ?? null,
            'email'             => $data['parentEmail'] ?? null,
            'living_abroad'         => $data['isParentAbroad'] === '1',
            'abroad_country_id' => $data['isParentAbroad'] === '1' ? ($data['abroadCountry'] ?? null) : null,
            'living_with'        => $data['isParentAbroad'] === '0' ? ($data['livingWithParent'] ?? null) : null,
            'governorate_id'    => $data['isParentAbroad'] === '0' ? ($data['parentGovernorate'] ?? null) : null,
            'city_id'           => $data['isParentAbroad'] === '0' ? ($data['parentCity'] ?? null) : null,
        ];

        $livesWithValue = $data['isParentAbroad'] === '0' ? ($data['livingWithParent'] ?? null) : null;

        // Log the value
        $user->parent()->updateOrCreate(['user_id' => $user->id], $parentData);

        // Store emergency contact information if parent is abroad
        if ($data['isParentAbroad'] === '1') {
            $emergencyContactData = [
                'relation' => $data['emergencyContactRelationship'] ?? null,
                'name'     => $data['emergencyContactName'] ?? null,
                'phone'    => $data['emergencyContactPhone'] ?? null,
            ];
            $user->emergencyContact()->updateOrCreate(['user_id' => $user->id], $emergencyContactData);
        }

        // Store sibling information if applicable
        if (!empty($data['hasSiblingInDorm']) && $data['hasSiblingInDorm'] === '1') {
            $siblingData = [
                'gender' => $data['siblingGender'] ?? null,
                'name'         => $data['siblingName'] ?? null,
                'national_id'  => $data['siblingNationalId'] ?? null,
                'faculty_id'   => $data['siblingFaculty'] ?? null,
            ];
            $user->sibling()->updateOrCreate(['user_id' => $user->id], $siblingData);
        }

        // Mark profile as completed
        $user->update([
            'profile_completed'    => true,
            'profile_completed_at' => Carbon::now(),
        ]);

        DB::commit();

        Log::info('Profile data stored successfully', ['user_id' => $user->id,'data'=>$data]);

        return ['success' => true];
    } catch (\Exception $e) {
        DB::rollBack();
        
        // Log the error with additional context
        Log::error('Error in storeProfileData', [
            'method'  => 'storeProfileData',
            'user_id' => $user->id,
            'data'    => $data,
            'error'   => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return [
            'success' => false,
            'message' => __('Failed to store profile data. Please try again or contact support.')
        ];
    }
}

}