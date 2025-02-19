<?php

// app/Models/MaintenanceProblem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceProblem extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCategory::class, 'category_id');
    }
}