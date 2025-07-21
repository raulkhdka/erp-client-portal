<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document; // Make sure your Document model exists
use App\Models\DocumentCategory;
use App\Models\Client; // Make sure your Client model exists
use Illuminate\Support\Facades\Storage; // For file operations
use Illuminate\Support\Facades\Log;

class ClientDocumentController extends Controller
{
    /**
     * Display a listing of documents for the authenticated client.
     */
    public function index()
    {
        $client = Auth::user()->client;

        if(!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }

        // Fetch documents for the authenticated client
        $documents = Document::where('client_id', $client->id)
                   ->with('uploadedBy') // Eager load the user who uploaded the document
                   ->latest()
                   ->paginate(10);

        $categories = DocumentCategory::all(); // Loan categories
        $clients = Client::count();

        return view('documents.index', compact('documents', 'categories'));
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

            return redirect()->route('documents.index')->with('success', 'Document uploaded successfully!');

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
        $clientId = Auth::user()->client->id;

        if ($document->client_id !== $clientId) {
            abort(403, 'Unauthorized access.');
        }

        $document->load('uploadedBy');

        return view('documents.show', compact('document'));
    }

    /**
     * Preview the specified document (if supported by browser/format).
     */
    public function download(Document $document) // <--- CHANGED: Use ClientDocument for route model binding
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