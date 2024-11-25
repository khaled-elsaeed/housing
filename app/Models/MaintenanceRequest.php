<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'room_id',
        'issue_type',
        'description',
        'status',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function images()
    {
        return $this->hasMany(MaintenanceRequestImage::class);
    }
}
