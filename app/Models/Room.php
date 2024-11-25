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
        $apartment = $this->apartment; // Assuming the Room model has an apartment relationship
        $building = $apartment ? $apartment->building : null; // Access the building through apartment
    
        return [
            'building' => $building ? $building->number : 'N/A', // Safely access building number
            'apartment' => $apartment ? $apartment->number : 'N/A', // Safely access apartment number
            'room' => $this->number ?? 'N/A', // Room number directly from the current model
        ];
    }
    

}
