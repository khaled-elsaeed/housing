<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

     protected $table = 'invoices';
     
     public function reservation()
    {
        return $this->belongsTo(reservation::class); // Assuming one user has one student record
    }
}
