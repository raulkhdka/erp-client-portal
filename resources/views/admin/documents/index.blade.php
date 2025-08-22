@extends('layouts.app')

@section('title', 'Document Center')

@section('breadcrumb')
    <span class="breadcrumb-item active">Documents</span>
@endsection

@section('actions')
    <div>
        <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Document
        </a>
        <a href="{{ route('admin.document-categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-folder me-2"></i>Categories
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .btn-primary {
            background-color: #10b981;
            border-color: #10b981;
            margin-right: 0.5rem;
            border-radius: 50px;
            /* Fully rounded button */
        }

        .btn-outline-secondary {
            border-radius: 50px;
            /* Fully rounded button */
        }
         /* keep this it is helpful, KEEP btn-group to use this property */
        /* .btn-group>.btn-primary,
            .btn-group>.btn-outline-secondary {
                border-radius: 50px !important; /* force fully rounded */
        /* margin-right: 0.5rem; */
        /* spacing between buttons */
        /* } */

        /* Table Styling */
        .enhanced-table {
            border-collapse: separate;
            border-spacing: 0;
            border: 0.5px solid #000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 100%;
            table-layout: auto;
            border-radius: 0 !important;
            margin-top: 1rem;
        }

        .table-responsive {
            background: white;
            width: 100%;
            margin: 0 auto;
        }

        /* Table Borders */
        .enhanced-table thead th,
        .enhanced-table tbody td {
            border-right: 0.5px solid #000;
            border-bottom: 0.5px solid #000;
            padding: 0.5rem;
            text-align: center;
            vertical-align: middle;
        }

        .enhanced-table thead th:first-child,
        .enhanced-table tbody td:first-child {
            border-left: none;
        }

        .enhanced-table thead th:last-child,
        .enhanced-table tbody td:last-child {
            border-right: none;
        }

        .enhanced-table thead th {
            border-top: none;
        }

        .enhanced-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Table Header and Rows */
        .enhanced-table thead th {
            background: #10b981;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 0.5rem;
        }

        .enhanced-table tbody td {
            font-size: 0.875rem;
            background-color: white;
        }

        .enhanced-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        /* Badge Styling */
        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Search Form */
        .search-form {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            max-width: 500px;
        }

        .search-form-container {
            display: flex;
            justify-content: flex-end;
            width: 100%;
        }

        .search-form .form-control {
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .search-form .btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-overlay.show {
            display: flex;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #10b981;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .enhanced-table {
                font-size: 0.8rem;
            }

            .enhanced-table thead th,
            .enhanced-table tbody td {
                padding: 0.4rem 0.25rem;
            }
        }

        @media (max-width: 768px) {
            .enhanced-table {
                font-size: 0.75rem;
            }

            .enhanced-table thead th,
            .enhanced-table tbody td {
                padding: 0.3rem 0.2rem;
            }

            .search-form {
                flex-direction: column;
                align-items: stretch;
                max-width: 100%;
            }

            .search-form .form-control,
            .search-form .btn {
                width: 100%;
            }
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 0;
        }

        .pagination .page-link {
            border-radius: 50%;
            width: 42px;
            height: 42px;
            line contributor: center;
            padding: 0;
            color: #333;
            background-color: #f4f4f4;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .pagination .page-link:hover {
            background-color: #007bff;
            color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
        }

        /* Print Styles */
        @media print {

            .btn,
            .search-form-container,
            .pagination {
                display: none !important;
            }

            .enhanced-table {
                border: 1px solid #000;
            }

            .enhanced-table thead th {
                background: #f0f0f0;
                color: #000;
                font-size: 10pt;
                font-weight: bold;
                padding: 8px;
            }

            .enhanced-table tbody tr:nth-child(even) {
                background: #fff;
            }

            .enhanced-table thead th:nth-child(7),
            .enhanced-table tbody td:nth-child(7) {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="enhanced-card">
            <div class="card-body">

                <div class="search-form-container">
                    <form class="search-form">
                        <input type="text" name="search" id="search-input" class="form-control"
                            placeholder="Search by title..." value="{{ request('search') }}" title="Search by title">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </form>
                </div>

                <div class="table-container">
                    @if ($documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table enhanced-table" id="documentsTable">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-list-ol me-2"></i>SN</th>
                                        <th><i class="fas fa-file-signature me-2"></i>Title</th>
                                        <th><i class="fas fa-folder me-2"></i>Category</th>
                                        <th><i class="fas fa-users me-2"></i>Client</th>
                                        <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                        <th><i class="fas fa-calendar me-2"></i>Created</th>
                                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="document-table-body">
                                    @foreach ($documents as $document)
                                        <tr data-document-id="{{ $document->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $document->title }}</td>
                                            <td>
                                                @if ($document->category)
                                                    <span class="badge badge-modern"
                                                        style="background-color: {{ $document->category->color }}">
                                                        <i class="{{ $document->category->icon }}"></i>
                                                        {{ $document->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No category</span>
                                                @endif
                                            </td>
                                            <td>{{ $document->client ? $document->client->company_name : 'N/A' }}</td>
                                            <td>
                                                @if ($document->is_approved)
                                                    <span class="badge badge-modern"
                                                        style="background-color: #10b981; color: white;">
                                                        <i class="fas fa-check me-1"></i>Approved
                                                    </span>
                                                @else
                                                    <span class="badge badge-modern bg-danger">
                                                        <i class="fas fa-times me-1"></i>Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    {!! $document->created_at_nepali_html !!}
                                                </div>
                                                <small
                                                    class="text-muted">{{ $document->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.documents.show', $document) }}"
                                                        class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                                        title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.documents.download', $document) }}"
                                                        class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip"
                                                        title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if ($document->uploaded_by === Auth::id() || Auth::user()->isAdmin())
                                                        <a href="{{ route('admin.documents.edit', $document) }}"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger delete-document-btn"
                                                            data-bs-toggle="tooltip" title="Delete"
                                                            data-id="{{ $document->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="loading-overlay" id="loading-overlay">
                                <div class="spinner"></div>
                            </div>
                        </div>
                        <div class="pagination mt-4">
                            {{ $documents->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5" id="empty-state">
                            <i class="fas fa-folder-open fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No Documents Found</h4>
                            <p class="text-muted mb-4">Upload your first document to get started!</p>
                            <a href="{{ route('admin.documents.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus-circle me-2"></i>Upload Your First Document
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });

            // Handle delete document
            function deleteDocument(documentId) {
                if (!documentId || isNaN(documentId)) {
                    showToast('error', 'Invalid document ID.');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        if (!csrfToken) {
                            showToast('error', 'Security token not found.');
                            return;
                        }

                        axios.delete(`${window.location.origin}/admin/documents/${documentId}`, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(response => {
                            if (response.data.status === 'success') {
                                const row = document.querySelector(
                                    `tr[data-document-id="${documentId}"]`);
                                if (row) row.remove();
                                showToast('success', 'Document deleted successfully.');
                            } else {
                                showToast('error', response.data.message ||
                                    'Failed to delete document.');
                            }
                        }).catch(error => {
                            let errorMessage = 'An error occurred while deleting the document.';
                            if (error.response) {
                                if (error.response.status === 405) errorMessage =
                                    'Method not allowed.';
                                else if (error.response.status === 404) errorMessage =
                                    'Document not found.';
                                else if (error.response.data?.message) errorMessage = error.response
                                    .data.message;
                            } else if (error.request) {
                                errorMessage = 'No response from server.';
                            }
                            showToast('error', errorMessage);
                        });
                    }
                });
            }

            // Handle delete button clicks
            document.body.addEventListener('click', function(event) {
                const button = event.target.closest('.delete-document-btn');
                if (!button) return;
                event.preventDefault();
                deleteDocument(button.dataset.id);
            });

            // Real-time search
            const searchInput = document.getElementById('search-input');
            const searchForm = document.querySelector('.search-form');
            const tableBody = document.getElementById('document-table-body');
            const tableContainer = document.querySelector('.table-container');
            const emptyState = document.getElementById('empty-state');
            const pagination = document.querySelector('.pagination');

            function performSearch() {
                const searchTerm = searchInput.value.trim();
                axios.get(`${window.location.origin}/admin/documents`, {
                    params: {
                        search: searchTerm
                    },
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (response.data.documents?.data.length > 0) {
                        tableBody.innerHTML = response.data.documents.data.map((document, index) => `
                    <tr data-document-id="${document.id}">
                        <td>${index + 1}</td>
                        <td>${document.title}</td>
                        <td>
                            ${document.category ? `
                                        <span class="badge badge-modern" style="background-color: ${document.category.color}">
                                            <i class="${document.category.icon}"></i> ${document.category.name}
                                        </span>
                                    ` : '<span class="text-muted">No category</span>'}
                        </td>
                        <td>${document.client ? document.client.company_name : 'N/A'}</td>
                        <td>
                            ${document.is_approved ?
                                '<span class="badge badge-modern" style="background-color: #10b981; color: white;"><i class="fas fa-check me-1"></i>Approved</span>' :
                                '<span class="badge badge-modern bg-danger"><i class="fas fa-times me-1"></i>Pending</span>'}
                        </td>
                        <td>
                            <div class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                ${document.created_at_nepali_html}
                            </div>
                            <small class="text-muted">${new Date(document.created_at).toLocaleString('en-US', { timeZone: 'Asia/Kathmandu' })}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="${window.location.origin}/admin/documents/${document.id}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="${window.location.origin}/admin/documents/${document.id}/download" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                ${document.uploaded_by === {{ Auth::id() }} || {{ Auth::user()->isAdmin() }} ? `
                                            <a href="${window.location.origin}/admin/documents/${document.id}/edit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-document-btn" data-bs-toggle="tooltip" title="Delete" data-id="${document.id}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        ` : ''}
                            </div>
                        </td>
                    </tr>
                `).join('');
                        tableContainer.style.display = 'block';
                        if (emptyState) emptyState.style.display = 'none';
                        if (pagination) pagination.innerHTML = response.data.pagination || '';
                        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap
                            .Tooltip(el));
                    } else {
                        tableBody.innerHTML = '';
                        tableContainer.style.display = 'none';
                        if (emptyState) emptyState.style.display = 'block';
                        if (pagination) pagination.innerHTML = '';
                    }
                }).catch(error => {
                    showToast('error', error.response?.data?.message ||
                        'An error occurred while searching.');
                });
            }

            searchInput.addEventListener('input', performSearch);
            searchForm.addEventListener('submit', function(event) {
                event.preventDefault();
                performSearch();
            });
        });
    </script>
@endpush
