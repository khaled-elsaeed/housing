<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_request_id',
        'issue_type',
        'description',
    ];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class); // An issue belongs to a maintenance request
    }
}
