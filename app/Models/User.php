<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    /**
     * Get the maintenance requests submitted by the user.
     */
    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'resident_id');
    }

    /**
     * Get the user's national ID link.
     */
    public function userNationalLink()
    {
        return $this->hasOne(UserNationalLink::class);
    }

    /**
     * Get the user's university archive through the national ID link.
     */
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

    /**
     * Get the student record associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the user's activities.
     */
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get the user's reservations.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the user's reservation requests.
     */
    public function reservationRequests()
    {
        return $this->hasMany(ReservationRequest::class);
    }

    /**
     * Get the user's invoices through reservations.
     */
    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Reservation::class);
    }

    /**
     * Get the parent record associated with the user.
     */
    public function parent()
    {
        return $this->hasOne(Parents::class);
    }

    /**
     * Get the sibling record associated with the user.
     */
    public function sibling()
    {
        return $this->hasOne(Sibling::class);
    }

    /**
     * Get the emergency contact associated with the user.
     */
    public function emergencyContact()
    {
        return $this->hasOne(EmergencyContact::class);
    }

    /**
     * Get all media associated with the user.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the user's profile picture media.
     */
    public function profilePictureMedia()
    {
        return $this->media()->where('collection', 'profile_picture');
    }

    // ==================== User Status Methods ====================

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is a resident.
     */
    public function isResident(): bool
    {
        return $this->hasRole('resident');
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the user is verified.
     */
    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    /**
     * Check if the user is deleted.
     */
    public function isDeleted(): bool
    {
        return !is_null($this->deleted_at);
    }

    /**
     * Check if the user's profile is complete.
     */
    public function isProfileComplete(): bool
    {
        return $this->profile_completed;
    }

    /**
     * Check if the user is allowed to complete their profile late.
     */
    public function allowLateProfileCompletion(): bool
    {
        return $this->can_complete_late ?? false;
    }

    // ==================== Lookup Methods ====================

    /**
     * Find a user by email.
     */
    public static function findUserByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    /**
     * Get a user by their national ID.
     */
    public static function getUserByNationalId(string $national_id): ?self
    {
        $userNationalLink = UserNationalLink::where('national_id', $national_id)->first();
        return $userNationalLink ? $userNationalLink->user : null;
    }

    // ==================== Student Status Methods ====================

    /**
     * Check if the user is a new comer student.
     */
    public function isNewComerStudent(User $user): bool
    {
        return $user->student->universityArchive->is_new_comer ?? false;
    }

    /**
     * Check if the user is an old student.
     */
    public function isOldStudent(User $user): bool
    {
        return !$this->isNewComerStudent($user);
    }

    // ==================== Attribute Methods ====================

    /**
     * Get the user's full name based on the application locale.
     */
    public function getNameAttribute()
    {
        $lang = app()->getLocale();

        if ($lang == 'ar') {
            return $this->first_name_ar . ' ' . $this->last_name_ar;
        }

        return $this->first_name_en . ' ' . $this->last_name_en;
    }

    /**
     * Get the user's username based on the application locale.
     */
    public function getUsername()
    {
        return $this->getNameAttribute();
    }

    /**
     * Get the user's last reservation.
     */
    public function lastReservation($academicTermId = null)
    {
        // Validate input
        if ($academicTermId && (!is_int($academicTermId) || $academicTermId <= 0)) {
            throw new \InvalidArgumentException('Academic term ID must be a positive integer or null.');
        }

        // Base query
        $query = $this->reservations()
            ->where('period_type', 'long')
            ->whereIn('status', ['completed', 'active']);

        // Handle null academic term ID
        if (!$academicTermId) {
            return $query->whereHas('academicTerm')
                ->latest('created_at')
                ->first();
        }

        // Fetch academic term
        $academicTerm = AcademicTerm::find($academicTermId);
        if (!$academicTerm) {
            throw new \Exception('Academic term not found.');
        }

        // Filter by academic term
        return $query->whereHas('academicTerm', function ($query) use ($academicTerm) {
                $query->where('academic_year', $academicTerm->academic_year);

                // Only filter by start_date if it is not NULL
                if ($academicTerm->start_date) {
                    $query->where('start_date', '<', $academicTerm->start_date);
                }
            })
            ->latest('created_at')
            ->first();
    }

    // ==================== Media Methods ====================

    /**
     * Get the user's profile picture URL.
     */
    public function profilePicture()
    {
        $media = $this->profilePictureMedia()->first();

        if ($media && $media->path) {
            return asset($media->path);
        }

        return asset('images/users/boy.svg');
    }

    /**
     * Check if the user has a profile picture.
     */
    public function hasProfilePicture()
    {
        return $this->profilePictureMedia()->exists();
    }

    // ==================== Location Methods ====================

    /**
     * Get the user's location details based on their active reservation.
     */
    public function getLocationDetails()
    {
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
    }
}