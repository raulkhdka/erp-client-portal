@extends('layouts.app')

@section('title', 'View Document: ' . $document->name)

@section('page-navbar-title')
    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Document Details</h5>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Details for: {{ $document->name }}</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('documents.download', $document->id) }}" class="btn btn-success btn-sm" title="Download Document">
                            <i class="fas fa-download me-1"></i> Download
                        </a>

                        {{-- Show edit/delete only if user is logged in and NOT client --}}
                        @if(Auth::check() && !Auth::user()->isClient())
                            <a href="{{ route('documents.edit', $document->id) }}" class="btn btn-warning btn-sm" title="Edit Document">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>

                            <form action="{{ route('documents.destroy', $document->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Document">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        @endif

                        {{-- Show approve/reject only if user is logged in, not client, and document is not approved --}}
                        @if(Auth::check() && !Auth::user()->isClient() && !$document->is_approved)
                            <form action="{{ route('documents.approve', $document) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve Document">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>

                            <form action="{{ route('documents.reject', $document) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this document?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject Document">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Document Name:</div>
                        <div class="col-md-8">{{ $document->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Document Type:</div>
                        <div class="col-md-8">{{ $document->file_type ?? 'N/A' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Description:</div>
                        <div class="col-md-8">{{ $document->description ?? 'N/A' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">File Size:</div>
                        <div class="col-md-8">{{ number_format($document->file_size / 1024, 2) }} KB</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">MIME Type:</div>
                        <div class="col-md-8">{{ $document->mime_type }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Uploaded By:</div>
                        <div class="col-md-8">{{ $document->uploadedBy->name ?? 'N/A' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Upload Date:</div>
                        <div class="col-md-8">{{ $document->created_at->format('M d, Y H:i A') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Last Updated:</div>
                        <div class="col-md-8">{{ $document->updated_at->format('M d, Y H:i A') }}</div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('documents.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Additional JS if needed.
});
</script>
@endpush
