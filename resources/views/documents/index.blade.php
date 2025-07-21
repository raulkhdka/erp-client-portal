@extends('layouts.app')

@section('title', 'Document Center')

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
            @if(isset($clients) && (Auth::user()->isAdmin() || Auth::user()->isEmployee()))
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
            @endif
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

                        @if(isset($clients) && (Auth::user()->isAdmin() || Auth::user()->isEmployee()))
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
                        @endif

                        <div class="col-md-3">
                            <label class="form-label">Access Level</label>
                            <select name="access_level" class="form-select">
                                <option value="">All Documents</option>
                                <option value="public" {{ request('access_level') == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="confidential" {{ request('access_level') == 'confidential' ? 'selected' : '' }}>Confidential</option>
                                <option value="my_documents" {{ request('access_level') == 'my_documents' ? 'selected' : '' }}>My Documents</option>
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
                @php
                    $isAdmin = Auth::user()->isAdmin();
                    $isEmployee = Auth::user()->isEmployee();
                    $isUploader = $document->uploaded_by === Auth::id();
                    $assignedEmployeeId = optional($document->client)->employee_id;
                    $isAssignedEmployee = $isEmployee && $assignedEmployeeId === Auth::id();
                @endphp

                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="document-card p-3">
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

                        <h6 class="mb-2 text-truncate" title="{{ $document->title }}">{{ $document->title }}</h6>

                        @if ($document->category)
                            <span class="badge category-badge mb-2"
                                style="background-color: {{ $document->category->color }}">
                                <i class="{{ $document->category->icon }}"></i> {{ $document->category->name }}
                            </span>
                        @endif

                        <div class="small text-muted mb-2">
                            <div>{{ $document->formatted_file_size }}</div>
                            <div>{{ $document->created_at->format('M d, Y') }}</div>
                            @if ($document->client)
                                <div>Client: {{ $document->client->company_name }}</div>
                            @endif
                        </div>

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

                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-download"></i>
                            </a>

                            @if ($isAdmin || $isUploader || $isAssignedEmployee)
                                <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-folder-open fa-5x text-muted mb-3"></i>
                    <h4>No documents found</h4>
                    <p class="text-muted">Upload your first document to get started!</p>
                    <a href="{{ route('documents.create') }}" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload Document
                    </a>
                </div>
            @endforelse
        </div>

        @if ($documents->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.client-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'All Clients',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
