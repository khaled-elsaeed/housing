<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentRoomMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'reservation_id',
        'old_room_id',
        'new_room_id',
        'changed_by',
        'reason'
    ];

    /**
     * Get the resident associated with this movement.
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * Get the reservation associated with this movement.
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the old room before movement.
     */
    public function oldRoom()
    {
        return $this->belongsTo(Room::class, 'old_room_id');
    }

    /**
     * Get the new room after movement.
     */
    public function newRoom()
    {
        return $this->belongsTo(Room::class, 'new_room_id');
    }

    /**
     * Get the user who made the change.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
