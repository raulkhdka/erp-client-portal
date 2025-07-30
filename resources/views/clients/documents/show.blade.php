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
                </div>

                <hr class="opacity-50">

                {{-- Added Document Details --}}
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Uploaded By:</strong>
                            @if($document->uploader)
                                {{ $document->uploader->name }}
                            @else
                                N/A
                            @endif
                        </p>
                        <p class="mb-0"><strong>Upload Date:</strong> {{ $document->created_at->format('M d, Y H:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Approved By:</strong>
                            @if($document->approver)
                                {{ $document->approver->name }}
                            @else
                                <span class="badge bg-warning">Pending Approval</span>
                            @endif
                        </p>
                        @if($document->approved_at)
                            <p class="mb-0"><strong>Approved On:</strong> {{ $document->approved_at->format('M d, Y H:i A') }}</p>
                        @endif
                    </div>
                </div>
                {{-- End Added Document Details --}}

            </div>

            <div class="col-md-4 text-end">
                <div class="btn-group-vertical" role="group">
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-light btn-lg mb-2">
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                    @if(in_array(strtolower($document->file_type), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'txt']))
                        <a href="{{ route('clients.documents.preview', $document) }}" class="btn btn-outline-light mb-2" target="_blank">
                            <i class="fas fa-eye me-2"></i>Preview
                        </a>
                    @endif
                    <a href="{{ route('clients.documents.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Documents
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(in_array(strtolower($document->file_type), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'txt']))
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
@endsection