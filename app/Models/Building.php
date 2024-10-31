<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    
    protected $table = 'buildings'; 

    
    protected $fillable = [
        'number',
        'gender',
        'status',
        'description',
    ];

    
    public function apartment()
    {
        return $this->hasMany(Apartment::class);
    }
}
