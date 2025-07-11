<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ClientService extends Model
{
    use HasFactory;

    protected $table = 'client_service_details';

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'service_links',
        'credentials',
        'amount',
        'cost_type',
        'next_due_date',
        'notes',
        'assigned_employee_id',
    ];

    protected $casts = [
        'service_links' => 'array',
        'credentials' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'next_due_date' => 'date',
    ];

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
}
