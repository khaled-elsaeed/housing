<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

     protected $table = 'invoices';

     protected $fillable = [
        'reservation_id', 
        'amount', 
        'due_date', 
        'status', 
        'category'
    ];
     
     public function reservation()
    {
        return $this->belongsTo(Reservation::class); 
    }
}
