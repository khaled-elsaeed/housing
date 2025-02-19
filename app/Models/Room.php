<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

      /**
     * Get all reservations for this room.
     *
     * @return HasMany
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the last active reservation for this room.
     *
     * @return HasOne
     */
    public function reservation(): HasOne
    {
        return $this->hasOne(Reservation::class)
            ->where('status', 'active') // Filter by active status
            ->latest(); // Order by the latest reservation
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


