<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'detail',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'type' => 'integer',
    ];

    // Relationships
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_services')
            ->using(\App\Models\ClientService::class) // custom pivot
            ->withPivot(['status', 'description', 'assigned_by', 'created_at'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
