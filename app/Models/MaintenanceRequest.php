<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

   // Table name (optional if it's the default 'maintenance_requests')
   protected $table = 'maintenance_requests';

   protected $fillable = [
    'user_id',
    'additional_info',
    'status',
];

public function user()
{
    return $this->belongsTo(User::class); // A request belongs to a user
}

public function issues()
{
    return $this->hasMany(MaintenanceIssue::class); // A request can have many issues
}

public function images()
{
    return $this->hasMany(MaintenanceImage::class); // A request can have many images
}
}
