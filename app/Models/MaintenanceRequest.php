<?php

// app/Models/MaintenanceRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'title',
        'description',
        'status',
    ];

    // Relationship to Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relationship to Reservation through Room
    public function reservation()
    {
        return $this->hasOneThrough(Reservation::class, Room::class, 'id', 'room_id', 'room_id', 'id');
    }

    // Relationship to User through Reservation
    public function user()
    {
        return $this->hasOneThrough(User::class, Reservation::class, 'room_id', 'id', 'room_id', 'user_id');
    }
}
