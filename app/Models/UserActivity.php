<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'admin_id',
        'user_id', 
        'activity_type', 
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Method to log a new activity
    public static function logActivity($adminId,$userId, $activity_type, $description)
    {
        return self::create([
            'admin_id' => $adminId,
            'user_id' => $userId,
            'activity_type' => $activity_type,
            'description' => $description
        ]);
    }

    // Scope to get recent activities
    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')
                     ->limit($limit);
    }
}
