<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_request_id',
        'image_path',
    ];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class); // An image belongs to a maintenance request
    }
}
