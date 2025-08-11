<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

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
