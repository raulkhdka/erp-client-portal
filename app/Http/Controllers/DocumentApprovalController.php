<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class DocumentApprovalController extends Controller
{
    // Shows all pending documents to admin/assigned employee.

    public function index()
    {
        $user = Auth::user();
        if($user->isAdmin()){
            // Fetch all pending documents for admin
            $documents = Document::where('is_approved', false)
                       ->latest()
                       ->paginate(10);
        } elseif ($user->isEmployee()) {
            // Get documents of assigned clients only
            $assignedClientIds = $user->assignedClients()->pluck('id');
            $documents = Document::where('is_approved', false)
                       ->whereIn('client_id', $assignedClientIds)
                       ->latest()
                       ->paginate(10);
        } else {
            abort(403, 'Unauthorized access');
        }

        return view('documents.approval.index', compact('documents'));
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

        if ($user->isEmployee() && $user->assignedClients()->where('id', $document->client_id)->exists()) {
            return true;
        }

        abort(403, 'You are not authorized to approve/reject this document.');
    }
}
