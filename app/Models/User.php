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
        'profile_picture',
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


    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'resident_id');
    }

    public function userNationalLink()
{
    return $this->hasOne(UserNationalLink::class);
}



// User model
public static function getUserByNationalId(string $national_id): ?self
{
    $userNationalLink = UserNationalLink::where('national_id', $national_id)->first();
    
    if ($userNationalLink) {
        return $userNationalLink->user; 
    }

    return null; 
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

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Reservation::class);
    }

   /**
 * Get the user's most recent completed or active long-term reservation for the same academic year as the provided academic term.
 *
 * @param int $academicTermId The ID of the academic term to filter reservations.
 * @return \App\Models\Reservation|null
 */
public function lastReservation($academicTermId)
{
    // Find the academic term by ID
    $academicTerm = AcademicTerm::find($academicTermId);

    if (!$academicTerm) {
        return null; // Return null if the academic term is not found
    }

    // Get all academic terms in the same academic year
    $academicYear = $academicTerm->academic_year;

    // Get the most recent reservation for the user in the same academic year
    return $this->reservations()
        ->whereHas('academicTerm', function ($query) use ($academicYear) {
            $query->where('academic_year', $academicYear);
        })
        ->whereIn('status', ['completed', 'active'])
        ->where('period_type', 'long')
        ->latest('created_at')
        ->first();
}

    public function parent(){
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

    public static function findUserByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    public function isNewComerStudent(User $user): bool
    {
        return $user->student->universityArchive->is_new_comer ?? false;
    }

    public function isOldStudent(User $user): bool
    {
        return !$this->isNewComerStudent($user);
    }

    public function getUsernameEnAttribute()
    {
        return $this->first_name_en . ' ' . $this->last_name_en;
    }

    public function getLocationDetails()
{
    // Retrieve the active reservation for the current object
    $activeReservation = $this->reservations()
        ->where('status', 'active')
        ->first();

    // Check if there is an active reservation
    if (!$activeReservation) {
        return [
            'error' => trans('No active reservation found.')
        ];
    }

    // Access room and related location details
    $room = $activeReservation->room;
    $roomNumber = $room->number;
    $apartmentNumber = $room->apartment->number;
    $buildingNumber = $room->apartment->building->number;

    // Return the structured location data
    return [
        'building' => $buildingNumber,
        'apartment' => $apartmentNumber,
        'room' => $roomNumber,
    ];
}


 
    
}
