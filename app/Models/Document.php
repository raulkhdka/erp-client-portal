<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'categories_id',
        'client_id',
        'employee_id',
        'uploaded_by',
        'is_approved', // Approval status
        'approved_by', // User who approved the document
        'access_permissions',
        'is_public',
        'is_confidential',
        'expires_at',
        'download_count',
        'last_accessed_at',
    ];

    protected $casts = [
        'access_permissions' => 'array',
        'is_public' => 'boolean',
        'is_confidential' => 'boolean',
        'is_approved' => 'boolean',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes >= 1024 && $i < 3; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute()
    {
        return route('documents.download', $this->id);
    }

    public function getPreviewUrlAttribute()
    {
        return route('documents.preview', $this->id);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeConfidential($query)
    {
        return $query->where('is_confidential', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeAccessibleBy($query, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return $query->where('id', null); // Return empty result
        }

        // Admin sees all documents
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function($q) use ($userId, $user) {
            // Public documents
            $q->where('is_public', true)
              // User's own documents
              ->orWhere('uploaded_by', $userId)
              // Documents with explicit access
              ->orWhereJsonContains('access_permissions', $userId);

            // For employees, add client-specific access
            if ($user->isEmployee()) {
                $clientIds = DB::table('client_employee_accesses')
                    ->where('employee_id', $user->id)
                    ->where('is_active', true)
                    ->pluck('client_id');

                    $q->orWhereIn('client_id', $clientIds);
                }

                if ($user->isClient()) {
                    $client = Client::where('user_id', $userId)->first();
                    if ($client) {
                        $q->orWhere('client_id', $client->id);
                    }
                }
            });
    }

    // Helpers
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        $this->update(['last_accessed_at' => now()]);
    }

    public function hasAccess($userId)
    {
        // Public documents are accessible to everyone
        if ($this->is_public) {
            return true;
        }

        // Document uploader always has access
        if ($this->uploaded_by == $userId) {
            return true;
        }

        // Check if user is admin
        $user = User::find($userId);
        if ($user && $user->role === 'admin') {
            return true;
        }

        // For confidential documents, check specific access rules
        if ($this->is_confidential) {
            return $this->hasConfidentialAccess($userId);
        }

        // Check access permissions array
        if ($this->access_permissions && in_array($userId, $this->access_permissions)) {
            return true;
        }

        return false;
    }

    public function hasConfidentialAccess($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        // Admin always has access
        if ($user->role === 'admin') {
            return true;
        }

        // If document belongs to a client
        if ($this->client_id) {
            // Client user has access to their own documents
            if ($this->client && $this->client->user_id === $userId) {
                return true;
            }

            // Check if employee is assigned to this client
            if ($user->role === 'employee') {
                $hasClientAccess = DB::table('client_employee_accesses')
                    ->where('client_id', $this->client_id)
                    ->where('employee_id', $user->id)
                    ->where('is_active', true)
                    ->exists();

                if ($hasClientAccess) {
                    return true;
                }
            }
        }

        return false;
    }

    //Override delete to remove files
    public function delete()
    {
        // Delete the file from storage
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        return parent::delete();
    }
}
