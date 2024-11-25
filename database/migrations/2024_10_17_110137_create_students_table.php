<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Define table name if it's different from default
    protected $table = 'students';

    // Define fillable attributes
    protected $fillable = [
        'user_id',
        'name_en',
        'name_ar',
        'national_id',
        'mobile',
        'birthdate',
        'gender',
        'governorate_id',
        'city_id',
        'street',
        'faculty_id',
        'program_id',
        'profile_completed',
        'profile_completed_at',
        'can_complete_late',
        'university_archive_id',
        'application_status',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function universityArchive()
    {
        return $this->belongsTo(UniversityArchive::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'reservations'); // Many-to-Many relationship via the reservations table
    }

    public function studentPermissionRequests()
    {
        return $this->hasMany(StudentPermissionRequest::class, 'student_id');  // A student can have many requests
    }
}
