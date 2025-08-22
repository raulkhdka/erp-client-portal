@extends('layouts.app')

@section('title', 'Document Categories')

@section('breadcrumb')
    <a href="{{ route('admin.documents.index') }}">Documents</a>
    <span class="breadcrumb-item active">Categories</span>
@endsection

@section('actions')
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fas fa-plus me-2"></i>New Category
        </button>
        <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Documents
        </a>
    </div>
@endsection

@push('styles')
    <style>
        /* Table Styling */
        .enhanced-table {
            border-collapse: separate;
            border-spacing: 0;
            border: 0.5px solid #000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 100%;
            table-layout: auto;
            border-radius: 0 !important;
        }

        .table-responsive {
            background: white;
            width: 100%;
            margin: 0 auto;
        }

        /* Button Styling */
        .btn-primary, .btn-secondary {
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.5rem;
            border-radius: 20px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #10b981;
            border-color: #10b981;
        }

        .btn-primary:hover {
            background-color: #0e9f6e;
            transform: scale(1.05);
        }

        .btn-secondary {
            background-color: #6b7280;
            border-color: #6b7280;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
            transform: scale(1.05);
        }

        .btn-print {
            background-color: #4B5EAA;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            background-color: #3B4A8A;
            transform: scale(1.05);
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
            border-radius: 20px;
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
            line-height: 42px;
            text-align: center;
            padding: 0;
            color: #333;
            background-color: #f4f4f4;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .pagination .page-link:hover {
            background-color: #10b981;;
            color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: #10b981;;
            color: white;
            border-color: #10b981;
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

        /* Modal Styling */
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .modal-header {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        /* Icon Wrapper */
        .icon-wrapper {
            transition: transform 0.3s ease;
        }

        .icon-wrapper:hover {
            transform: scale(1.1);
        }

        /* Badge Styling */
        .animated-badge {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: inline-block;
            white-space: nowrap;
        }

        .animated-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .animated-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .animated-badge:hover::before {
            left: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card enhanced-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <div class="d-flex gap-2">
                                <button onclick="window.print()" class="btn btn-print" data-bs-toggle="tooltip" title="Print Table">
                                    <i class="fas fa-print me-2"></i>Print
                                </button>
                            </div>
                            <div class="search-form-container">
                                <form class="search-form">
                                    <input type="text" name="search" id="search-input" class="form-control"
                                        placeholder="Search by name..." value="{{ request('search') }}" title="Search by name">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($categories->count() > 0)
                            <div class="table-container">
                                <div class="table-responsive">
                                    <table class="table enhanced-table">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-list-ol me-2"></i>SN</th>
                                                <th><i class="fas fa-folder me-2"></i>Name</th>
                                                <th><i class="fas fa-image me-2"></i>Icon</th>
                                                <th><i class="fas fa-toggle-on me-2"></i>Active</th>
                                                <th><i class="fas fa-sort me-2"></i>Order</th>
                                                <th><i class="fas fa-file me-2"></i>Documents</th>
                                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="category-table-body">
                                            @forelse ($categories as $category)
                                                <tr>
                                                    <td>
                                                        <span class="truncated-content" data-bs-toggle="tooltip"
                                                            title="Category #{{ $loop->iteration }}">
                                                            {{ $loop->iteration }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.document-categories.show', $category->id) }}"
                                                            class="text-decoration-none text-primary">
                                                            <strong>{{ $category->name }}</strong>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if ($category->icon && $category->color)
                                                            <span
                                                                class="icon-wrapper d-inline-flex align-items-center justify-content-center rounded-circle animated-badge"
                                                                style="background-color: {{ $category->color }}; color: {{ \App\Helpers\ColorHelper::getTextColor($category->color) ?? '#ffffff' }}; width: 30px; height: 30px;"
                                                                data-bs-toggle="tooltip"
                                                                title="Icon: {{ $category->icon }}">
                                                                <i class="{{ $category->icon }}"
                                                                    style="font-size: 1.2em;"></i>
                                                            </span>
                                                        @elseif ($category->icon)
                                                            <span class="icon-wrapper animated-badge"
                                                                data-bs-toggle="tooltip"
                                                                title="Icon: {{ $category->icon }}">
                                                                <i class="{{ $category->icon }}"
                                                                    style="font-size: 1.2em;"></i>
                                                            </span>
                                                        @else
                                                            <span data-bs-toggle="tooltip" title="No icon">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $category->is_active ? 'success' : 'danger' }} animated-badge"
                                                            data-bs-toggle="tooltip"
                                                            title="Active: {{ $category->is_active ? 'Yes' : 'No' }}">
                                                            {{ $category->is_active ? 'Yes' : 'No' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span data-bs-toggle="tooltip"
                                                            title="Sort Order: {{ $category->sort_order }}">
                                                            {{ $category->sort_order }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span data-bs-toggle="tooltip"
                                                            title="Documents: {{ $category->documents_count }}">
                                                            {{ $category->documents_count }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.document-categories.show', $category->id) }}"
                                                                class="btn btn-sm btn-outline-primary"
                                                                data-bs-toggle="tooltip" title="View Category">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-warning edit-category-btn"
                                                                data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                                                data-id="{{ $category->id }}"
                                                                data-name="{{ $category->name }}"
                                                                data-description="{{ $category->description }}"
                                                                data-icon="{{ $category->icon }}"
                                                                data-color="{{ $category->color }}"
                                                                data-is-active="{{ $category->is_active }}"
                                                                data-sort-order="{{ $category->sort_order }}"
                                                                data-update-url="{{ route('admin.document-categories.update', $category->id) }}"
                                                                data-bs-toggle="tooltip" title="Edit Category">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal{{ $category->id }}"
                                                                data-bs-toggle="tooltip" title="Delete Category">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>

                                                        <!-- Delete Modal -->
                                                        <div class="modal fade" id="deleteModal{{ $category->id }}"
                                                            tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-danger text-white">
                                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                                        <button type="button"
                                                                            class="btn-close btn-close-white"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to delete this
                                                                        category?<br><br>
                                                                        <strong>Name:</strong> {{ $category->name }}<br>
                                                                        <strong>Description:</strong>
                                                                        {{ $category->description ?? 'N/A' }}<br>
                                                                        <small class="text-muted">This action cannot be
                                                                            undone.</small>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Cancel</button>
                                                                        <form
                                                                            action="{{ route('admin.document-categories.destroy', $category->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Delete
                                                                                Category</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center empty-state">
                                                        <i class="fas fa-folder fa-4x text-muted mb-4"></i>
                                                        <h4 class="text-muted mb-3">No Document Categories Found</h4>
                                                        <p class="text-muted mb-4">Start by creating your first category.
                                                        </p>
                                                        <div class="mt-4">
                                                            <button type="button" class="btn btn-primary btn-lg"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#createCategoryModal">
                                                                <i class="fas fa-plus me-2"></i>Create First Category
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <div class="loading-overlay" id="loading-overlay">
                                        <div class="spinner"></div>
                                    </div>
                                </div>

                                <div class="pagination mt-4">
                                    {{ $categories->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5 empty-state">
                                <i class="fas fa-folder fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">No Document Categories Found</h4>
                                <p class="text-muted mb-4">Start by creating your first category.</p>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                                        data-bs-target="#createCategoryModal">
                                        <i class="fas fa-plus me-2"></i>Create First Category
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createCategoryModalLabel">Create New Document Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.document-categories.store') }}" method="POST" id="createCategoryForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon (Font Awesome Class)</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                    id="icon" name="icon" value="{{ old('icon') }}"
                                    placeholder="e.g., fas fa-folder">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#iconPickerModal" data-target-input="#icon">
                                    <i class="fas fa-folder-open"></i> Pick Icon
                                </button>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="color" class="form-label">Color (Hex Code)</label>
                            <input type="color"
                                class="form-control form-control-color @error('color') is-invalid @enderror"
                                id="color" name="color" value="{{ old('color', '#007bff') }}">
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active_create" name="is_active"
                                value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active_create">Is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Document Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name"
                                name="name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="edit_description" name="description"
                                rows="3"></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_icon" class="form-label">Icon (Font Awesome Class)</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                    id="edit_icon" name="icon" placeholder="e.g., fas fa-folder">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#iconPickerModal" data-target-input="#edit_icon">
                                    <i class="fas fa-folder-open"></i> Pick Icon
                                </button>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_color" class="form-label">Color (Hex Code)</label>
                            <input type="color"
                                class="form-control form-control-color @error('color') is-invalid @enderror"
                                id="edit_color" name="color">
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                id="edit_sort_order" name="sort_order" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active"
                                value="1">
                            <label class="form-check-label" for="edit_is_active">Is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="iconPickerModalLabel">Select an Icon</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="iconSearchInput" placeholder="Search icons...">
                    </div>
                    <div id="iconGrid" class="row row-cols-4 row-cols-md-6 row-cols-lg-8 g-2 text-center">
                        <!-- Icons will be loaded here by JavaScript -->
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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });

            // Real-time search
            const searchInput = document.getElementById('search-input');
            const searchForm = document.querySelector('.search-form');
            const tableBody = document.getElementById('category-table-body');
            const tableContainer = document.querySelector('.table-container');
            const emptyState = document.querySelector('.empty-state');
            const pagination = document.querySelector('.pagination');

            function performSearch() {
                const searchTerm = searchInput.value.trim();
                axios.get(`${window.location.origin}/admin/document-categories`, {
                    params: {
                        search: searchTerm
                    },
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (response.data.categories?.data.length > 0) {
                        tableBody.innerHTML = response.data.categories.data.map((category, index) => `
                            <tr>
                                <td>
                                    <span class="truncated-content" data-bs-toggle="tooltip" title="Category #${index + 1}">
                                        ${index + 1}
                                    </span>
                                </td>
                                <td>
                                    <a href="${window.location.origin}/admin/document-categories/${category.id}"
                                        class="text-decoration-none text-primary">
                                        <strong>${category.name}</strong>
                                    </a>
                                </td>
                                <td>
                                    ${category.icon && category.color ? `
                                        <span class="icon-wrapper d-inline-flex align-items-center justify-content-center rounded-circle animated-badge"
                                            style="background-color: ${category.color}; color: ${category.text_color || '#ffffff'}; width: 30px; height: 30px;"
                                            data-bs-toggle="tooltip" title="Icon: ${category.icon}">
                                            <i class="${category.icon}" style="font-size: 1.2em;"></i>
                                        </span>
                                    ` : category.icon ? `
                                        <span class="icon-wrapper animated-badge" data-bs-toggle="tooltip" title="Icon: ${category.icon}">
                                            <i class="${category.icon}" style="font-size: 1.2em;"></i>
                                        </span>
                                    ` : `
                                        <span data-bs-toggle="tooltip" title="No icon">N/A</span>
                                    `}
                                </td>
                                <td>
                                    <span class="badge bg-${category.is_active ? 'success' : 'danger'} animated-badge"
                                        data-bs-toggle="tooltip" title="Active: ${category.is_active ? 'Yes' : 'No'}">
                                        ${category.is_active ? 'Yes' : 'No'}
                                    </span>
                                </td>
                                <td>
                                    <span data-bs-toggle="tooltip" title="Sort Order: ${category.sort_order}">
                                        ${category.sort_order}
                                    </span>
                                </td>
                                <td>
                                    <span data-bs-toggle="tooltip" title="Documents: ${category.documents_count}">
                                        ${category.documents_count}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="${window.location.origin}/admin/document-categories/${category.id}"
                                            class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Category">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-warning edit-category-btn"
                                            data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                            data-id="${category.id}"
                                            data-name="${category.name}"
                                            data-description="${category.description || ''}"
                                            data-icon="${category.icon || ''}"
                                            data-color="${category.color || '#007bff'}"
                                            data-is-active="${category.is_active}"
                                            data-sort-order="${category.sort_order}"
                                            data-update-url="${window.location.origin}/admin/document-categories/${category.id}"
                                            data-bs-toggle="tooltip" title="Edit Category">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal${category.id}"
                                            data-bs-toggle="tooltip" title="Delete Category">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                        tableContainer.style.display = 'block';
                        if (emptyState) emptyState.style.display = 'none';
                        if (pagination) pagination.innerHTML = response.data.pagination || '';
                        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
                    } else {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center empty-state">
                                    <i class="fas fa-folder fa-4x text-muted mb-4"></i>
                                    <h4 class="text-muted mb-3">No Document Categories Found</h4>
                                    <p class="text-muted mb-4">Start by creating your first category.</p>
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-primary btn-lg"
                                            data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                                            <i class="fas fa-plus me-2"></i>Create First Category
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableContainer.style.display = 'block';
                        if (emptyState) emptyState.style.display = 'none';
                        if (pagination) pagination.innerHTML = '';
                    }
                }).catch(error => {
                    showToast('error', error.response?.data?.message || 'An error occurred while searching.');
                });
            }

            searchInput.addEventListener('input', performSearch);
            searchForm.addEventListener('submit', function(event) {
                event.preventDefault();
                performSearch();
            });

            // Edit modal population
            var editCategoryModal = document.getElementById('editCategoryModal');
            editCategoryModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');
                var description = button.getAttribute('data-description');
                var icon = button.getAttribute('data-icon');
                var color = button.getAttribute('data-color');
                var isActive = button.getAttribute('data-is-active');
                var sortOrder = button.getAttribute('data-sort-order');
                var updateUrl = button.getAttribute('data-update-url');

                var modalForm = editCategoryModal.querySelector('#editCategoryForm');
                modalForm.action = updateUrl;

                modalForm.querySelector('#edit_name').value = name;
                modalForm.querySelector('#edit_description').value = description || '';
                modalForm.querySelector('#edit_icon').value = icon || '';
                modalForm.querySelector('#edit_color').value = color || '#007bff';
                modalForm.querySelector('#edit_sort_order').value = sortOrder || 0;
                modalForm.querySelector('#edit_is_active').checked = (isActive === '1');
            });

            // Icon Picker Logic
            const fontAwesomeIcons = [
                'fas fa-folder', 'fas fa-file', 'fas fa-book', 'fas fa-address-book',
                'fas fa-camera', 'fas fa-car', 'fas fa-chart-bar', 'fas fa-cloud',
                'fas fa-code', 'fas fa-cog', 'fas fa-comments', 'fas fa-credit-card',
                'fas fa-database', 'fas fa-desktop', 'fas fa-dollar-sign', 'fas fa-envelope',
                'fas fa-exchange-alt', 'fas fa-exclamation-triangle', 'fas fa-eye', 'fas fa-female',
                'fas fa-gavel', 'fas fa-globe', 'fas fa-graduation-cap', 'fas fa-heart',
                'fas fa-home', 'fas fa-image', 'fas fa-inbox', 'fas fa-info-circle',
                'fas fa-key', 'fas fa-laptop', 'fas fa-lightbulb', 'fas fa-lock',
                'fas fa-map-marker-alt', 'fas fa-microchip', 'fas fa-mobile-alt', 'fas fa-music',
                'fas fa-newspaper', 'fas fa-paint-brush', 'fas fa-paper-plane', 'fas fa-phone',
                'fas fa-play-circle', 'fas fa-print', 'fas fa-question-circle', 'fas fa-rocket',
                'fas fa-search', 'fas fa-share-alt', 'fas fa-shopping-cart', 'fas fa-sign-in-alt',
                'fas fa-sign-out-alt', 'fas fa-sitemap', 'fas fa-sliders-h', 'fas fa-star',
                'fas fa-sync-alt', 'fas fa-tag', 'fas fa-tasks', 'fas fa-terminal',
                'fas fa-th', 'fas fa-thumbs-up', 'fas fa-ticket-alt', 'fas fa-times',
                'fas fa-trash', 'fas fa-truck', 'fas fa-undo', 'fas fa-upload',
                'fas fa-user', 'fas fa-user-circle', 'fas fa-video', 'fas fa-wallet',
                'fas fa-wifi', 'fas fa-wrench', 'fas fa-bell', 'fas fa-calendar-alt',
                'fas fa-clipboard', 'fas fa-comment', 'fas fa-download', 'fas fa-edit',
                'fas fa-filter', 'fas fa-link', 'fas fa-list', 'fas fa-paperclip',
                'fas fa-plus', 'fas fa-minus', 'fas fa-eye-slash', 'fas fa-location-dot',
                'fas fa-magnifying-glass', 'fas fa-power-off', 'fas fa-shield-alt', 'fas fa-chart-line',
                'fas fa-clipboard-check', 'fas fa-dollar-sign', 'fas fa-euro-sign', 'fas fa-pound-sign',
                'fas fa-rupee-sign', 'fas fa-yen-sign', 'fas fa-coins', 'fas fa-piggy-bank',
                'fas fa-file-invoice', 'fas fa-receipt', 'fas fa-chart-pie', 'fas fa-chart-bar',
                'fas fa-book-open', 'fas fa-briefcase', 'fas fa-building', 'fas fa-university',
                'fas fa-calculator', 'fas fa-cash-register', 'fas fa-file-excel', 'fas fa-file-csv',
                'fas fa-handshake', 'fas fa-users', 'fas fa-user-tie', 'fas fa-phone-alt',
                'fas fa-clipboard-list', 'fas fa-concierge-bell', 'fas fa-file-alt', 'fas fa-tags',
                'fa-solid fa-id-card'
            ];

            const iconGrid = document.getElementById('iconGrid');
            const iconSearchInput = document.getElementById('iconSearchInput');
            let currentTargetInput = null;

            function renderIcons(iconsToRender) {
                iconGrid.innerHTML = '';
                iconsToRender.forEach(iconClass => {
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col icon-item mb-2';
                    colDiv.innerHTML = `
                        <div class="card h-100 p-2 border-0 shadow-sm icon-card animated-badge" data-icon="${iconClass}">
                            <i class="${iconClass} fa-2x"></i>
                            <small class="mt-1 text-muted text-truncate">${iconClass.replace('fas fa-', '')}</small>
                        </div>
                    `;
                    iconGrid.appendChild(colDiv);
                });
            }

            renderIcons(fontAwesomeIcons);

            iconSearchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const filteredIcons = fontAwesomeIcons.filter(iconClass => iconClass.includes(searchTerm));
                renderIcons(filteredIcons);
            });

            document.querySelectorAll('[data-bs-target="#iconPickerModal"]').forEach(button => {
                button.addEventListener('click', function() {
                    const targetInputSelector = this.getAttribute('data-target-input');
                    currentTargetInput = document.querySelector(targetInputSelector);
                    iconSearchInput.value = '';
                    renderIcons(fontAwesomeIcons);
                });
            });

            iconGrid.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopImmediatePropagation();

                const card = event.target.closest('.icon-card');
                if (card && currentTargetInput) {
                    const selectedIconClass = card.getAttribute('data-icon');
                    currentTargetInput.value = selectedIconClass;

                    const iconPickerModal = bootstrap.Modal.getInstance(document.getElementById('iconPickerModal'));
                    if (iconPickerModal) {
                        iconPickerModal.hide();
                    }

                    const parentModal = currentTargetInput.closest('.modal');
                    if (parentModal) {
                        const bootstrapModal = bootstrap.Modal.getInstance(parentModal);
                        if (bootstrapModal) {
                            bootstrapModal.show();
                        }
                    }
                }
            });

            ['createCategoryForm', 'editCategoryForm'].forEach(formId => {
                const form = document.getElementById(formId);
                if (form) {
                    form.addEventListener('submit', function(event) {
                        const submitButton = event.submitter;
                        if (!submitButton || (!submitButton.classList.contains('btn-primary') && !submitButton.classList.contains('btn-warning'))) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    });

                    form.closest('.modal').addEventListener('hidden.bs.modal', function(event) {
                        if (!event.target.querySelector('[type="submit"]:focus')) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    });
                }
            });

            // Handle modal errors
            @if ($errors->any())
                @php
                    $hasCreateErrors = $errors->hasAny(['name', 'description', 'icon', 'color', 'sort_order', 'is_active']) && old('_token') && session('status') !== 'updated';
                    $hasEditErrors = $errors->any() && old('_token') && session('status') === 'updated';
                @endphp

                var createModalErrors = @json($hasCreateErrors);
                var editModalErrors = @json($hasEditErrors);

                if (createModalErrors) {
                    var createModal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
                    createModal.show();
                } else if (editModalErrors) {
                    var editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                    editModal.show();
                }
            @endif
        });
    </script>
@endpush