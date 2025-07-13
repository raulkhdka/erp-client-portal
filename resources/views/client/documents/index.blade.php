@extends('layouts.app')

@section('title', 'My Documents')

{{-- Define the navbar-title section here to display "My Documents" in the top navigation bar --}}
@section('page-navbar-title')
    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>My Documents</h5>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Documents List</h5>
                    <a href="{{ route('client.documents.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Upload New Document
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Document Name</th>
                                        <th>Description</th>
                                        <th>Uploaded By</th>
                                        <th>Upload Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $document)
                                        <tr>
                                            <td>{{ $document->name }}</td>
                                            <td>{{ Str::limit($document->description, 50) ?? 'N/A' }}</td>
                                            <td>{{ $document->uploadedBy->name ?? 'N/A' }}</td>
                                            <td>{{ $document->created_at->format('M d, Y H:i A') }}</td>
                                            <td>
                                                <a href="{{ route('client.documents.show', $document->id) }}" class="btn btn-info btn-sm me-1" title="View Document">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                {{-- Assuming you have a download route --}}
                                                <a href="{{ route('client.documents.download', $document->id) }}" class="btn btn-success btn-sm" title="Download Document">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $documents->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">You haven't uploaded or received any documents yet.</h5>
                            <p class="text-muted">Click the "Upload New Document" button to add your first document.</p>
                            <a href="{{ route('client.documents.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-1"></i> Upload Document
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Any specific JavaScript for this page can go here
});
</script>
@endpush
@endsection