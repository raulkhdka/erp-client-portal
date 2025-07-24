<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document; // Make sure your Document model exists
use App\Models\DocumentCategory;
use App\Models\Client; // Make sure your Client model exists
use Illuminate\Support\Facades\Storage; // For file operations
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientDocumentController extends Controller
{
    /**
     * Display a listing of documents for the authenticated client.
     */
    use AuthorizesRequests;
    public function index()
    {
        $client = Auth::user()->client;

        if (!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }

        // Fetch documents for the authenticated client
        $documents = Document::where('client_id', $client->id)
            ->with('uploader', 'approver') // Eager load the user who uploaded the document
            ->latest()
            ->paginate(10);

        $categories = DocumentCategory::all(); // Load categories
        //$clients = Client::count();

        return view('client.documents.index', compact('documents', 'categories'));
    }

    /**
     * Display the form for uploading a new document.
     */
    public function create()
    {
        $client = Auth::user()->client;

        if (!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }

        return view('documents.create', compact('client'));
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        $client = Auth::user()->client;

        if (!$client) {
            return redirect()->route('login')->with('error', 'Client profile not found.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB
            'description' => 'nullable|string|max:1000',
            'file_type' => 'nullable|string|max:50',
            'categories_id' => 'nullable|integer|exists:document_categories,id',
        ]);

        $file = $request->file('document_file');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = 'documents/' . $client->id . '/' . $fileName;

        $fileSize = $file->getSize(); // Size in bytes
        $fileMimeType = $file->getMimeType();

        try {
            Storage::disk('public')->put($filePath, file_get_contents($file));

            // Save document details to the client_documents table
            Document::create([
                'title' => $request->name,
                'description' => $request->description ?? '',
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'file_mime_type' => $fileMimeType,
                'file_type' => $request->file_type ?? 'general',
                'categories_id' => $request->categories_id ?? null,
                'client_id' => $client->id,
                'uploaded_by' => Auth::id(),
                'is_approved' => false, // Default to false, i.e. not approved
            ]);

            return redirect()->route('client.documents.index')->with('success', 'Document uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Client document upload failed: ' . $e->getMessage(), ['exception' => $e]);
            // If file was uploaded, attempt to delete it to prevent orphaned files
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return redirect()->back()->with('error', 'Failed to upload document. Please try again.')->withInput();
        }
    }

    /**
     * Download the specified document.
     */
    public function show(Document $document)
    {
        $user = Auth::user();

        if ($user->isClient() && optional($user->client)->id === $document->client_id) {
            return view('client.documents.show', compact('document'));
        }

        abort(403, 'Unauthorized to view this document.');
    }


    /**
     * Preview the specified document (if supported by browser/format).
     */

     public function preview(Document $document)
     {
        $clientId = Auth::user()->client->id; // Load client relationship

         if (!$clientId || $document->client_id !== $clientId) {
            abort(403, 'Unauthorized access to this document.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document file not found.');
        }

        $mimeType = $document->mime_type;

        $allowedPreviewMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'text/plain',
        ];

        if (in_array($mimeType, $allowedPreviewMimeTypes) || str_starts_with($mimeType, 'image/') || str_starts_with($mimeType, 'text/')) {
            $fileContents = Storage::disk('public')->get($document->file_path);

            return response($fileContents)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $document->file_name . '"');
        }

        return back()->with('error', 'Preview is not available for this file type. Please download the document to view it.');
    }

    public function download(Document $document)
    {
        $clientId = Auth::user()->client->id;
        // Ensure the authenticated client owns this document
        if ($document->client_id !== $clientId) {
            abort(403, 'Unauthorized access.');
        }

        $filePath = storage_path('app/public/' . $document->file_path);
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $document->name . '.' . $extension);
    }
}
