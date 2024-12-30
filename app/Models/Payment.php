<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'reservation_id',
        'amount',
        'receipt_image',
        'status',
    ];
     
     public function reservation()
    {
        return $this->belongsTo(Reservation::class); 
    }
}
