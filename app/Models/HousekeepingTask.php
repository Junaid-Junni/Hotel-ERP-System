<?php
// app/Models/HousekeepingTask.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousekeepingTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'assigned_to',
        'task_type',
        'description',
        'priority',
        'status',
        'scheduled_date',
        'started_at',
        'completed_at',
        'estimated_minutes',
        'actual_minutes',
        'notes',
        'cancellation_reason'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    // Accessor for task duration
    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        return null;
    }

    // Check if task is overdue
    public function getIsOverdueAttribute()
    {
        return $this->status === 'Pending' && $this->scheduled_date < now();
    }

    // Scope for active tasks
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Pending', 'In Progress']);
    }

    // Scope for today's tasks
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    // Generate task ID
    public static function generateTaskId()
    {
        $latest = self::latest()->first();
        $number = $latest ? intval(substr($latest->task_id, 3)) + 1 : 1;
        return 'TASK' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
