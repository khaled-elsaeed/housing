<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNationalLink extends Model
{
    use HasFactory;

    protected $table = 'user_national_link'; 

    protected $fillable = [
        'user_id',
        'national_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
