<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationRequest extends Model
{
    protected $table = 'reservation_requests';
    protected $fillable = ['user_id','academic_term_id','period_type','period_duration','old_room_id','stay_in_last_old_room','sibling_id','share_with_sibling',
    'start_date','end_date','status'];

    
    public function user()
    {
        return $this->belongsTo(user::class);
    }


    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }
}
