<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Helpers\NepaliDateHelper;

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
        'due_date',
        'started_at',
        'completed_at',
        'notes',
        'attachments',
        'estimated_hours',
        'actual_hours'
    ];

    protected $casts = [
        'due_date' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'attachments' => 'array',
        'status' => 'integer',
        'estimated_hours' => 'integer',
        'actual_hours' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'due_date_formatted',
        'due_date_nepali_html',
        'created_at_formatted',
        'created_at_nepali_html',
        'updated_at_formatted',
        'updated_at_nepali_html',
        'started_at_formatted',
        'started_at_nepali_html',
        'completed_at_formatted',
        'completed_at_nepali_html',
    ];

    const STATUS_PENDING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_ON_HOLD = 3;
    const STATUS_ESCALATED = 4;
    const STATUS_WAITING_CLIENT = 5;
    const STATUS_TESTING = 6;
    const STATUS_COMPLETED = 7;
    const STATUS_RESOLVED = 8;
    const STATUS_BACKLOG = 9;

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    public function callLog(): BelongsTo
    {
        return $this->belongsTo(CallLog::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function adminCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

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
        return $query->where('due_date', '<', now()->format('Ymd'))
                     ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_RESOLVED]);
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
        return $query->where('due_date', now()->format('Ymd'));
    }

    public function scopeDueSoon($query, $days = 3)
    {
        $start = now()->format('Ymd');
        $end = now()->addDays($days)->format('Ymd');
        return $query->whereBetween('due_date', [$start, $end]);
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
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

        return $statuses[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_ON_HOLD => 'secondary',
            self::STATUS_ESCALATED => 'danger',
            self::STATUS_WAITING_CLIENT => 'info',
            self::STATUS_TESTING => 'dark',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_RESOLVED => 'success',
            self::STATUS_BACKLOG => 'dark'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            self::PRIORITY_LOW => 'success',
            self::PRIORITY_MEDIUM => 'warning',
            self::PRIORITY_HIGH => 'danger',
            self::PRIORITY_URGENT => 'dark'
        ];

        return $colors[$this->priority] ?? 'secondary';
    }

    public function getIsOverdueAttribute()
    {
        if (!$this->due_date) {
            return false;
        }
        $dueDate = Carbon::createFromFormat('Ymd', $this->due_date);
        return $dueDate->isPast() && !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_RESOLVED]);
    }

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

    public function getDueDateFormattedAttribute(): string
    {
        if (!$this->due_date) {
            return 'N/A';
        }
        $dateStr = str_pad($this->due_date, 8, '0', STR_PAD_LEFT);
        return substr($dateStr, 0, 4) . '-' . substr($dateStr, 4, 2) . '-' . substr($dateStr, 6, 2);
    }

    public function getDueDateNepaliHtmlAttribute()
    {
        if (!$this->due_date) {
            return 'N/A';
        }
        $dateStr = $this->due_date_formatted;
        return NepaliDateHelper::auto_nepali_date($dateStr, 'formatted');
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : 'N/A';
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
        return $this->updated_at ? $this->updated_at->format('Y-m-d') : 'N/A';
    }

    public function getUpdatedAtNepaliHtmlAttribute()
    {
        if (!$this->updated_at) {
            return 'N/A';
        }
        return NepaliDateHelper::auto_nepali_date($this->updated_at->format('Y-m-d'), 'formatted');
    }

    public function getStartedAtFormattedAttribute()
    {
        return $this->started_at ? $this->started_at->format('Y-m-d') : 'N/A';
    }

    public function getStartedAtNepaliHtmlAttribute()
    {
        if (!$this->started_at) {
            return 'N/A';
        }
        return NepaliDateHelper::auto_nepali_date($this->started_at->format('Y-m-d'), 'formatted');
    }

    public function getCompletedAtFormattedAttribute()
    {
        return $this->completed_at ? $this->completed_at->format('Y-m-d') : 'N/A';
    }

    public function getCompletedAtNepaliHtmlAttribute()
    {
        if (!$this->completed_at) {
            return 'N/A';
        }
        return NepaliDateHelper::auto_nepali_date($this->completed_at->format('Y-m-d'), 'formatted');
    }
}