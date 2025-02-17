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

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'room_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function getLocation()
    {
        $apartment = $this->apartment;
        $building = $apartment ? $apartment->building : null;

        return [
            'building' => optional($building)->number ?? trans("N/A"),
            'apartment' => optional($apartment)->number ?? trans("N/A"),
            'room' => $this->number ?? trans("N/A"),
        ];
    }
}


