<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\NepaliDateHelper;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'employee_id',
        'department',
        'position',
        'phone',
        'hire_date',
        'salary',
        'status',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array',
        'hire_date' => 'integer',
        'salary' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

     // Append these accessors automatically in JSON
     protected $appends = [
        'hire_date_formatted',
        'hire_date_nepali_html',
        'created_at_formatted',
        'created_at_nepali_html',
        'updated_at_formatted',
        'updated_at_nepali_html'
    ];

    /**
     * Accessor: get hire_date formatted as YYYY-MM-DD string.
     * Converts the integer BS date to a date string.
     */
    public function getHireDateFormattedAttribute()
    {
        if (!$this->hire_date) {
            return null;
        }

        // Convert integer to string and pad zeros to 8 digits (YYYYMMDD)
        $hd = str_pad((string)$this->hire_date, 8, '0', STR_PAD_LEFT);

        // Validate length
        if (strlen($hd) !== 8) {
            return null;
        }

        // Format as YYYY-MM-DD
        return substr($hd, 0, 4) . '-' . substr($hd, 4, 2) . '-' . substr($hd, 6, 2);
    }

    /**
     * Accessor: get Nepali date HTML for hire_date (BS integer).
     * Uses your helper to generate formatted Nepali HTML.
     */
    public function getHireDateNepaliHtmlAttribute()
    {
        if (!$this->hire_date) {
            return 'N/A';
        }

        return NepaliDateHelper::auto_nepali_date_bs_integer($this->hire_date, 'formatted');
    }

    /**
     * Accessor: format created_at as Y-m-d string.
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }

    /**
     * Accessor: get Nepali date HTML for created_at.
     */
    public function getCreatedAtNepaliHtmlAttribute()
    {
        if (!$this->created_at) {
            return 'N/A';
        }

        return NepaliDateHelper::auto_nepali_date($this->created_at->format('Y-m-d'), 'formatted');
    }

    /**
     * Accessor: format updated_at as Y-m-d string.
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('Y-m-d') : null;
    }

    /**
     * Accessor: get Nepali date HTML for updated_at.
     */
    public function getUpdatedAtNepaliHtmlAttribute()
    {
        if (!$this->updated_at) {
            return 'N/A';
        }

        return NepaliDateHelper::auto_nepali_date($this->updated_at->format('Y-m-d'), 'formatted');
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function clientAccesses()
    // {
    //     return $this->hasMany(ClientEmployeeAccess::class);
    // }

    public function accessibleClients()
    {
        return $this->belongsToMany(Client::class, 'client_employee_accesses', 'employee_id', 'client_id')
            ->using(ClientEmployeeAccess::class)
            ->withPivot('permissions', 'access_granted_date', 'access_expires_date', 'is_active')
            ->wherePivot('is_active', true)
            ->wherePivot('access_granted_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('access_expires_date')
                    ->orWhere('access_expires_date', '>=', now());
            })
            ->withTimestamps();
    }

    public function callLogs()
    {
        return $this->hasMany(CallLog::class);
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }
}
