<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    // Many-to-many relationship with students
    public function students()
    {
        return $this->belongsToMany(Student::class); // Many-to-Many relationship via the pivot table
    }

    // One-to-many relationship with programs
    public function programs()
    {
        return $this->hasMany(Program::class); // Corrected method to hasMany
    }
}
