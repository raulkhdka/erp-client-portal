<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'department',
        'position',
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
        return $this->belongsTo(User::class);
    }

    public function clientAccesses()
    {
        return $this->hasMany(ClientEmployeeAccess::class);
    }

    public function accessibleClients()
    {
        return $this->belongsToMany(Client::class, 'client_employee_accesses')
                    ->withPivot('permissions', 'access_granted_date', 'access_expires_date', 'is_active')
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
