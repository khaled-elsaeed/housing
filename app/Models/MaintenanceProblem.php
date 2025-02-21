<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceProblem extends Model
{
    protected $fillable = [
        'category_id',
        'name_en',
        'name_ar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relationship: A maintenance problem belongs to a category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCategory::class, 'category_id');
    }

    /**
     * Accessor: Get the name based on the current language.
     */
    public function getNameAttribute()
    {
        $locale = app()->getLocale(); // Get the current app language (en/ar)
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function requests()
    {
        return $this->belongsToMany(MaintenanceRequest::class, 'maintenance_problem_request', 'problem_id', 'request_id');
    }
}
