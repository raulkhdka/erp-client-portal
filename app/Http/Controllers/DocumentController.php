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
    public function index(Request $request)
    {
        $query = Document::with(['category', 'client', 'uploader'])
            ->notExpired()
            ->orderBy('created_at', 'desc');

        $user = Auth::user();

        if ($user->isClient()) {
            $client = $user->client;
            $query->where('client_id', $client->id);
        }

        // Filters (Admin/Employee can use these)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('access_level')) {
            if ($request->access_level == 'public') {
                $query->where('is_public', true);
            } elseif ($request->access_level == 'confidential') {
                $query->where('is_confidential', true);
            } elseif ($request->access_level == 'my_documents') {
                $query->where('uploaded_by', $user->id);
            }
        }

        if ($request->filled('approval_status')) {
            $query->where('is_approved', $request->approval_status == 'approved');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(20);
        $categories = DocumentCategory::active()->ordered()->get();
        $clients = ClientCacheService::getClientsCollection();

        return view('documents.index', compact('documents', 'categories', 'clients'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $categories = DocumentCategory::getSelectOptions();
        $clients = ClientCacheService::getClientsForSelect();
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->pluck('name', 'id');

        $selectedClientId = $request->get('client_id');

        return view('documents.create', compact('categories', 'clients', 'users', 'selectedClientId', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
            'categories_id' => 'nullable|exists:document_categories,id',
            'client_id' => 'nullable|exists:clients,id',
            'employee_id' => 'nullable|exists:users,id',
            'access_permissions' => 'nullable|array',
            'access_permissions.*' => 'exists:users,id',
            'is_public' => 'boolean',
            'is_confidential' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'public');

        $isClient = $user->isClient();

        $employeeId = null;
        $clientId = $request->client_id;
        $isApproved = !$isClient;

        if ($isClient) {
            $client = $user->client;
            $clientId = $client->id;

            $assignedEmployee = $client->assignedEmployees()->first();
            if ($assignedEmployee) {
                $employeeId = $assignedEmployee->id;
            }

            $isApproved = false; // Client upload needs approval
        }

        Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'categories_id' => $request->categories_id,
            'client_id' => $clientId,
            'employee_id' => $employeeId,
            'uploaded_by' => $user->id,
            'is_approved' => $isApproved,
            'access_permissions' => $request->access_permissions,
            'is_public' => $request->boolean('is_public'),
            'is_confidential' => $request->boolean('is_confidential'),
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully!');
    }

    public function show(Document $document)
    {
        $this->checkAccess($document);

        $document->load(['category', 'client', 'uploader']);

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $this->checkAccess($document, true);

        $categories = DocumentCategory::getSelectOptions();
        $clients = ClientCacheService::getClientsForSelect();
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->pluck('name', 'id');

        return view('documents.edit', compact('document', 'categories', 'clients', 'users'));
    }

    public function update(Request $request, Document $document)
    {
        $this->checkAccess($document, true);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categories_id' => 'nullable|exists:document_categories,id',
            'client_id' => 'nullable|exists:clients,id',
            'employee_id' => 'nullable|exists:users,id',
            'access_permissions' => 'nullable|array',
            'access_permissions.*' => 'exists:users,id',
            'is_public' => 'boolean',
            'is_confidential' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $data = $request->only([
            'title',
            'description',
            'categories_id',
            'client_id',
            'employee_id',
            'access_permissions',
            'is_public',
            'is_confidential',
            'expires_at',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');

            $data['file_name'] = $file->getClientOriginalName();
            $data['file_path'] = $path;
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }

        $document->update($data);

        return response()->json(['message' => 'Document updated successfully!'], 200);
    }

    public function download(Document $document)
    {
        $this->checkAccess($document);

        $document->incrementDownloadCount();

        return response()->download(Storage::disk('public')->path($document->file_path), $document->file_name);
    }

    public function preview(Document $document)
    {
        $this->checkAccess($document);

        // Ensure the file exists in storage
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'Document file not found.'); // Use back() for a user-friendly message
        }

        $mimeType = $document->mime_type;

        $allowedPreviewMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            // 'text/plain', // Be cautious with plain text, it might expose raw code etc.
        ];

        // Check if the mime type is explicitly allowed, or if it's a general image type
        if (in_array($mimeType, $allowedPreviewMimeTypes) || Str::startsWith($mimeType, 'image/')) {
            $path = Storage::disk('public')->path($document->file_path);

            return response()->file($path, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
            ]);
        }

        // If the file type is not supported for preview, redirect back with an error message
        return back()->with('error', 'Preview is not available for this file type. Please download the document to view it.');
    }

    public function destroy(Document $document)
    {
        $this->checkAccess($document, true);

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
    }

    // Approve/Reject (same as your previous logic)
    // public function approve(Document $document)
    // {
    //     $this->checkEmployeeOrAdmin();

    //     $document->update([
    //         'is_approved' => true,
    //         'approved_by' => Auth::id(),
    //     ]);

    //     return back()->with('success', 'Document approved.');
    // }

    // public function reject(Document $document)
    // {
    //     $this->checkEmployeeOrAdmin();

    //     $document->delete();

    //     return back()->with('success', 'Document rejected and deleted.');
    // }

    // Access check helper
    protected function checkAccess(Document $document, $allowUploader = false)
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        if ($user->isClient() && $document->client_id === $user->client->id) {
            return true;
        }

        if ($allowUploader && $document->uploaded_by === $user->id) {
            return true;
        }

        abort(403, 'Unauthorized access.');
    }

    protected function checkEmployeeOrAdmin()
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isEmployee()) {
            abort(403, 'Only admin or employee can perform this action.');
        }
    }
}
