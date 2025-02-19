<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'room_id',
        'category_id',
        'description',
        'problems',
        'priority',
        'status',
        'assigned_to',
        'completed_at'
    ];

    protected $casts = [
        'problems' => 'array',
        'completed_at' => 'datetime'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCategory::class, 'category_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $staffId)
    {
        return $query->where('assigned_to', $staffId);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }
}