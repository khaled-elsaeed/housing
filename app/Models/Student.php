<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

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
