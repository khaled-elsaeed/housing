<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'email',
        'first_name_en',
        'last_name_en',
        'first_name_ar',
        'last_name_ar',
        'password',
        'is_active',
        'media_id',
        'gender',
        'last_login',
        'is_verified',
        'activation_token',
        'activation_expires_at',
        'profile_completed',
        'status',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activation_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'profile_picture' => 'string',
    ];

    // ==================== Relationships ====================

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'resident_id');
    }

    public function userNationalLink()
    {
        return $this->hasOne(UserNationalLink::class);
    }

    public function universityArchive()
    {
        return $this->hasOneThrough(
            UniversityArchive::class,
            UserNationalLink::class,
            'user_id',
            'id',
            'id',
            'university_archive_id'
        );
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function insurance()
    {
        return $this->hasOne(Insurance::class);
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reservationRequests()
    {
        return $this->hasMany(ReservationRequest::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Reservation::class);
    }

    public function parent()
    {
        return $this->hasOne(Parents::class);
    }

    public function sibling()
    {
        return $this->hasOne(Sibling::class);
    }

    public function emergencyContact()
    {
        return $this->hasOne(EmergencyContact::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function profilePictureMedia()
    {
        return $this->media()->where('collection', 'profile_picture');
    }

    // ==================== User Status Methods ====================

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isResident(): bool
    {
        return $this->hasRole('resident');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    public function isDeleted(): bool
    {
        return !is_null($this->deleted_at);
    }

    public function isProfileComplete(): bool
    {
        return $this->profile_completed;
    }

    public function allowLateProfileCompletion(): bool
    {
        return $this->can_complete_late ?? false;
    }

    // ==================== Lookup Methods ====================

    public static function findUserByEmail(string $email): ?self
    {
        try {
            return self::where('email', $email)->first();
        } catch (\Exception $e) {
            logError('Failed to find user by email', 'find_user_by_email', $e);
            return null;
        }
    }

    public static function getUserByNationalId(string $national_id): ?self
    {
        try {
            $userNationalLink = UserNationalLink::where('national_id', $national_id)->first();
            return $userNationalLink ? $userNationalLink->user : null;
        } catch (\Exception $e) {
            logError('Failed to get user by national ID', 'get_user_by_national_id', $e);
            return null;
        }
    }

    // ==================== Student Status Methods ====================

    public function isNewComerStudent(User $user): bool
    {
        try {
            return $user->student->universityArchive->is_new_comer ?? false;
        } catch (\Exception $e) {
            logError('Failed to check if user is a new comer student', 'is_new_comer_student', $e);
            return false;
        }
    }

    public function isOldStudent(User $user): bool
    {
        return !$this->isNewComerStudent($user);
    }

    // ==================== Attribute Methods ====================

    public function getNameAttribute()
    {
        $lang = app()->getLocale();
        return $lang === 'ar'
            ? $this->first_name_ar . ' ' . $this->last_name_ar
            : $this->first_name_en . ' ' . $this->last_name_en;
    }

    public function getUsername()
    {
        return $this->getNameAttribute();
    }

    public function lastReservation($academicTermId = null)
    {
        try {
            if ($academicTermId && (!is_int($academicTermId) || $academicTermId <= 0)) {
                throw new \InvalidArgumentException('Academic term ID must be a positive integer or null.');
            }

            $query = $this->reservations()
                ->where('period_type', 'long')
                ->whereIn('status', ['completed', 'active']);

            if (!$academicTermId) {
                return $query->whereHas('academicTerm')
                    ->latest('created_at')
                    ->first();
            }

            $academicTerm = AcademicTerm::find($academicTermId);
            if (!$academicTerm) {
                throw new \Exception('Academic term not found.');
            }

            return $query->whereHas('academicTerm', function ($query) use ($academicTerm) {
                    $query->where('academic_year', $academicTerm->academic_year);
                    if ($academicTerm->start_date) {
                        $query->where('start_date', '<', $academicTerm->start_date);
                    }
                })
                ->latest('created_at')
                ->first();
        } catch (\Exception $e) {
            logError('Failed to get last reservation', 'get_last_reservation', $e);
            return null;
        }
    }

    public function activeReservation()
    {
        try {
            return $this->reservations()
                ->where('status', 'active')
                ->latest()
                ->first();
        } catch (\Exception $e) {
            logError('Failed to get active reservation', 'get_active_reservation', $e);
            return null;
        }
    }

    public function getRoommates()
    {
        try {
            $reservation = $this->activeReservation();

            if ($reservation && optional($reservation->room)->apartment) {
                $apartment = $reservation->room->apartment;
                $roomIds = $apartment->rooms->pluck('id');

                return Reservation::whereIn('room_id', $roomIds)
                    ->where('status', 'active')
                    ->where('user_id', '!=', $this->id)
                    ->with(['user', 'room'])
                    ->get();
            }

            return collect();
        } catch (\Exception $e) {
            logError('Failed to get roommates', 'get_roommates', $e);
            return collect();
        }
    }

    public function getEligibleSibling()
    {
        try {
            // Check if sibling details are present
            if (!$this->sibling || !$this->sibling->gender || !$this->sibling->national_id) {
                \Log::warning('Sibling data is missing or incomplete', [
                    'sibling' => $this->sibling
                ]);
                return null;
            }
    
            $siblingGender = $this->sibling->gender;
            $userGender = $this->student->gender ?? null;
    
            \Log::info('Sibling and student gender check', [
                'siblingGender' => $siblingGender,
                'userGender' => $userGender
            ]);
    
            // Check eligibility based on gender
            $isEligible = ($siblingGender === 'brother' && $userGender === 'male') ||
                          ($siblingGender === 'sister' && $userGender === 'female');
    
            if (!$isEligible) {
                \Log::info('Sibling is not eligible due to gender mismatch', [
                    'siblingGender' => $siblingGender,
                    'userGender' => $userGender
                ]);
                return null;
            }
    
            // Query for sibling user
            $siblingUser = User::whereHas('student', function ($query) {
                $query->where('national_id', $this->sibling->national_id);
            })->first();
    
            if (!$siblingUser) {
                \Log::info('No user found with given national_id', [
                    'national_id' => $this->sibling->national_id
                ]);
            } else {
                \Log::info('Eligible sibling found', [
                    'siblingUser' => $siblingUser->toArray()
                ]);
            }
    
            return $siblingUser ?: null;
    
        } catch (\Exception $e) {
            \Log::error('Failed to get eligible sibling', [
                'method' => 'getEligibleSibling',
                'exception' => $e->getMessage()
            ]);
            return null;
        }
    }
    

    // ==================== Media Methods ====================

    public function profilePicture()
    {
        try {
            $media = $this->profilePictureMedia()->first();

            if ($media && $media->path) {
                return asset($media->path);
            }

            return asset('images/users/boy.svg');
        } catch (\Exception $e) {
            logError('Failed to get profile picture', 'get_profile_picture', $e);
            return asset('images/users/boy.svg');
        }
    }

    public function hasProfilePicture()
    {
        try {
            return $this->profilePictureMedia()->exists();
        } catch (\Exception $e) {
            logError('Failed to check profile picture existence', 'has_profile_picture', $e);
            return false;
        }
    }

    // ==================== Location Methods ====================

    public function getLocationDetails()
    {
        try {
            $activeReservation = $this->reservations()
                ->where('status', 'active')
                ->first();

            if (!$activeReservation) {
                return ['error' => trans('No active reservation found.')];
            }

            $room = $activeReservation->room;
            return [
                'building' => $room->apartment->building->number,
                'apartment' => $room->apartment->number,
                'room' => $room->number,
            ];
        } catch (\Exception $e) {
            logError('Failed to get location details', 'get_location_details', $e);
            return ['error' => trans('Failed to retrieve location details.')];
        }
    }
}