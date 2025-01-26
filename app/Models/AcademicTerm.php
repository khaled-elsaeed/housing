<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{

    protected $table = 'academic_terms' ;

    protected $fillable = [
        'name', 
        'academic_year', 
        'start_date', 
        'end_date', 
        'description', 
        'status'
    ];

    protected $dates = [
        'start_date', 
        'end_date', 
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Scope for active terms
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for upcoming terms
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    // Check if term is current
    public function isCurrent()
    {
        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }

    public function reservation(){
        return $this->belongsTo(Reservation::class);
    }
}