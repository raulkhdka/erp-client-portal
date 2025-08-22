<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\NepaliDateHelper;
use Carbon\Carbon;

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
        'call_time',
        'duration_minutes',
        'follow_up_required',
        'follow_up_date',
        'follow_up_time',
    ];

    protected $casts = [
        'call_date' => 'integer',
        'follow_up_date' => 'integer',
        'call_time' => 'datetime:H:i',
        'follow_up_time' => 'datetime:H:i',
        'status' => 'integer',
        'duration_minutes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'call_date_formatted',
        'call_date_nepali_html',
        'call_time_formatted',
        'follow_up_date_formatted',
        'follow_up_date_nepali_html',
        'created_at_formatted',
        'created_at_nepali_html',
        'updated_at_formatted',
        'updated_at_nepali_html'
    ];

    /**
     * Accessor: get call_date formatted as YYYY-MM-DD string.
     */
    public function getCallDateFormattedAttribute(): string
    {
        if (!$this->call_date) {
            return 'N/A';
        }
        $dateStr = str_pad($this->call_date, 8, '0', STR_PAD_LEFT);
        return substr($dateStr, 0, 4) . '-' . substr($dateStr, 4, 2) . '-' . substr($dateStr, 6, 2);
    }

    /**
     * Accessor: get Nepali date HTML for call_date.
     */
    public function getCallDateNepaliHtmlAttribute()
    {
        if (!$this->call_date) {
            return 'N/A';
        }
        $dateStr = $this->call_date_formatted;
        return NepaliDateHelper::auto_nepali_date($dateStr, 'formatted');
    }

    /**
     * Accessor: get call_time formatted as h:i A (e.g., 01:15 PM).
     */
    public function getCallTimeFormattedAttribute()
    {
        if (!$this->call_time) {
            return 'N/A';
        }
        return Carbon::parse($this->call_time)->format('h:i A');
    }

    public function getFollowUpDateFormattedAttribute(): string
    {
        if (!$this->follow_up_date) {
            return 'N/A';
        }
        $dateStr = str_pad($this->follow_up_date, 8, '0', STR_PAD_LEFT);
        return substr($dateStr, 0, 4) . '-' . substr($dateStr, 4, 2) . '-' . substr($dateStr, 6, 2);
    }

    public function getFollowUpDateNepaliHtmlAttribute()
    {
        if (!$this->follow_up_date) {
            return 'N/A';
        }
        $dateStr = $this->follow_up_date_formatted;
        return NepaliDateHelper::auto_nepali_date($dateStr, 'formatted');
    }

    public function getCallDatetimeFormattedAttribute(): string
    {
        $date = $this->call_date_formatted;
        $time = $this->call_time ? $this->call_time_formatted : 'N/A';
        if ($date === 'N/A' && $time === 'N/A') {
            return 'N/A';
        }
        return $date . ' ' . $time;
    }

    public function getFollowUpDatetimeFormattedAttribute(): string
    {
        $date = $this->follow_up_date_formatted;
        $time = $this->follow_up_time ? Carbon::parse($this->follow_up_time)->format('h:i A') : 'N/A';
        if ($date === 'N/A' && $time === 'N/A') {
            return 'N/A';
        }
        return $date . ' ' . $time;
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }

    public function getCreatedAtNepaliHtmlAttribute()
    {
        if (!$this->created_at) {
            return 'N/A';
        }
        return NepaliDateHelper::auto_nepali_date($this->created_at->format('Y-m-d'), 'formatted');
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('Y-m-d') : null;
    }

    public function getUpdatedAtNepaliHtmlAttribute()
    {
        if (!$this->updated_at) {
            return 'N/A';
        }
        return NepaliDateHelper::auto_nepali_date($this->updated_at->format('Y-m-d'), 'formatted');
    }

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

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'call_log_id');
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
            1 => 'warning',
            2 => 'primary',
            3 => 'secondary',
            4 => 'danger',
            5 => 'info',
            6 => 'dark',
            7 => 'success',
            8 => 'success',
            9 => 'light'
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
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }
}
