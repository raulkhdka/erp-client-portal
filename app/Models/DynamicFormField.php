<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_form_id',
        'field_name',
        'field_label',
        'field_type',
        'field_options',
        'is_required',
        'sort_order',
        'validation_rules',
        'placeholder',
        'help_text'
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean',
        'validation_rules' => 'array',
    ];

    // Relationships
    public function dynamicForm()
    {
        return $this->belongsTo(DynamicForm::class);
    }
}
