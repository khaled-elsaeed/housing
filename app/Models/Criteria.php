<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'criteria',
        'weight',
        'type',
    ];

    // Define the relationship to fields
    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
