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
        'user_id',                 // Foreign key on UserNationalLink table
        'id',                      // Foreign key on UniversityArchives table
        'id',                      // Local key on Users table
        'university_archive_id'    // Local key on UserNationalLink table
    );
}


   public function student()
    {
        return $this->hasOne(Student::class); // Assuming one user has one student record
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class); // Assuming one user has one student record
    }

    public function parent(){
        return $this->hasOne(Parents::class);
    }

    // User Model

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

    // Define an accessor for 'username_en' (concatenation of first_name_en and last_name_en)
    public function getUsernameEnAttribute()
    {
        return $this->first_name_en . ' ' . $this->last_name_en;
    }

    public function getLocationDetails()
    {
        // Access the related room for this reservation
        $room = $this->reservation->room;
    
        // Retrieve the room number, apartment number, and building number
        $roomNumber = $room->number;
        $apartmentNumber = $room->apartment->number;
        $buildingNumber = $room->apartment->building->number;
    
        // Return each location detail separately
        return [
            'building' => $buildingNumber,
            'apartment' => $apartmentNumber,
            'room' => $roomNumber
        ];
    }

 
    
}
