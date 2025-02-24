<?php

namespace App\Services;

use App\Models\User;
use App\Models\UniversityArchive;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Support\Facades\{Auth, DB};
use Exception;
use App\Exceptions\BusinessRuleException;
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

            if (!$user->student()->exists()) {
                return $this->getNoProfileData($user);
            }

            if (!$user->profile_completed) {
                return $this->getIncompleteProfileData($user->id);
            }

            // If profile is completed, return empty array or handle as needed
            return [];
        } catch (Exception $e) {
            logError('Failed to get user profile data', 'get_user_profile_data', $e);
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
                'emergencyContact' => $this->getEmergencyContact($userId),
            ];
        } catch (Exception $e) {
            logError('Failed to get incomplete profile data', 'get_incomplete_profile_data', $e);
            throw $e;
        }
    }

    /**
     * Get data for no profile
     *
     * @param User $user
     * @return array
     * @throws Exception
     */
    private function getNoProfileData($user): array
    {
        try {
            $archiveData = $user->universityArchive;

            if (!$archiveData) {
                logError('User has no archive data', 'get_no_profile_data', new Exception('No archive data found'));
                return $this->getEmptyProfileData();
            }

            return $this->formatArchiveData($archiveData);
        } catch (Exception $e) {
            logError('Failed to get no profile data', 'get_no_profile_data', $e);
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
            logError('Failed to get personal information', 'get_personal_information', $e);
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
            logError('Failed to get contact information', 'get_contact_information', $e);
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
            logError('Failed to get academic information', 'get_academic_information', $e);
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
            logError('Failed to get parent information', 'get_parent_information', $e);
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

            if (!$sibling) {
                return array_merge($this->getEmptySiblingData(), ['hasSiblingInDorm' => false]);
            }

            return [
                'hasSiblingInDorm' => true,
                'siblingGender' => $sibling->gender,
                'siblingFaculty' => $sibling->faculty_id,
                'siblingName' => $sibling->name,
                'siblingNationalId' => $sibling->national_id,
            ];
        } catch (Exception $e) {
            logError('Failed to get sibling information', 'get_sibling_information', $e);
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
            logError('Failed to get emergency contact information', 'get_emergency_contact', $e);
            throw $e;
        }
    }

    /**
     * Store or update the user's profile data.
     *
     * @param User $user
     * @param array $data
     * @throws Exception
     */
    public function storeProfileData(User $user, array $data)
    {
        try {
            DB::beginTransaction();

            // Prepare student data
            $studentData = [
                'name_en' => $data['nameEn'] ?? null,
                'name_ar' => $data['nameAr'] ?? null,
                'national_id' => $data['nationalId'] ?? null,
                'academic_id' => $data['academicId'] ?? null,
                'phone' => $data['phone'] ?? null,
                'birth_date' => $data['birthDate'] ?? null,
                'gender' => $data['gender'] ?? null,
                'governorate_id' => $data['governorate'] ?? null,
                'city_id' => $data['city'] ?? null,
                'street' => $data['street'] ?? null,
                'faculty_id' => $data['faculty'] ?? null,
                'program_id' => $data['program'] ?? null,
                'university_archive_id' => $user->universityArchive->id,
            ];

            if (!$user->student()->exists()) {
                $studentData['application_status'] = 'pending';
            }

            // Update or create student record
            $user->student()->updateOrCreate(['user_id' => $user->id], $studentData);

            // Prepare parent data
            $parentData = [
                'relation' => $data['parentRelationship'] ?? null,
                'name' => $data['parentName'] ?? null,
                'phone' => $data['parentPhone'] ?? null,
                'email' => $data['parentEmail'] ?? null,
                'living_abroad' => $data['isParentAbroad'] === '1',
                'abroad_country_id' => $data['isParentAbroad'] === '1' ? ($data['abroadCountry'] ?? null) : null,
                'living_with' => $data['isParentAbroad'] === '0' ? ($data['livingWithParent'] ?? null) : null,
                'governorate_id' => $data['isParentAbroad'] === '0' ? ($data['parentGovernorate'] ?? null) : null,
                'city_id' => $data['isParentAbroad'] === '0' ? ($data['parentCity'] ?? null) : null,
            ];

            $user->parent()->updateOrCreate(['user_id' => $user->id], $parentData);

            // Store emergency contact if parent is abroad
            if ($data['isParentAbroad'] === '1') {
                $emergencyContactData = [
                    'relation' => $data['emergencyContactRelationship'] ?? null,
                    'name' => $data['emergencyContactName'] ?? null,
                    'phone' => $data['emergencyContactPhone'] ?? null,
                ];
                $user->emergencyContact()->updateOrCreate(['user_id' => $user->id], $emergencyContactData);
            }

            // Store sibling data if applicable
            if (!empty($data['hasSiblingInDorm']) && $data['hasSiblingInDorm'] === '1') {
                $siblingData = [
                    'gender' => $data['siblingGender'] ?? null,
                    'name' => $data['siblingName'] ?? null,
                    'national_id' => $data['siblingNationalId'] ?? null,
                    'faculty_id' => $data['siblingFaculty'] ?? null,
                ];
                $user->sibling()->updateOrCreate(['user_id' => $user->id], $siblingData);
            }

            // Mark profile as completed and log activity
            $user->update([
                'profile_completed' => true,
                'profile_completed_at' => Carbon::now(),
            ]);

            userActivity($user->id, 'complete_profile', 'Completed profile information');

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            logError('Failed to store profile data', 'store_profile_data', $e);
            throw $e;
        }
    }
}