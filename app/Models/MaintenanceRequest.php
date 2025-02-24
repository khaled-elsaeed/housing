<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'category_id',
        'description',
        'status',
        'assigned_to',
        'assigned_at', 
        'staff_accepted_at',
        'rejected_at', 
        'reject_reason', 
        'completed_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime', // Added accepted_at to casts
        'rejected_at' => 'datetime', // Added rejected_at to casts
        'staff_accepted_at' => 'datetime', // Added rejected_at to casts

        'completed_at' => 'datetime',
    ];

    /**
     * Relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to the Room model.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relationship to the MaintenanceCategory model.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCategory::class, 'category_id');
    }

    /**
     * Relationship to the User model (assigned technician).
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }


    /**
     * Scope to filter by assigned technician.
     */
    public function scopeAssignedTo($query, $staffId)
    {
        return $query->where('assigned_to', $staffId);
    }

    /**
     * Check if the request is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the request is assigned.
     */
    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }

    /**
     * Check if the request is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Relationship to the Media model.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Relationship to the MaintenanceProblem model.
     */
    public function problems()
    {
        return $this->belongsToMany(MaintenanceProblem::class, 'maintenance_problem_request', 'request_id', 'problem_id');
    }
}