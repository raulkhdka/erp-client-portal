<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicFormResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_form_id',
        'client_id',
        'response_data',
        'submitted_at'
    ];

    protected $casts = [
        'response_data' => 'array',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function dynamicForm()
    {
        return $this->belongsTo(DynamicForm::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
