<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientService extends Pivot
{
    use HasFactory;

    protected $table = 'client_services';

    protected $fillable = [
        'client_id',
        'service_id',
        'status',
        'description',
        'assigned_by',
    ];

    // protected $casts = [
    //     'service_links' => 'array',
    //     'credentials' => 'array',
    //     'start_date' => 'date',
    //     'end_date' => 'date',
    //     'amount' => 'decimal:2',
    //     'next_due_date' => 'date',
    // ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(User::class, 'assigned_employee_id');
    }

    // Accessors
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               (!$this->end_date || $this->end_date->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function daysRemaining(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        return now()->diffInDays($this->end_date, false);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                    ->where('end_date', '>', now());
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }


    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
