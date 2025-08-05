@extends('layouts.app')

@section('title', 'Assigned Client Documents')

@push('styles')
    <style>
        .document-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
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

        .enhanced-table {
            border-collapse: separate !important;
            border-spacing: 0;
            border: 0.5px solid #000000 !important;
            border-radius: 12px !important;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            width: 100% !important;
            table-layout: fixed !important;
            min-width: 0;
        }

        .table-container {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modal improvements */
        .modal {
            z-index: 1055 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }

        .delete-confirm:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Prevent focus issues */
        .modal.show {
            display: block !important;
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
                        <h2 class="mb-1 fs-5">📁 Assigned Client Documents</h2>
                        <p class="text-muted mb-0">Manage documents for your assigned clients</p>
                    </div>
                    <div>
                        <a href="{{ route('employee.document-approvals.index') }}" class="btn btn-primary me-2">
                            <i class="fas fa-check-circle me-2"></i>Pending Approvals
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="fas fa-upload me-2"></i>Upload Document
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-0">
            <div class="card-body">
                <form method="GET" action="{{ route('employee.documents.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select client-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->documents_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
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
                            <label class="form-label">Approval Status</label>
                            <select name="approval_status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending</option>
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
                            <a href="{{ route('employee.documents.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Documents Grid -->
        <div class="row table-container">
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
                                    'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info',
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
                                style="background-color: {{ $document->category->color ?? '#6c757d' }}">
                                <i class="{{ $document->category->icon ?? 'fas fa-tag' }}"></i>
                                {{ $document->category->name }}
                            </span>
                        @endif

                        <!-- Client -->
                        <div class="small text-muted mb-2">
                            <div><strong>Client:</strong> {{ $document->client->company_name ?? 'N/A' }}</div>
                            <div><strong>Uploaded By:</strong> {{ $document->uploader->name ?? 'N/A' }} ({{ $document->uploader->isClient() ? 'Client' : 'Employee' }})</div>
                        </div>

                        <!-- File Details -->
                        <div class="small text-muted mb-2">
                            <div>{{ number_format($document->file_size / 1024, 2) }} KB</div>
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
                            <a href="{{ route('employee.documents.show', $document) }}" class="btn btn-sm btn-outline-primary" title="View Document">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('employee.documents.edit', $document) }}" class="btn btn-sm btn-outline-warning" title="Edit Document">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('employee.documents.download', $document) }}" class="btn btn-sm btn-outline-success" title="Download Document">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="{{ route('employee.documents.preview', $document) }}" class="btn btn-sm btn-outline-info" title="Preview Document">
                                <i class="fas fa-file-image"></i>
                            </a>
                            @if (!$document->is_approved && !$document->approved_by && $document->uploader->isClient())
                                <a href="{{ route('employee.document-approvals.index') }}?client_id={{ $document->client_id }}" class="btn btn-sm btn-outline-primary" title="View Approvals">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                            @endif
                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                data-document-id="{{ $document->id }}"
                                data-document-title="{{ $document->title }}"
                                data-client-name="{{ $document->client->company_name ?? 'N/A' }}"
                                data-delete-url="{{ route('employee.documents.destroy', $document) }}"
                                title="Delete Document">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-5x text-muted mb-3"></i>
                        <h4>No documents found</h4>
                        <p class="text-muted">Upload a document for your assigned clients to get started!</p>
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

        <!-- Single Delete Modal for All Documents -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this document?</p>
                        <div id="deleteModalContent">
                            <strong>Title:</strong> <span id="modalDocumentTitle"></span><br>
                            <strong>Client:</strong> <span id="modalClientName"></span><br>
                        </div>
                        <small class="text-muted">This action cannot be undone.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Document</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Document Modal -->
        <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('employee.documents.store') }}" enctype="multipart/form-data" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Document Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="categories_id" class="form-label">Category</label>
                            <select class="form-select client-select" id="categories_id" name="categories_id">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('categories_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->documents_count }})
                                    </option>
                                @endforeach
                            </select>
                            @error('categories_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Client -->
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Client</label>
                            <select class="form-select client-select" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} ({{ $client->company_name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- File -->
                        <div class="mb-3">
                            <label for="document_file" class="form-label">Choose File (PDF, DOC, XLS, JPG, PNG)</label>
                            <input type="file" class="form-control" id="document_file" name="document_file" required>
                            @error('document_file') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description (optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Access Levels -->
                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_public" {{ old('is_public') ? 'checked' : '' }}> Public
                            </label>
                            <label class="form-check-label ms-3">
                                <input type="checkbox" name="is_confidential" {{ old('is_confidential') ? 'checked' : '' }}> Confidential
                            </label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for category and client dropdowns
            $('.client-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select an option',
                allowClear: true,
                width: '100%'
            });

            let currentDeleteUrl = '';

            // Handle delete button click - Use single modal approach
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const documentId = $(this).data('document-id');
                const documentTitle = $(this).data('document-title');
                const clientName = $(this).data('client-name');
                currentDeleteUrl = $(this).data('delete-url');

                console.log('Delete button clicked for document ID:', documentId);

                // Populate modal with document info
                $('#modalDocumentTitle').text(documentTitle);
                $('#modalClientName').text(clientName);

                // Show the single modal
                $('#deleteModal').modal('show');
            });

            // Handle delete confirmation
            $('#confirmDeleteBtn').on('click', function(e) {
                e.preventDefault();

                const $button = $(this);

                // Prevent double-clicks
                if ($button.prop('disabled')) {
                    return false;
                }

                // Disable button and show loading
                $button.prop('disabled', true);
                const originalText = $button.html();
                $button.html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

                // Create form data for DELETE request
                const formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('_method', 'DELETE');

                $.ajax({
                    url: currentDeleteUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Delete success:', response);

                        if (typeof AppUtils !== 'undefined' && AppUtils.showToast) {
                            AppUtils.showToast('Document deleted successfully', 'success');
                        } else {
                            alert('Document deleted successfully');
                        }

                        // Hide modal and reload page
                        $('#deleteModal').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    },
                    error: function(xhr, status, error) {
                        console.log('Delete error:', error);

                        if (typeof AppUtils !== 'undefined' && AppUtils.showToast) {
                            AppUtils.showToast('Failed to delete document', 'error');
                        } else {
                            alert('Failed to delete document');
                        }

                        // Re-enable button on error
                        $button.prop('disabled', false);
                        $button.html(originalText);
                    }
                });
            });

            // Reset modal state when hidden
            $('#deleteModal').on('hidden.bs.modal', function() {
                console.log('Delete modal hidden');
                $('#confirmDeleteBtn').prop('disabled', false).html('Delete Document');
                currentDeleteUrl = '';
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Animate document cards on load
            $('.document-card').each(function(index) {
                $(this).css('animation-delay', `${index * 0.1}s`);
            });

            // Handle pagination with smooth transition
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