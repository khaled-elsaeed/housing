<?php
// app/Models/MaintenanceCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceCategory extends Model
{
    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function problems(): HasMany
    {
        return $this->hasMany(MaintenanceProblem::class, 'category_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'category_id');
    }
}