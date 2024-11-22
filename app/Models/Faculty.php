<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    public function students()
    {
        return $this->belongsToMany(Student::class); // Many-to-Many relationship via the reservations table
    }
}
