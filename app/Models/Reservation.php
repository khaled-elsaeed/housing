<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'year',
        'term',
        'status',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the student that owns the reservation.
     */
    public function user()
    {
        return $this->belongsTo(user::class);
    }
    

    /**
     * Get the room that is reserved.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }
}
