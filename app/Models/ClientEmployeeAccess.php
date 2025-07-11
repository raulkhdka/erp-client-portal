<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEmployeeAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'employee_id',
        'permissions',
        'access_granted_date',
        'access_expires_date',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'access_granted_date' => 'date',
        'access_expires_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
