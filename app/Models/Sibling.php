<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sibling extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id', 'gender', 'name', 'national_id', 'faculty_id'
    ];

    // Define the relationship with the User (assuming a user has one sibling)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with Faculty (if you want to access the Faculty model)
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}
