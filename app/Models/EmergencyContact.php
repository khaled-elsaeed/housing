<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id', 'name', 'phone','relation'
    ];

    // Define the relationship with the User (assuming a user has one emergency contact)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
