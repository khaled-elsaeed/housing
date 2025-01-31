<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'email',
        'first_name_en',
        'last_name_en',
        'first_name_ar',
        'last_name_ar',
        'password',
        'is_active',
        'media_id',
        'last_login',
        'is_verified',
        'activation_token',
        'activation_expires_at',
        'profile_completed',
        'status',
        'balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activation_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime', 
        'is_active' => 'boolean', 
        'is_verified' => 'boolean', 
        'profile_picture' => 'string', 
    ];

    // Relationships
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

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reservationRequests(){
        return $this->hasMany(reservationRequests::class);
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

    // User Status Methods
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

    // Lookup Methods
    public static function findUserByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    public static function getUserByNationalId(string $national_id): ?self
    {
        $userNationalLink = UserNationalLink::where('national_id', $national_id)->first();
        return $userNationalLink ? $userNationalLink->user : null;
    }

    // Student Status Methods
    public function isNewComerStudent(User $user): bool
    {
        return $user->student->universityArchive->is_new_comer ?? false;
    }

    public function isOldStudent(User $user): bool
    {
        return !$this->isNewComerStudent($user);
    }

    // Attribute Methods
    public function getUsername()
    {
        $lang = app()->getLocale();

        if ($lang == 'ar') {
            return $this->first_name_ar . ' ' . $this->last_name_ar;
        }

        return $this->first_name_en . ' ' . $this->last_name_en;


    }

    public function lastReservation($academicTermId)
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


public function media()
{
    return $this->hasOne(Media::class, 'id', 'media_id');
}

public function profilePicture()
{
    $media = $this->profilePictureMedia()->first();
    
    if ($media && $media->path) {
        return asset("storage/" . $media->path);
    }

    return asset('images/users/boy.svg');
}

public function hasProfilePicture()
{
    return (bool) $this->profilePictureMedia()->exists();
}

public function profilePictureMedia()
{
    return $this->media()->where('collection', 'profile_picture');
}


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
