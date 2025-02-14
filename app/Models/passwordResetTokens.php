<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class passwordResetTokens extends Model
{
    use HasFactory;



    protected $fillable = [
        'email',
        'token',
        'token_expires_at'
    ];

    protected $casts = [
        'token_expires_at' => 'datetime', 
    ];


}
