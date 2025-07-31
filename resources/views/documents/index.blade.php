@extends('layouts.app')

@section('title', 'Document Center')

@section('breadcrumb')
    <span class="breadcrumb-item active">Documents</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('documents.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Document
        </a>
        <a href="{{ route('document-categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-folder me-2"></i>Categories
        </a>
    </div>
@endsection

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
                        <h2 class="mb-1">📁 Document Center</h2>
                        <p class="text-muted mb-0">Manage all your documents in one place</p>
                    </div>
                    <div>
                        <a href="{{ route('documents.create') }}" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Document
                        </a>
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
                            <i class="fas fa-users fa-2x text-success me-3"></i>
                            <div>
                                <h4 class="mb-0">{{ $clients->count() }}</h4>
                                <small class="text-muted">Clients</small>
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
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('documents.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Client</label>
                            <select name="client_id" class="form-select client-select">
                                <option value="">All Clients</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Access Level</label>
                            <select name="access_level" class="form-select">
                                <option value="">All Documents</option>
                                <option value="public" {{ request('access_level') == 'public' ? 'selected' : '' }}>Public
                                </option>
                                <option value="confidential"
                                    {{ request('access_level') == 'confidential' ? 'selected' : '' }}>Confidential</option>
                                <option value="my_documents"
                                    {{ request('access_level') == 'my_documents' ? 'selected' : '' }}>My Documents</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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
                            <a href="{{ route('documents.index') }}" class="btn btn-secondary">Clear</a>
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
                            @if ($document->client)
                                <div>Client: {{ $document->client->company_name }}</div>
                            @endif
                        </div>

                        <!-- Access Indicators -->
                        <div class="mb-2">
                            @if ($document->is_public)
                                <span class="badge bg-success">Public</span>
                            @endif
                            @if ($document->is_confidential)
                                <span class="badge bg-danger">Confidential</span>
                            @endif
                            @if ($document->download_count > 0)
                                <span class="badge bg-info">{{ $document->download_count }} downloads</span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-download"></i>
                            </a>

                            @if ($document->uploaded_by === Auth::id() || Auth::user()->isAdmin())
                                <a href="{{ route('documents.edit', $document) }}"
                                    class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('documents.destroy', $document) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif

                            @if (!$document->is_approved && (Auth::user()->isAdmin() || Auth::user()->isEmployee()))
                                <form action="{{ route('documents.approve', $document) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>

                                <form action="{{ route('documents.reject', $document) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Reject this document?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-5x text-muted mb-3"></i>
                        <h4>No documents found</h4>
                        <p class="text-muted">Upload your first document to get started!</p>
                        <a href="{{ route('documents.create') }}" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Document
                        </a>
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

    <!-- Send Form Modal -->
    <div class="modal fade" id="sendFormModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Select Client</label>
                            <select class="form-select client-select">
                                <option>Choose a client...</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Form Type</label>
                            <select class="form-select">
                                <option>Choose form type...</option>
                                <option>Contact Form</option>
                                <option>Feedback Form</option>
                                <option>Service Request</option>
                                <option>Custom Form</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" rows="3" placeholder="Optional message to include..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Send Form</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Access Modal -->
    <div class="modal fade" id="manageAccessModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Document Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Recent Documents</h6>
                            <div class="list-group">
                                @foreach ($documents->take(5) as $doc)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $doc->title }}</strong><br>
                                            <small class="text-muted">{{ $doc->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div>
                                            @if ($doc->is_public)
                                                <span class="badge bg-success">Public</span>
                                            @else
                                                <span class="badge bg-warning">Private</span>
                                            @endif
                                            <a href="{{ route('documents.manage-access', $doc) }}"
                                                class="btn btn-sm btn-outline-primary ms-2">
                                                Manage
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Access Statistics</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4>{{ $documents->where('is_public', true)->count() }}</h4>
                                            <small>Public Documents</small>
                                        </div>
                                        <div class="col-6">
                                            <h4>{{ $documents->where('is_confidential', true)->count() }}</h4>
                                            <small>Confidential</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for client dropdowns
            $('.client-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'All Clients',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
