<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousekeepingTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'employee_id',
        'cleaning_date',
        'cleaning_time',
        'cleaning_type',
        'status',
        'tasks',
        'notes',
        'duration_minutes',
        'cleaning_supplies_cost',
        'issues_found',
        'special_instructions',
        'started_at',
        'completed_at',
        'quality_rating',
        'supervisor_notes'
    ];

    protected $casts = [
        'cleaning_date' => 'date',
        'tasks' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cleaning_supplies_cost' => 'decimal:2'
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Accessors
    public function getCleaningDateTimeAttribute()
    {
        return $this->cleaning_date->format('Y-m-d') . ' ' . $this->cleaning_time;
    }

    public function getFormattedCleaningDateAttribute()
    {
        return $this->cleaning_date->format('M d, Y');
    }

    public function getFormattedCleaningTimeAttribute()
    {
        return date('h:i A', strtotime($this->cleaning_time));
    }

    public function getDurationFormattedAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Scheduled' => 'primary',
            'In Progress' => 'warning',
            'Completed' => 'success',
            'Cancelled' => 'danger'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getCleaningTypeBadgeAttribute()
    {
        $badges = [
            'Daily' => 'info',
            'Checkout' => 'primary',
            'Deep' => 'warning',
            'Maintenance' => 'danger'
        ];

        return $badges[$this->cleaning_type] ?? 'secondary';
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'Scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'In Progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeToday($query)
    {
        return $query->where('cleaning_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('cleaning_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    // Methods
    public function markInProgress()
    {
        $this->update([
            'status' => 'In Progress',
            'started_at' => now()
        ]);
    }

    public function markCompleted($rating = null, $notes = null)
    {
        $this->update([
            'status' => 'Completed',
            'completed_at' => now(),
            'quality_rating' => $rating,
            'supervisor_notes' => $notes
        ]);

        // Update room status based on cleaning type
        if ($this->cleaning_type === 'Maintenance') {
            $this->room->update(['Status' => 'Available']);
        } else {
            $this->room->update(['Status' => 'Available']);
        }
    }

    public function isOverdue()
    {
        return $this->status === 'Scheduled' && $this->cleaning_date < today();
    }
}
