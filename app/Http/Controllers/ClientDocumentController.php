<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClientDocument; // Make sure your Document model exists
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
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }

        // Fetch all documents belonging to this client, paginated
        // Ensure your Client model has a 'documents' relationship
        $documents = ClientDocument::where('client_id', $client->id)
                     ->with('uploadedBy')
                     ->latest()
                     ->paginate(10);

        return view('client.documents.index', compact('client', 'documents'));
    }

    /**
     * Display the form for uploading a new document.
     */
    public function create()
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }

        return view('client.documents.create', compact('client'));
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            return redirect()->route('login')->with('error', 'Client profile not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB
            'd_type' => 'nullable|string|max:255',
            'document_type' => 'nullable|string|max:50',
        ]);

        $client = Auth::user()->client;

        if (!$client) {
            return redirect()->back()->with('error', 'Client profile not found. Unable to upload document.');
        }

        $file = $request->file('document_file');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = 'client_documents/' . $client->id . '/' . $fileName;

        $fileSize = $file->getSize(); // Size in bytes
        $fileMimeType = $file->getMimeType();

        try {
            Storage::disk('public')->put($filePath, file_get_contents($file));

            // Save document details to the client_documents table
            ClientDocument::create([ // <--- CHANGED: Use ClientDocument
                'name' => $request->name,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'file_mime_type' => $fileMimeType,
                'document_type' => $request->document_type,
                'client_id' => $client->id,
                'uploaded_by_user_id' => Auth::id(),
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
    public function show(ClientDocument $document) // <--- CHANGED: Use ClientDocument for route model binding
    {
        // Ensure the authenticated client owns this document
        if (Auth::user()->client->id !== $document->client_id) {
            abort(403, 'Unauthorized access.');
        }

        return view('client.documents.show', compact('document'));
    }

    /**
     * Preview the specified document (if supported by browser/format).
     */
    public function download(ClientDocument $document) // <--- CHANGED: Use ClientDocument for route model binding
    {
        // Ensure the authenticated client owns this document
        if (Auth::user()->client->id !== $document->client_id) {
            abort(403, 'Unauthorized access.');
        }

        $filePath = storage_path('app/public/' . $document->file_path);

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $document->name . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));
    }
}