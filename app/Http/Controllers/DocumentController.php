<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Document::with(['category', 'client', 'uploader'])
                        ->notExpired()
                        ->orderBy('created_at', 'desc');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->byClient($request->client_id);
        }

        // Filter by access level
        if ($request->filled('access_level')) {
            switch ($request->access_level) {
                case 'public':
                    $query->public();
                    break;
                case 'confidential':
                    $query->confidential();
                    break;
                case 'my_documents':
                    $query->where('uploaded_by', Auth::id());
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        // Apply access control
        $query->accessibleBy(Auth::id());

        $documents = $query->paginate(20);
        $categories = DocumentCategory::active()->ordered()->get();
        $clients = ClientCacheService::getClientsCollection();

        return view('documents.index', compact('documents', 'categories', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categories = DocumentCategory::getSelectOptions();
        $clients = ClientCacheService::getClientsForSelect();
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->pluck('name', 'id');
        $selectedClientId = $request->get('client_id');

        return view('documents.create', compact('categories', 'clients', 'users', 'selectedClientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
            'category_id' => 'nullable|exists:document_categories,id',
            'client_id' => 'nullable|exists:clients,id',
            'access_permissions' => 'nullable|array',
            'access_permissions.*' => 'exists:users,id',
            'is_public' => 'boolean',
            'is_confidential' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'public');

        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'category_id' => $request->category_id,
            'client_id' => $request->client_id,
            'uploaded_by' => Auth::id(),
            'access_permissions' => $request->access_permissions,
            'is_public' => $request->boolean('is_public'),
            'is_confidential' => $request->boolean('is_confidential'),
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('documents.index')
                        ->with('success', 'Document uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        // Check access permission
        if (!$document->hasAccess(Auth::id()) && !Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to view this document.');
        }

        $document->load(['category', 'client', 'uploader']);

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        // Check permission
        if ($document->uploaded_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to edit this document.');
        }

        $categories = DocumentCategory::getSelectOptions();
        $clients = ClientCacheService::getClientsForSelect();
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->pluck('name', 'id');

        return view('documents.edit', compact('document', 'categories', 'clients', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        // Check permission
        if ($document->uploaded_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to edit this document.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
            'category_id' => 'nullable|exists:document_categories,id',
            'client_id' => 'nullable|exists:clients,id',
            'access_permissions' => 'nullable|array',
            'access_permissions.*' => 'exists:users,id',
            'is_public' => 'boolean',
            'is_confidential' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'client_id' => $request->client_id,
            'access_permissions' => $request->access_permissions,
            'is_public' => $request->boolean('is_public'),
            'is_confidential' => $request->boolean('is_confidential'),
            'expires_at' => $request->expires_at,
        ];

        // Handle file replacement
        if ($request->hasFile('file')) {
            // Delete old file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Store new file
            $file = $request->file('file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');

            $updateData = array_merge($updateData, [
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        $document->update($updateData);

        return redirect()->route('documents.index')
                        ->with('success', 'Document updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        // Check permission
        if ($document->uploaded_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to delete this document.');
        }

        $document->delete();

        return redirect()->route('documents.index')
                        ->with('success', 'Document deleted successfully!');
    }

    /**
     * Download the document file
     */
    public function download(Document $document)
    {
        // Check access permission
        if (!$document->hasAccess(Auth::id()) && !Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to download this document.');
        }

        // Increment download count
        $document->incrementDownloadCount();

        // Return file download
        return response()->download(Storage::disk('public')->path($document->file_path), $document->file_name);
    }

    /**
     * Preview the document (for supported file types)
     */
    public function preview(Document $document)
    {
        // Check access permission
        if (!$document->hasAccess(Auth::id()) && !Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to preview this document.');
        }

        $supportedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'txt'];

        if (!in_array(strtolower($document->file_type), $supportedTypes)) {
            return redirect()->route('documents.download', $document);
        }

        // Update last accessed time
        $document->update(['last_accessed_at' => now()]);

        return response()->file(Storage::disk('public')->path($document->file_path));
    }

    /**
     * Manage access permissions for a document
     */
    public function manageAccess(Document $document)
    {
        // Check permission
        if ($document->uploaded_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to manage access for this document.');
        }

        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get();
        $currentPermissions = $document->access_permissions ?: [];

        return view('documents.manage-access', compact('document', 'users', 'currentPermissions'));
    }

    /**
     * Update access permissions
     */
    public function updateAccess(Request $request, Document $document)
    {
        // Check permission
        if ($document->uploaded_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You do not have permission to manage access for this document.');
        }

        $request->validate([
            'access_permissions' => 'nullable|array',
            'access_permissions.*' => 'exists:users,id',
            'is_public' => 'boolean',
        ]);

        $document->update([
            'access_permissions' => $request->access_permissions,
            'is_public' => $request->boolean('is_public'),
        ]);

        return redirect()->route('documents.show', $document)
                        ->with('success', 'Access permissions updated successfully!');
    }
}
