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
use Exception;

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

            // Check if user has an incomplete profile
            if (!$user->profile_completed) {
                return $this->getIncompleteProfileData($user->id);
            }
            
            // Check if user has no profile
            if (!$user->student()->exists()) {
                return $this->getNoProfileData($user->id);
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
                'contactDetails' => $this->getContactDetails($userId),
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
     * Get data for no profile
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function getNoProfileData(int $userId): array
    {
        try {
            $archiveData = UniversityArchive::where('user_id', $userId)->first();
            
            if (!$archiveData) {
                return $this->getEmptyProfileData();
            }

            return $this->formatArchiveData($archiveData);
        } catch (Exception $e) {
            $this->logError('getNoProfileData', $e, ['user_id' => $userId]);
            throw $e;
        }
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
                'name_en' => $archiveData->name_en,
                'name_ar' => $archiveData->name_ar,
                'nationalId' => $archiveData->national_id,
                'birthDate' => $archiveData->birthDate,
                'gender' => $archiveData->gender,
            ],
            'contactDetails' => [
                'governorate' => $governorate,
                'city' => $city,
                'street' => $archiveData->street,
                'mobile' => $archiveData->mobile,
            ],
            'academicInformation' => [
                'faculty' => $faculty,
                'program' => $program,
                'universityId' => $archiveData->universityId,
                'universityEmail' => $archiveData->universityEmail,
                'gpa' => $archiveData->gpa,
            ],
            'parentInformation' => [
                'parentRelationship' => $archiveData->parentRelationship,
                'parentName' => $archiveData->parent_name,
                'parentPhone' => $archiveData->parent_mobile,
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
                'birthDate' => $user->student->birthdate,
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
    private function getContactDetails(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            $student = $user->student;

            return [
                'governorate' => $student?->governorate->id,
                'city' => $student?->city->id,
                'street' => $student?->street,
                'mobile' => $student?->mobile,
            ];
        } catch (Exception $e) {
            $this->logError('getContactDetails', $e, ['user_id' => $userId]);
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
                'universityId' => $student?->academic_id,
                'universityEmail' => $student?->academic_email,
                'gpa' => $student?->gpa,
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
                'parentMobile' => $parent->mobile,
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

            if (!$sibling) {
                return $this->getEmptySiblingData();
            }

            return [
                'siblingName' => $sibling->name,
                'faculty' => $sibling->faculty_id,
                'siblingNationalId' => $sibling->national_id,
                'siblingGender' => $sibling->gender,
                'siblingShareRoom' => $sibling->share_room,
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
     * Get empty profile data structure
     *
     * @return array
     */
    private function getEmptyProfileData(): array
    {
        return [
            'personalInformation' => [
                'name_en' => null,
                'name_ar' => null,
                'nationalId' => null,
                'birthDate' => null,
                'gender' => null,
            ],
            'contactDetails' => [
                'governorate' => null,
                'city' => null,
                'street' => null,
                'mobile' => null,
            ],
            'academicInformation' => [
                'faculty' => null,
                'program' => null,
                'universityId' => null,
                'universityEmail' => null,
                'gpa' => null,
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
            'parentMobile' => null,
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
            'siblingName' => null,
            'faculty' => null,
            'siblingNationalId' => null,
            'siblingGender' => null,
            'siblingShareRoom' => null,
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

   
}