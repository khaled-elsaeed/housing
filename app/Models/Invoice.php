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
        'reference_number',
        'due_date', 
        'paid_amount',
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
    
    /**
     * Calculate the total amount of the invoice.
     *
     * @return string
     */
    public function totalAmount()
    {
        $total = $this->details->sum('amount'); 
    
        $formattedTotal = number_format($total, 2);
    
        $currency = $this->currency ?? trans('EGP');
    
        return "{$formattedTotal} {$currency}";
    }

    public function academicTerm()
{
    return $this->reservation->academicTerm;
}

    public function media()
    {
        return $this->hasOne(Media::class, 'id', 'media_id');  
    }
    

}
