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
        'category',
        'media_id'
    ];
     
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
    
    /**
     * Get the details for the invoice.
     */
    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    } 

    /**
     * Get the payments for the invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function totalAmount()
    {
        return $this->details->sum('amount');
    }

    public function media()
    {
        return $this->hasOne(Media::class, 'id', 'media_id');  // Specify the correct foreign key
    }
    

}
