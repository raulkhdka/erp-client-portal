<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'email',
        'type',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
