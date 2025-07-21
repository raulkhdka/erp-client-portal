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
                            'jpg', 'jpeg', 'png', 'gif', 'svg' => 'fas fa-file-image',
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

                <div class="mb-3">
                    @if($document->category)
                        <span class="badge bg-light text-dark me-2">
                            <i class="{{ $document->category->icon }}"></i> {{ $document->category->name }}
                        </span>
                    @endif
                    @if($document->is_public)
                        <span class="badge bg-success me-2">Public</span>
                    @endif
                    @if($document->is_confidential)
                        <span class="badge bg-danger me-2">Confidential</span>
                    @endif
                    @if($document->expires_at)
                        <span class="badge bg-warning text-dark me-2">Expires {{ $document->expires_at->format('M d, Y') }}</span>
                    @endif
                </div>

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
        <!-- Left Panel -->
        <div class="col-md-8">
            @if($document->description)
                <div class="card action-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-align-left me-2"></i>Description</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $document->description }}</p>
                    </div>
                </div>
            @endif

            @if(in_array(strtolower($document->file_type), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']))
                <div class="card action-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-eye me-2"></i>Preview</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="preview-container">
                            @if(strtolower($document->file_type) === 'pdf')
                                <iframe src="{{ route('documents.preview', $document) }}" width="100%" height="600px"></iframe>
                            @elseif(in_array(strtolower($document->file_type), ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                                <img src="{{ route('documents.preview', $document) }}" class="img-fluid">
                            @elseif(strtolower($document->file_type) === 'txt')
                                <div class="p-3">
                                    <pre>{{ Storage::disk('public')->get($document->file_path) }}</pre>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Panel -->
        <div class="col-md-4">
            <div class="card action-card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Type:</dt>
                        <dd class="col-sm-7">{{ strtoupper($document->file_type) }}</dd>

                        <dt class="col-sm-5">Size:</dt>
                        <dd class="col-sm-7">{{ $document->formatted_file_size }}</dd>

                        <dt class="col-sm-5">Uploaded By:</dt>
                        <dd class="col-sm-7">{{ $document->uploader->name }}</dd>

                        <dt class="col-sm-5">Date:</dt>
                        <dd class="col-sm-7">{{ $document->created_at->format('M d, Y H:i') }}</dd>

                        @if($document->client)
                            <dt class="col-sm-5">Client:</dt>
                            <dd class="col-sm-7">{{ $document->client->company_name }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            @if($document->uploaded_by === Auth::id() || Auth::user()->isAdmin())
                <div class="card action-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-bolt me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>Delete Document
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
