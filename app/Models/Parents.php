<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'relation',
        'email',
        'phone',
        'living_abroad',
        'abroad_country_id',
        'living_with',
        'governorate_id',
        'city_id',
    ];  

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function governorate()
{
    return $this->belongsTo(Governorate::class);
}

public function city()
{
    return $this->belongsTo(City::class);
}

}
