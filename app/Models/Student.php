<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name_en',
        'name_ar',
        'national_id',
        'academic_id',
        'phone',
        'birth_date',
        'gender',
        'governorate_id',
        'city_id',
        'street',
        'faculty_id',
        'program_id',
        'academic_id',
        'university_archive_id',
        'application_status',
    ];

    // Define a relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define a relationship with the UniversityArchive model
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

public function getNameAttribute()
{
    
    $lang = app()->getLocale();

    if ($lang == 'ar') {
        return $this->name_ar;
    }
    return $this->name_en;
}

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'reservations'); // Many-to-Many relationship via the reservations table
    }

    public function StudentPermissionRequests()
    {
        return $this->hasMany(StudentPermissionRequest::class, 'student_id');  // A student can have many requests
    }

}
