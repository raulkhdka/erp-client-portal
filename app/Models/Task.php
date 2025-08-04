<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'call_log_id',
        'client_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'priority',
        'status',
        'completed_at',
        'due_date',
        'started_at',
        'notes',
        'attachments',
        'estimated_hours',
        'actual_hours'
    ];

    // protected $dates = [
    //     'due_date',
    //     'started_at',
    //     'completed_at'
    // ];

    protected $casts = [
        'attachments' => 'array',
        'status' => 'integer',
        'estimated_hours' => 'integer',
        'actual_hours' => 'integer',
        'due_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // Status constants (same as CallLog for consistency)
    const STATUS_PENDING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_ON_HOLD = 3;
    const STATUS_ESCALATED = 4;
    const STATUS_WAITING_CLIENT = 5;
    const STATUS_TESTING = 6;
    const STATUS_COMPLETED = 7;
    const STATUS_RESOLVED = 8;
    const STATUS_BACKLOG = 9;

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Relationships
    public function callLog()
    {
        return $this->belongsTo(CallLog::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function adminCreator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_RESOLVED]);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $employeeId)
    {
        return $query->where('assigned_to', $employeeId);
    }

    public function scopeStandalone($query)
    {
        return $query->whereNull('call_log_id');
    }

    public function scopeFromCallLog($query)
    {
        return $query->whereNotNull('call_log_id');
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', now()->toDateString());
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $statuses = [
            1 => 'Pending',
            2 => 'In Progress',
            3 => 'On Hold',
            4 => 'Escalated',
            5 => 'Waiting Client',
            6 => 'Testing',
            7 => 'Completed',
            8 => 'Resolved',
            9 => 'Backlog'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            1 => 'warning',   // Pending
            2 => 'primary',   // In Progress
            3 => 'secondary', // On Hold
            4 => 'danger',    // Escalated
            5 => 'info',      // Waiting Client
            6 => 'dark',      // Testing
            7 => 'success',   // Completed
            8 => 'success',   // Resolved
            9 => 'light'      // Backlog
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'dark'
        ];

        return $colors[$this->priority] ?? 'secondary';
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_RESOLVED]);
    }

    // Static methods
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_ESCALATED => 'Escalated',
            self::STATUS_WAITING_CLIENT => 'Waiting Client',
            self::STATUS_TESTING => 'Testing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_BACKLOG => 'Backlog'
        ];
    }

    public static function getPriorityOptions()
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent'
        ];
    }


}
