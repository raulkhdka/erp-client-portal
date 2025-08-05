<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EmployeeDocumentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
         {
             $user = Auth::user();
             $employee = $user->employee;

             if (!$employee) {
                 Log::error('Employee profile not found for user', ['user_id' => $user->id]);
                 return redirect()->route('employee.dashboard')->with('error', 'Employee profile not found.');
             }

             $assignedClientIds = $employee->accessibleClients()->pluck('clients.id');
             Log::info('Assigned clients', ['client_ids' => $assignedClientIds->toArray()]);

             $query = Document::with(['category', 'client', 'uploader', 'approver'])
                 ->notExpired()
                 ->whereIn('client_id', $assignedClientIds)
                 ->orderBy('created_at', 'desc');

             if ($request->filled('category_id')) {
                 $query->where('category_id', $request->category_id);
             }

             if ($request->filled('client_id')) {
                 if ($assignedClientIds->contains($request->client_id)) {
                     $query->where('client_id', $request->client_id);
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

             $documents = $query->paginate(10);
             $categories = DocumentCategory::active()
                 ->ordered()
                 ->withCount(['documents' => function ($query) use ($assignedClientIds) {
                     $query->whereIn('client_id', $assignedClientIds);
                 }])
                 ->get();
             $clients = Client::whereIn('id', $assignedClientIds)->get();

             return view('employees.documents.index', compact('documents', 'categories', 'clients'));
         }

         public function create(Request $request)
         {
             $user = Auth::user();
             $employee = $user->employee;

             if (!$employee) {
                 Log::error('Employee profile not found for user', ['user_id' => $user->id]);
                 return redirect()->route('employee.dashboard')->with('error', 'Employee profile not found.');
             }

             $categories = DocumentCategory::active()->ordered()->pluck('name', 'id');
             $clients = Client::whereIn('id', $employee->accessibleClients()->pluck('clients.id'))->pluck('company_name', 'id');
             $users = User::where('id', '!=', $user->id)->pluck('name', 'id');
             $selectedClientId = $request->get('client_id');

             return view('employees.documents.create', compact('categories', 'clients', 'users', 'selectedClientId'));
         }

         public function store(Request $request)
         {
             $user = Auth::user();
             $employee = $user->employee;

             if (!$employee) {
                 Log::error('Employee profile not found for user', ['user_id' => $user->id]);
                 return redirect()->route('employee.dashboard')->with('error', 'Employee profile not found.');
             }

             Log::info('Store request data:', $request->all());

             $assignedClientIds = $employee->accessibleClients()->pluck('clients.id');

             $validated = $request->validate([
                 'title' => 'required|string|max:255',
                 'description' => 'nullable|string|max:1000',
                 'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
                 'categories_id' => 'nullable|integer|exists:document_categories,id',
                 'client_id' => ['required', 'exists:clients,id', Rule::in($assignedClientIds)],
                 'is_public' => 'nullable|in:0,1,on',
                 'is_confidential' => 'nullable|in:0,1,on',
                 'expires_at' => 'nullable|date|after:today',
                //  'access_permissions' => 'nullable|array',
                //  'access_permissions.*' => 'exists:users,id',
             ]);

             $file = $request->file('document_file');
             $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
             $filePath = 'documents/' . $validated['client_id'] . '/' . $filename;

             try {
                 Storage::disk('public')->put($filePath, file_get_contents($file));

                 $document = Document::create([
                     'title' => $validated['title'],
                     'description' => $validated['description'] ?? '',
                     'file_name' => $file->getClientOriginalName(),
                     'file_path' => $filePath,
                     'file_type' => $file->getClientOriginalExtension(),
                     'file_size' => $file->getSize(),
                     'mime_type' => $file->getMimeType(),
                     'categories_id' => $validated['categories_id'] ?? null,
                     'client_id' => $validated['client_id'],
                     'employee_id' => $employee->id,
                     'uploaded_by' => $user->id,
                     'is_approved' => true, // Employee-uploaded documents bypass approval
                     'is_public' => $request->input('is_public', 0) == 1 || $request->input('is_public') === 'on',
                     'is_confidential' => $request->input('is_confidential', 0) == 1 || $request->input('is_confidential') === 'on',
                     'expires_at' => $validated['expires_at'],
                 ]);

                //  if (!empty($validated['access_permissions'])) {
                //      $document->users()->sync($validated['access_permissions']);
                //  }

                 return redirect()->route('employee.documents.index')->with('success', 'Document uploaded successfully!');
             } catch (\Exception $e) {
                 Log::error('Employee document upload failed: ' . $e->getMessage(), ['exception' => $e]);
                 if (Storage::disk('public')->exists($filePath)) {
                     Storage::disk('public')->delete($filePath);
                 }
                 return redirect()->back()->with('error', 'Failed to upload document. Please try again.')->withInput();
             }
         }

         public function show(Document $document)
         {
             $this->checkAccess($document);

             $document->load(['category', 'client', 'uploader', 'approver']);

             return view('employees.documents.show', compact('document'));
         }

         public function edit(Document $document)
         {
             $this->checkAccess($document, true);

             $employee = Auth::user()->employee;
             $categories = DocumentCategory::active()->ordered()->pluck('name', 'id');
             $clients = Client::whereIn('id', $employee->accessibleClients()->pluck('clients.id'))->pluck('company_name', 'id');

             return view('employees.documents.edit', compact('document', 'categories', 'clients'));
         }

         public function update(Request $request, Document $document)
         {
             $this->checkAccess($document, true);

             $employee = Auth::user()->employee;
             $assignedClientIds = $employee->accessibleClients()->pluck('clients.id');

             $validated = $request->validate([
                 'title' => 'required|string|max:255',
                 'description' => 'nullable|string|max:1000',
                 'document_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
                 'categories_id' => 'nullable|integer|exists:document_categories,id',
                 'client_id' => ['required', 'exists:clients,id', Rule::in($assignedClientIds)],
                 'is_public' => 'nullable|in:0,1,on',
                 'is_confidential' => 'nullable|in:0,1,on',
                 'expires_at' => 'nullable|date|after:today',
                //  'access_permissions' => 'nullable|array',
                //  'access_permissions.*' => 'exists:users,id',
             ]);

             $data = [
                 'title' => $validated['title'],
                 'description' => $validated['description'] ?? '',
                 'categories_id' => $validated['categories_id'] ?? null,
                 'client_id' => $validated['client_id'],
                 'is_public' => $request->input('is_public', 0) == 1 || $request->input('is_public') === 'on',
                 'is_confidential' => $request->input('is_confidential', 0) == 1 || $request->input('is_confidential') === 'on',
                 'expires_at' => $validated['expires_at'] ?? null,
                 'updated_by' => Auth::id(),
             ];

             if ($request->hasFile('document_file')) {
                 if (Storage::disk('public')->exists($document->file_path)) {
                     Storage::disk('public')->delete($document->file_path);
                 }

                 $file = $request->file('document_file');
                 $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                 $filePath = 'documents/' . $validated['client_id'] . '/' . $filename;

                 Storage::disk('public')->put($filePath, file_get_contents($file));

                 $data['file_name'] = $file->getClientOriginalName();
                 $data['file_path'] = $filePath;
                 $data['file_type'] = $file->getClientOriginalExtension();
                 $data['file_size'] = $file->getSize();
                 $data['mime_type'] = $file->getMimeType();
             }

             $document->update($data);

            //  if (!empty($validated['access_permissions'])) {
            //      $document->users()->sync($validated['access_permissions']);
            //  } else {
            //      $document->users()->detach();
            //  }

            return redirect()->route('employee.documents.index')->with('success', 'Document Updated successfully!'); //return response()->json(['message' => 'Document updated successfully!'], 200);
         }

         public function download(Document $document)
         {
             $this->checkAccess($document);

             if (!Storage::disk('public')->exists($document->file_path)) {
                 return back()->with('error', 'File not found.');
             }

             $document->incrementDownloadCount();

             $filePath = storage_path('app/public/' . $document->file_path);
             $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

             return response()->download($filePath, $document->title . '.' . $extension);
         }

         public function preview(Document $document)
         {
             $this->checkAccess($document);

             if (!Storage::disk('public')->exists($document->file_path)) {
                 return back()->with('error', 'Document file not found.');
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

             if (in_array($mimeType, $allowedPreviewMimeTypes) || Str::startsWith($mimeType, 'image/')) {
                 $fileContents = Storage::disk('public')->get($document->file_path);

                 return response($fileContents)
                     ->header('Content-Type', $mimeType)
                     ->header('Content-Disposition', 'inline; filename="' . $document->file_name . '"');
             }

             return back()->with('error', 'Preview is not available for this file type. Please download the document to view it.');
         }

         public function destroy(Document $document)
         {
             $this->checkAccess($document, true);

             if (Storage::disk('public')->exists($document->file_path)) {
                 Storage::disk('public')->delete($document->file_path);
             }

             $document->delete();

             return response()->json(['success' => true, 'message' => 'Document deleted successfully.']);//return redirect()->route('employee.documents.index')->with('success', 'Document deleted successfully.');
         }

         protected function checkAccess(Document $document, $allowUploader = false)
         {
             $user = Auth::user();
             $employee = $user->employee;

             if (!$employee) {
                 Log::error('Employee profile not found for user', ['user_id' => $user->id]);
                 abort(403, 'Employee profile not found.');
             }

             $assignedClientIds = $employee->accessibleClients()->pluck('clients.id');
             Log::info('CheckAccess clients', ['client_ids' => $assignedClientIds->toArray()]);

             if ($assignedClientIds->contains($document->client_id)) {
                 return true;
             }

             if ($allowUploader && $document->uploaded_by === $user->id) {
                 return true;
             }

             abort(403, 'Unauthorized access to this document.');
         }
}