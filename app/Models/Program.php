<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    public function students()
    {
        return $this->belongsToMany(Student::class); // Many-to-Many relationship via the reservations table
    }

    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
