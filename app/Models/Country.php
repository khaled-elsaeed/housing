<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional, Laravel assumes it’s the plural form of the model name)
    protected $table = 'countries';

    // Define which fields are fillable
    protected $fillable = ['name', 'code'];

    // Optional: You can add relationships if you have any (e.g., related cities)
}
