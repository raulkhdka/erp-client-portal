<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Services\ClientCacheService;
use App\Helpers\NepaliDateHelper;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'company_name',
        'address',
        'tax_id',
        'business_license',
        'status',
        'notes'
    ];

    // Append these virtual attributes automatically when toArray() or JSON used
    protected $appends = [
        'created_at_nepali_html',
        'updated_at_nepali_html',
    ];

    protected $casts = [
        // services removed since it's now a relationship
    ];

    // Boot method to handle cache clearing
    protected static function boot()
    {
        parent::boot();

        // Clear cache when client is created, updated, or deleted
        static::saved(function ($client) {
            ClientCacheService::clearCache();
        });

        static::deleted(function ($client) {
            ClientCacheService::clearCache();
        });

    }

    // Accessors for Nepali HTML dates (do not store in DB)
    public function getCreatedAtNepaliHtmlAttribute()
    {
        if (!$this->created_at) {
            return 'N/A';
        }

        return NepaliDateHelper::auto_nepali_date($this->created_at->format('Y-m-d'), 'formatted');
    }

    public function getUpdatedAtNepaliHtmlAttribute()
    {
        if (!$this->updated_at) {
            return 'N/A';
        }

        return NepaliDateHelper::auto_nepali_date($this->updated_at->format('Y-m-d'), 'formatted');
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phones()
    {
        return $this->hasMany(ClientPhone::class);
    }

    public function emails()
    {
        return $this->hasMany(ClientEmail::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function newDocuments()
    {
        return $this->hasMany(Document::class, 'client_id')->where('is_approved', false);
    }


    // public function newDocuments()
    // {
    //     return $this->hasMany(Document::class);
    // }

    public function clientServices()
    {
        return $this->hasMany(ClientService::class);
    }

    public function images()
    {
        return $this->hasMany(ClientImage::class);
    }

    // public function employeeAccesses()
    // {
    //     return $this->hasMany(ClientEmployeeAccess::class);
    // }

    public function formResponses()
    {
        return $this->hasMany(\App\Models\DynamicFormResponse::class, 'client_id');
    }

    // public function accessibleEmployees()
    // {
    //     return $this->belongsToMany(Employee::class, 'client_employee_accesses')
    //                 ->using(ClientEmployeeAccess::class)
    //                 ->withPivot('permissions', 'access_granted_date', 'access_expires_date', 'is_active')
    //                 ->withTimestamps();
    // }

    public function assignedEmployees()
    {
        return $this->belongsToMany(Employee::class, 'client_employee_accesses')
            ->using(ClientEmployeeAccess::class)
            ->withPivot('permissions', 'access_granted_date', 'access_expires_date', 'is_active')
            ->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'client_services', 'client_id', 'service_id')
            ->withPivot('status', 'description', 'created_at')
            ->withTimestamps();
    }

    public function callLogs()
    {
        return $this->hasMany(CallLog::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function sharedForms()
    {
        return $this->belongsToMany(DynamicForm::class, 'dynamic_form_client', 'client_id', 'dynamic_form_id');
    }

    public function dynamicForms()
    {
        return $this->belongsToMany(DynamicForm::class, 'dynamic_form_client');
    }

    public static function convertAdToBs($adDate)
      {
          return NepaliDateHelper::auto_nepali_date($adDate, 'formatted');
      }
}
