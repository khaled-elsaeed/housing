<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'room_id',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Get the student that owns the reservation.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the room that is reserved.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
