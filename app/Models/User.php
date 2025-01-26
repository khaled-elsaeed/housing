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

    /**
     * Get the user's most recent completed or active long-term reservation for a specific academic year.
     *
     * @param int $academicYearId The ID of the academic year to filter reservations.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lastReservation($academicYearId)
    {
        return $this->hasOne(Reservation::class)
            ->where('academic_term_id', $academicYearId)
            ->whereIn('status', ['completed', 'active'])
            ->where('period_type', 'long_term')
            ->latest('created_at')
            ->first(); // Resolve and return the actual model
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
        $room = $this->reservations->room;
    
        $roomNumber = $room->number;
        $apartmentNumber = $room->apartment->number;
        $buildingNumber = $room->apartment->building->number;
    
        return [
            'building' => $buildingNumber,
            'apartment' => $apartmentNumber,
            'room' => $roomNumber
        ];
    }

 
    
}
