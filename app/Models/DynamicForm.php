<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    // Relationships
    public function fields()
    {
        return $this->hasMany(DynamicFormField::class)->orderBy('sort_order');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'dynamic_form_client');
    }


    public function responses()
    {
        return $this->hasMany(DynamicFormResponse::class);
    }

    public function sharedWithClients()
    {
        return $this->belongsToMany(Client::class, 'dynamic_form_client', 'dynamic_form_id', 'client_id');
    }
}
