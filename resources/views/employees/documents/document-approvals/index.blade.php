@extends('layouts.app')

@section('title', 'Pending Document Approvals')

@push('styles')
    <style>
        .document-card {
            background-color: #f8f9fa; /* Light grey background */
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            padding: 15px;
            position: relative;
            overflow: hidden;
        }

        .document-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            border: 2px solid transparent;
            background-image: linear-gradient(white, white), linear-gradient(45deg, #667eea, #764ba2);
            background-clip: padding-box, border-box;
            background-origin: border-box;
        }

        .file-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #6c757d;
        }

        .document-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .document-info {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #212529;
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 12px;
        }

        .btn-group .btn {
            font-size: 0.9rem;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .table-container {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal {
            transition: none !important;
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
                        <h2 class="mb-1">📁 Pending Document Approvals</h2>
                        <p class="text-muted mb-0">Review and approve documents uploaded by your assigned clients</p>
                    </div>
                    <div>
                        <a href="{{ route('employee.documents.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('employee.document-approvals.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Client</label>
                            <select name="client_id" class="form-select client-select">
                                <option value="">All Clients</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search documents..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('employee.document-approvals.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Documents Grid -->
        <div class="row table-container">
            @forelse($documents as $document)
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="document-card">
                        <!-- File Icon -->
                        <div class="text-center">
                            @php
                                $iconClass = match (strtolower($document->file_type)) {
                                    'pdf' => 'fas fa-file-pdf text-danger',
                                    'doc', 'docx' => 'fas fa-file-word text-primary',
                                    'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                    'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info',
                                    default => 'fas fa-file text-muted',
                                };
                            @endphp
                            <i class="{{ $iconClass }} file-icon"></i>
                        </div>

                        <!-- Document Title -->
                        <h6 class="document-title" title="{{ $document->title }}">{{ $document->title }}</h6>

                        <!-- Document Info -->
                        <div class="document-info">
                            <div><strong>Client:</strong> {{ $document->client->company_name ?? 'N/A' }}</div>
                            <div><strong>Uploaded By:</strong> {{ $document->uploader->name ?? 'N/A' }} (Client)</div>
                            <div><strong>File Type:</strong> {{ strtoupper($document->file_type) }}</div>
                            <div><strong>Size:</strong> {{ number_format($document->file_size / 1024, 2) }} KB</div>
                            <div><strong>Uploaded:</strong> {{ $document->created_at->format('M d, Y') }}</div>
                        </div>

                        <!-- Approval Status -->
                        <div class="mb-3">
                            <span class="badge badge-pending">Pending Approval</span>
                        </div>

                        <!-- Actions -->
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('employee.documents.preview', $document) }}" class="btn btn-sm btn-outline-info" title="Preview">
                                <i class="fas fa-file-image"></i>
                            </a>
                            <form action="{{ route('employee.document-approvals.approve', $document) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('employee.document-approvals.reject', $document) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reject this document?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-5x text-muted mb-3"></i>
                        <h4>No pending documents found</h4>
                        <p class="text-muted">There are no client-uploaded documents awaiting approval.</p>
                        <a href="{{ route('employee.documents.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Documents
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for client dropdown
            $('.client-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select a client',
                allowClear: true,
                width: '100%'
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Animate document cards
            $('.document-card').each(function(index) {
                $(this).css('animation-delay', `${index * 0.1}s`);
                $(this).addClass('animate__animated animate__fadeInUp');
            });

            // Animate pagination links
            $('.pagination a').on('click', function() {
                $('.table-container').css({
                    'opacity': '0.7',
                    'transform': 'translateY(10px)',
                    'transition': 'all 0.3s ease'
                });
                setTimeout(() => {
                    $('.table-container').css({
                        'opacity': '1',
                        'transform': 'translateY(0)'
                    });
                }, 300);
            });
        });
    </script>
@endpush
