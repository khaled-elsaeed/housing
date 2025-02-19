<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'staff_id',
        'assigned_at',
        'completed_at',
    ];

    // Relationship with MaintenanceRequest
    public function request()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'request_id');
    }

    // Relationship with Staff (User)
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
