<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'permission_request_id', 
        'permission_type', 
        'description', 
        'start_date', 
        'end_date',
    ];

    /**
     * Get the permission request that owns the permission detail.
     */
    public function permissionRequest()
    {
        return $this->belongsTo(PermissionRequest::class);
    }
}
