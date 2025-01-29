<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'admin_id',
        'user_id',
        'activity_type',
        'description',
    ];

    /**
     * Get the user associated with the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin associated with the activity.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}