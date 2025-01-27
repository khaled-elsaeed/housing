<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'year',
        'term',
        'status',
        'period_type'
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

    public function getFormattedStartDateAttribute()
    {
        if($this->period_type === 'long_term'){
            return $this->academicTerm->start_date ? Carbon::parse($this->academicTerm->start_date)->format('d M Y') : 'Not specified';
        } else {
            return $this->start_date ? Carbon::parse($this->start_date)->format('d M Y') : null;

        }
    }

    public function getFormattedEndDateAttribute()
    {
        if($this->period_type === 'long_term'){
            return $this->academicTerm->end_date ? Carbon::parse($this->academicTerm->end_date)->format('d M Y') : 'Not specified';
        } else {
            return $this->end_date ? Carbon::parse($this->end_date)->format('d M Y') : null;

        }    }

        public function getFullRoomDetailsAttribute()
{
    $details = [];

    if ($this->room) {
        $details[] = 'Room ' . $this->room->number;

        if ($this->room->apartment) {
            $details[] = 'Apartment ' . $this->room->apartment->number;

            if ($this->room->apartment->building) {
                $details[] = 'Building ' . $this->room->apartment->building->number;
            }
        }
    }

    return $details ? implode(', ', $details) : 'Not specified';
}
}
