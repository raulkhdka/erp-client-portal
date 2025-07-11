@extends('layouts.app')

@section('title', $document->title)

@push('styles')
<style>
    .document-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .action-card {
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-radius: 10px;
        transition: transform 0.3s ease;
    }
    .action-card:hover {
        transform: translateY(-5px);
    }
    .preview-container {
        max-height: 600px;
        overflow: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Document Header -->
    <div class="document-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    @php
                        $iconClass = match(strtolower($document->file_type)) {
                            'pdf' => 'fas fa-file-pdf',
                            'doc', 'docx' => 'fas fa-file-word',
                            'xls', 'xlsx' => 'fas fa-file-excel',
                            'ppt', 'pptx' => 'fas fa-file-powerpoint',
                            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image',
                            'zip', 'rar' => 'fas fa-file-archive',
                            default => 'fas fa-file'
                        };
                    @endphp
                    <i class="{{ $iconClass }} fa-3x me-3"></i>
                    <div>
                        <h2 class="mb-1">{{ $document->title }}</h2>
                        <p class="mb-0 opacity-75">{{ $document->file_name }}</p>
                    </div>
                </div>

                <!-- Badges -->
                <div class="mb-3">
                    @if($document->category)
                        <span class="badge bg-light text-dark me-2">
                            <i class="{{ $document->category->icon }}"></i>
                            {{ $document->category->name }}
                        </span>
                    @endif
                    @if($document->is_public)
                        <span class="badge bg-success me-2">Public</span>
                    @endif
                    @if($document->is_confidential)
                        <span class="badge bg-danger me-2">Confidential</span>
                    @endif
                    @if($document->expires_at)
                        <span class="badge bg-warning text-dark me-2">
                            Expires {{ $document->expires_at->format('M d, Y') }}
                        </span>
                    @endif
                </div>

                <!-- Stats -->
                <div class="row text-center">
                    <div class="col-3">
                        <div class="opacity-75">Size</div>
                        <strong>{{ $document->formatted_file_size }}</strong>
                    </div>
                    <div class="col-3">
                        <div class="opacity-75">Downloads</div>
                        <strong>{{ $document->download_count }}</strong>
                    </div>
                    <div class="col-3">
                        <div class="opacity-75">Uploaded</div>
                        <strong>{{ $document->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div class="col-3">
                        <div class="opacity-75">Last Access</div>
                        <strong>{{ $document->last_accessed_at ? $document->last_accessed_at->diffForHumans() : 'Never' }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group-vertical" role="group">
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-light btn-lg mb-2">
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                    @if(in_array(strtolower($document->file_type), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'txt']))
                        <a href="{{ route('documents.preview', $document) }}" class="btn btn-outline-light mb-2" target="_blank">
                            <i class="fas fa-eye me-2"></i>Preview
                        </a>
                    @endif
                    @if($document->uploaded_by === Auth::id() || Auth::user()->isAdmin())
                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-light mb-2">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        <a href="{{ route('documents.manage-access', $document) }}" class="btn btn-outline-light">
                            <i class="fas fa-users-cog me-2"></i>Manage Access
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Document Details -->
        <div class="col-md-8">
            <!-- Description -->
            @if($document->description)
                <div class="card action-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Description</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $document->description }}</p>
                    </div>
                </div>
            @endif

            <!-- Preview (if supported) -->
            @if(in_array(strtolower($document->file_type), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']))
                <div class="card action-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="preview-container">
                            @if(strtolower($document->file_type) === 'pdf')
                                <iframe src="{{ route('documents.preview', $document) }}" width="100%" height="600px"></iframe>
                            @elseif(in_array(strtolower($document->file_type), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ route('documents.preview', $document) }}" class="img-fluid" alt="{{ $document->title }}">
                            @elseif(strtolower($document->file_type) === 'txt')
                                <div class="p-3">
                                    <pre>{{ Storage::disk('public')->get($document->file_path) }}</pre>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Access Permissions -->
            @if($document->uploaded_by === Auth::id() || Auth::user()->isAdmin())
                <div class="card action-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Access Permissions</h5>
                    </div>
                    <div class="card-body">
                        @if($document->is_public)
                            <div class="alert alert-success">
                                <i class="fas fa-globe me-2"></i>This document is public and can be accessed by all users.
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-lock me-2"></i>This document has restricted access.
                            </div>
                            @if($document->access_permissions && count($document->access_permissions) > 0)
                                <h6>Users with Access:</h6>
                                <ul class="list-unstyled">
                                    @foreach(App\Models\User::whereIn('id', $document->access_permissions)->get() as $user)
                                        <li><i class="fas fa-user me-2"></i>{{ $user->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">Only you and administrators can access this document.</p>
                            @endif
                        @endif

                        <a href="{{ route('documents.manage-access', $document) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-users-cog me-2"></i>Manage Access
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Document Info -->
            <div class="card action-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Document Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">File Type:</dt>
                        <dd class="col-sm-7">{{ strtoupper($document->file_type) }}</dd>

                        <dt class="col-sm-5">File Size:</dt>
                        <dd class="col-sm-7">{{ $document->formatted_file_size }}</dd>

                        <dt class="col-sm-5">MIME Type:</dt>
                        <dd class="col-sm-7">{{ $document->mime_type }}</dd>

                        <dt class="col-sm-5">Uploaded By:</dt>
                        <dd class="col-sm-7">{{ $document->uploader->name }}</dd>

                        <dt class="col-sm-5">Upload Date:</dt>
                        <dd class="col-sm-7">{{ $document->created_at->format('M d, Y H:i') }}</dd>

                        @if($document->client)
                            <dt class="col-sm-5">Client:</dt>
                            <dd class="col-sm-7">
                                <a href="{{ route('clients.show', $document->client) }}">
                                    {{ $document->client->company_name }}
                                </a>
                            </dd>
                        @endif

                        @if($document->expires_at)
                            <dt class="col-sm-5">Expires:</dt>
                            <dd class="col-sm-7">
                                <span class="text-warning">{{ $document->expires_at->format('M d, Y') }}</span>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card action-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Download Document
                        </a>

                        @if($document->uploaded_by === Auth::id() || Auth::user()->isAdmin())
                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Document
                            </a>

                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2"></i>Delete Document
                            </button>
                        @endif

                        <hr>

                        <a href="{{ route('documents.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Documents
                        </a>
                    </div>
                </div>
            </div>

            <!-- Related Documents -->
            @if($document->category)
                @php
                    $relatedDocs = App\Models\Document::where('category_id', $document->category_id)
                                                   ->where('id', '!=', $document->id)
                                                   ->limit(5)
                                                   ->get();
                @endphp
                @if($relatedDocs->count() > 0)
                    <div class="card action-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Related Documents</h5>
                        </div>
                        <div class="card-body">
                            @foreach($relatedDocs as $relatedDoc)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-file me-2 text-muted"></i>
                                    <a href="{{ route('documents.show', $relatedDoc) }}" class="text-decoration-none">
                                        {{ $relatedDoc->title }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($document->uploaded_by === Auth::id() || Auth::user()->isAdmin())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $document->title }}</strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone and will permanently delete the file.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Document</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
