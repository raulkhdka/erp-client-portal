<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\NepaliDateHelper;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Append these accessors automatically in JSON
    protected $appends =[
        'created_at_nepali_html',
        'updated_at_nepali_html',
    ];

     /**
     * Accessor: format created_at as Y-m-d string.
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }

    /**
     * Accessor: get Nepali date HTML for created_at.
     */
    public function getCreatedAtNepaliHtmlAttribute()
    {
        if (!$this->created_at) {
            return 'N/A';
        }

        return NepaliDateHelper::auto_nepali_date($this->created_at->format('Y-m-d'), 'formatted');
    }

    /**
     * Accessor: format updated_at as Y-m-d string.
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('Y-m-d') : null;
    }

    /**
     * Accessor: get Nepali date HTML for updated_at.
     */
    public function getUpdatedAtNepaliHtmlAttribute()
    {
        if (!$this->updated_at) {
            return 'N/A';
        }

        return NepaliDateHelper::auto_nepali_date($this->updated_at->format('Y-m-d'), 'formatted');
    }

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
