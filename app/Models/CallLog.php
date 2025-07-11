<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'employee_id',
        'caller_name',
        'caller_phone',
        'call_type',
        'subject',
        'description',
        'notes',
        'priority',
        'status',
        'call_date',
        'duration_minutes',
        'follow_up_required',
        'follow_up_date'
    ];

    protected $casts = [
        'call_date' => 'datetime',
        'follow_up_date' => 'datetime',
        'status' => 'integer',
        'duration_minutes' => 'integer'
    ];

    // Status constants
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
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function task()
    {
        return $this->hasOne(Task::class);
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

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
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
