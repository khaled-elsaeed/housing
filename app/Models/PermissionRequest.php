<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'additional_info',
    ];

    /**
     * Get the user that owns the permission request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the permission details associated with the permission request.
     */
    public function permissionDetails()
    {
        return $this->hasMany(PermissionDetail::class);
    }
}
