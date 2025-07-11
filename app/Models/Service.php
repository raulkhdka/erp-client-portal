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
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
