@extends('layouts.app')

@section('title', 'My Documents')

@push('styles')
    <style>
        .document-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .document-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .category-badge {
            font-size: 0.8em;
        }

        .file-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">📁 My Documents</h2>
                        <p class="text-muted mb-0">Upload and view your documents</p>
                    </div>
                    <div>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="fas fa-upload me-2"></i>Upload Document
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card p-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-alt fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $documents->total() }}</h4>
                            <small>Total Documents</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-tags fa-2x text-primary me-3"></i>
                            <div>
                                <h4 class="mb-0">{{ $categories->count() }}</h4>
                                <small class="text-muted">Categories</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-download fa-2x text-info me-3"></i>
                            <div>
                                <h4 class="mb-0">{{ $documents->sum('download_count') }}</h4>
                                <small class="text-muted">Downloads</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock fa-2x text-secondary me-3"></i>
                            <div>
                                <h4 class="mb-0">{{ $documents->where('is_approved', true)->count() }}</h4>
                                <small class="text-muted">Approved Documents</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('clients.documents.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="categories_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Access Level</label>
                            <select name="access_level" class="form-select">
                                <option value="">All Documents</option>
                                <option value="public" {{ request('access_level') == 'public' ? 'selected' : '' }}>Public
                                </option>
                                <option value="confidential"
                                    {{ request('access_level') == 'confidential' ? 'selected' : '' }}>Confidential
                                </option>
                                <option value="my_documents"
                                    {{ request('access_level') == 'my_documents' ? 'selected' : '' }}>My Documents
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search documents..."
                                    value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('clients.documents.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Documents Grid -->
        <div class="row">
            @forelse($documents as $document)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="document-card p-3">
                        <!-- File Icon -->
                        <div class="text-center">
                            @php
                                $iconClass = match (strtolower($document->file_type)) {
                                    'pdf' => 'fas fa-file-pdf text-danger',
                                    'doc', 'docx' => 'fas fa-file-word text-primary',
                                    'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                    'ppt', 'pptx' => 'fas fa-file-powerpoint text-warning',
                                    'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info',
                                    'zip', 'rar' => 'fas fa-file-archive text-secondary',
                                    default => 'fas fa-file text-muted',
                                };
                            @endphp
                            <i class="{{ $iconClass }} file-icon"></i>
                        </div>

                        <!-- Document Info -->
                        <h6 class="mb-2 text-truncate" title="{{ $document->title }}">
                            {{ $document->title }}
                        </h6>

                        <!-- Category Badge -->
                        @if ($document->category)
                            <span class="badge category-badge mb-2"
                                style="background-color: {{ $document->category->color }}">
                                <i class="{{ $document->category->icon }}"></i>
                                {{ $document->category->name }}
                            </span>
                        @endif

                        <!-- File Details -->
                        <div class="small text-muted mb-2">
                            <div>{{ $document->formatted_file_size }}</div>
                            <div>{{ $document->created_at->format('M d, Y') }}</div>
                        </div>

                        <!-- Approval Status -->
                        <div class="mb-2">
                            @if ($document->is_approved)
                                <span class="badge bg-success">Approved</span>
                            @elseif ($document->approved_by)
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending Approval</span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('clients.documents.show', $document) }}" class="btn btn-sm btn-outline-primary" title="View Document">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-success" title="Download Document">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-5x text-muted mb-3"></i>
                        <h4>No documents found</h4>
                        <p class="text-muted">Upload your first document to get started!</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="fas fa-upload me-2"></i>Upload Document
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($documents->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

    <!-- Upload Document Modal -->
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Document Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="categories_id" class="form-label">Category</label>
                        <select class="form-select" id="categories_id" name="categories_id" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- File -->
                    <div class="mb-3">
                        <label for="file" class="form-label">Choose File</label>
                        <input type="file" class="form-control" id="file" name="file" required>
                    </div>

                    <!-- Optional Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <!-- You can add more fields here if needed -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 if used anywhere (not needed for this modal as default select is fine)
        });
    </script>
@endpush
