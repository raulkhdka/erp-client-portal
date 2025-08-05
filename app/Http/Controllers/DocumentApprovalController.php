<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

class DocumentApprovalController extends Controller
{
    /**
     * Shows all pending documents to admin/assigned employee.
     */
    public function index(Request $request)
         {
             $user = Auth::user();
             Log::info('DocumentApprovalController::index', [
                 'user_id' => $user->id,
                 'is_employee' => $user->isEmployee(),
                 'employee_exists' => $user->employee ? true : false,
             ]);

             if (!$user->employee && $user->isEmployee()) {
                 Log::error('Employee profile not found for user', ['user_id' => $user->id]);
                 abort(403, 'Employee profile not found.');
             }

             $query = Document::where('is_approved', false)
                 ->whereNull('approved_by')
                 ->whereHas('uploader', function ($q) {
                     $q->where('role', 'client'); // Only client-uploaded documents
                 })
                 ->with(['client', 'uploader'])
                 ->latest();

             if ($user->isAdmin()) {
                 $view = 'admin.documents.approval.index';
                 $clients = Client::all();
             } elseif ($user->isEmployee()) {
                 $assignedClientIds = $user->employee->accessibleClients()->pluck('clients.id');
                 Log::info('Assigned clients', ['client_ids' => $assignedClientIds->toArray()]);
                 $query->whereIn('client_id', $assignedClientIds);
                 $view = 'employees.documents.document-approvals.index';
                 $clients = Client::whereIn('id', $assignedClientIds)->get();
             } else {
                 abort(403, 'Unauthorized access');
             }

             // Filter by client_id if provided
             if ($request->has('client_id') && $user->isEmployee()) {
                 $clientId = $request->input('client_id');
                 if ($user->employee->accessibleClients()->where('id', $clientId)->exists()) {
                     $query->where('client_id', $clientId);
                 } else {
                     abort(403, 'Unauthorized access to client documents');
                 }
             }

             // Filter by search term
             if ($request->filled('search')) {
                 $search = $request->search;
                 $query->where(function ($q) use ($search) {
                     $q->where('title', 'like', "%{$search}%")
                       ->orWhere('description', 'like', "%{$search}%")
                       ->orWhere('file_name', 'like', "%{$search}%");
                 });
             }

             $documents = $query->paginate(10);

             return view($view, compact('documents', 'clients'));
         }

         /**
          * Approve a document.
          */
         public function approve(Document $document)
         {
             $this->authorizeApproval($document);

             $document->update([
                 'is_approved' => true,
                 'approved_by' => Auth::id(),
                 'approved_at' => now(),
             ]);

             return redirect()->back()->with('success', 'Document approved successfully.');
         }

         /**
          * Reject a document.
          */
         public function reject(Document $document)
         {
             $this->authorizeApproval($document);

             $document->update([
                 'is_approved' => false,
                 'approved_by' => Auth::id(),
                 'approved_at' => now(),
             ]);

             return redirect()->back()->with('success', 'Document marked as rejected.');
         }

         /**
          * Authorization logic for approval/rejection.
          */
         protected function authorizeApproval(Document $document)
         {
             $user = Auth::user();

             if ($user->isAdmin()) {
                 return true;
             }

             if ($user->isEmployee() && $user->employee) {
                 $access = $user->employee->accessibleClients()
                     ->where('clients.id', $document->client_id)
                     ->wherePivot('is_active', true)
                     ->wherePivot('access_granted_date', '<=', now())
                     ->where(function ($query) {
                         $query->whereNull('access_expires_date')
                               ->orWhere('access_expires_date', '>=', now());
                     })
                     ->exists();
                 if ($access) {
                     return true;
                 }
             }

             abort(403, 'You are not authorized to approve/reject this document.');
         }
}