<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationRequest extends Model
{
    protected $table = 'reservation_requests';
    protected $fillable = ['user_id','academic_term_id',
    'gender','period_type','period_duration',
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
