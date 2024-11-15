<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'number',
        'full_occupied',
        'max_occupancy',
        'current_occupancy',
        'status',
        'purpose',
        'type',  
        'description',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
