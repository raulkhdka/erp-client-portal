@extends('layouts.app')

@section('title', 'Document Categories')

@section('breadcrumb')
    <a href="{{ route('admin.documents.index') }}">Documents</a>
    <span class="breadcrumb-item active">Categories</span>
@endsection

@section('actions')
    <div class="btn-group">
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
        /* Enhanced Table Styling with Auto-Adjusting Sizes */
        .enhanced-table {
            border-collapse: separate !important;
            border-spacing: 0;
            border: 0.5px solid #000000 !important;
            border-radius: 12px !important;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            width: 100% !important;
            table-layout: auto !important;
            /* Changed from fixed to auto for dynamic column sizing */
        }

        .table-responsive {
            border: 0.5px solid #000000 !important;
            border-radius: 12px !important;
            background: white;
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .enhanced-table thead th:first-child,
        .enhanced-table tbody td:first-child {
            border-left: none !important;
        }

        .enhanced-table thead th:last-child,
        .enhanced-table tbody td:last-child {
            border-right: none !important;
        }

        .enhanced-table thead th {
            border-top: none !important;
            border-bottom: 0.5px solid #000000 !important;
        }

        .enhanced-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .enhanced-table thead th:first-child {
            border-top-left-radius: 12px;
        }

        .enhanced-table thead th:last-child {
            border-top-right-radius: 12px;
        }

        .enhanced-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }

        .enhanced-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }

        .enhanced-table thead th,
        .enhanced-table tbody td {
            border-right: 0.5px solid #000000 !important;
            border-bottom: 0.5px solid #000000 !important;
        }

        .enhanced-table thead th:last-child,
        .enhanced-table tbody td:last-child {
            border-right: none !important;
        }

        .enhanced-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .table-responsive .table {
            margin-bottom: 0 !important;
        }

        .enhanced-table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center !important;
            vertical-align: middle;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.5rem 0.5rem;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }

        /* Removed fixed width settings for dynamic column sizing */
        /* Previously had width and min-width for each column */

        .enhanced-table thead th::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .enhanced-table thead th:hover::before {
            left: 100%;
        }

        .enhanced-table tbody td {
            padding: 0.5rem 0.5rem;
            vertical-align: middle;
            text-align: center !important;
            transition: all 0.3s ease;
            background-color: white;
            word-wrap: break-word;
        }

        /* Specific column adjustments for readability */
        .enhanced-table tbody td:nth-child(1) {
            white-space: nowrap;
        }

        /* # */
        /* Removed width for Name, Icon, Active, Order, Documents, Actions */
        /* .enhanced-table tbody td:nth-child(3) { max-width: 200px; overflow: hidden; text-overflow: ellipsis; } */
        /* Description, commented out */

        .enhanced-table tbody tr {
            transition: all 0.3s ease;
            position: relative;
            height: auto;
        }

        .enhanced-table tbody tr:hover {
            background-color: #f8fafc !important;
            transform: scale(1.01);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .enhanced-table tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        .enhanced-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .enhanced-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        .enhanced-table tbody tr:nth-child(even):hover td {
            background-color: #f1f5f9 !important;
        }

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

        .enhanced-table tbody td strong,
        .enhanced-table tbody td small,
        .enhanced-table tbody td span {
            display: inline-block;
            text-align: center;
        }

        @media (max-width: 1200px) {
            .enhanced-table {
                font-size: 0.875rem;
                border-radius: 12px !important;
                width: 100% !important;
            }

            .enhanced-table thead th,
            .enhanced-table tbody td {
                padding: 0.5rem 0.25rem;
            }

            /* Removed width and min-width settings */
            .enhanced-table thead th:first-child {
                border-top-left-radius: 12px;
            }

            .enhanced-table thead th:last-child {
                border-top-right-radius: 12px;
            }

            .enhanced-table tbody tr:last-child td:first-child {
                border-bottom-left-radius: 12px;
            }

            .enhanced-table tbody tr:last-child td:last-child {
                border-bottom-right-radius: 12px;
            }

            .table-responsive {
                border-radius: 12px !important;
            }
        }

        @media (max-width: 992px) {
            .enhanced-table {
                font-size: 0.8rem;
                border-radius: 10px !important;
            }

            .enhanced-table thead th {
                font-size: 0.75rem;
                padding: 0.4rem 0.25rem;
            }

            .enhanced-table tbody td {
                padding: 0.4rem 0.25rem;
            }

            /* Removed width and min-width settings */
            .enhanced-table thead th:first-child {
                border-top-left-radius: 10px;
            }

            .enhanced-table thead th:last-child {
                border-top-right-radius: 10px;
            }

            .enhanced-table tbody tr:last-child td:first-child {
                border-bottom-left-radius: 10px;
            }

            .enhanced-table tbody tr:last-child td:last-child {
                border-bottom-right-radius: 10px;
            }

            .table-responsive {
                border-radius: 10px !important;
            }
        }

        @media (max-width: 768px) {
            .enhanced-table {
                font-size: 0.75rem;
                border-radius: 8px !important;
            }

            .enhanced-table thead th {
                padding: 0.3rem 0.2rem;
            }

            .enhanced-table tbody td {
                padding: 0.3rem 0.2rem;
            }

            /* Removed width and min-width settings */
            .enhanced-table thead th:first-child {
                border-top-left-radius: 8px;
            }

            .enhanced-table thead th:last-child {
                border-top-right-radius: 8px;
            }

            .enhanced-table tbody tr:last-child td:first-child {
                border-bottom-left-radius: 8px;
            }

            .enhanced-table tbody tr:last-child td:last-child {
                border-bottom-right-radius: 8px;
            }

            .table-responsive {
                border-radius: 8px !important;
            }
        }

        .table-container {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .enhanced-card {
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .enhanced-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .empty-state {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .empty-state i {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        /* Style the pagination container */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 0;
        }

        .pagination .page-item {
            transition: transform 0.2s ease;
        }

        .pagination .page-link {
            border: none;
            border-radius: 50%;
            width: 42px;
            height: 42px;
            line-height: 42px;
            text-align: center;
            padding: 0;
            font-weight: 500;
            color: #333;
            background-color: #f4f4f4;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.25s ease;
        }

        .pagination .page-link:hover {
            background-color: #007bff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .pagination .page-link:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.4);
        }


        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .truncated-content {
            cursor: help;
        }

        .modal-content {
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .modal-header {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .icon-wrapper {
            transition: transform 0.3s ease;
        }

        .icon-wrapper:hover {
            transform: scale(1.1);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card enhanced-card">
                    <div class="card-body">
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
                                                {{-- <th><i class="fas fa-comment me-2"></i>Description</th> --}}
                                                <th><i class="fas fa-image me-2"></i>Icon</th>
                                                <th><i class="fas fa-toggle-on me-2"></i>Active</th>
                                                <th><i class="fas fa-sort me-2"></i>Order</th>
                                                <th><i class="fas fa-file me-2"></i>Documents</th>
                                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
                                                    {{-- <td>
                                                    @php
                                                        $isLong = strlen($category->description ?? '') > 30;
                                                    @endphp
                                                    <span class="{{ $isLong ? 'truncated-content' : '' }}"
                                                          @if ($isLong) data-bs-toggle="tooltip" title="{{ $category->description }}" @endif>
                                                        {{ $category->description ? Str::limit($category->description, 30) : 'N/A' }}
                                                    </span>
                                                </td> --}}
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
                                                    <td colspan="8" class="text-center empty-state">
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
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    {{ $categories->links() }}
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
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Add loading animation to table rows
            const tableRows = document.querySelectorAll('.enhanced-table tbody tr');
            tableRows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.1}s`;
                row.style.animation = 'fadeInUp 0.6s ease-out forwards';
            });

            // Add click animation to badges and buttons
            const animatedElements = document.querySelectorAll('.animated-badge, .btn');
            animatedElements.forEach(element => {
                element.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1.05)';
                    }, 100);
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 200);
                });
            });

            // Smooth scroll for pagination
            const paginationLinks = document.querySelectorAll('.pagination a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const tableContainer = document.querySelector('.table-container');
                    if (tableContainer) {
                        tableContainer.style.opacity = '0.7';
                        tableContainer.style.transform = 'translateY(10px)';
                        setTimeout(() => {
                            tableContainer.style.opacity = '1';
                            tableContainer.style.transform = 'translateY(0)';
                        }, 300);
                    }
                });
            });

            // Auto-resize table
            function autoResizeTable() {
                const table = document.querySelector('.enhanced-table');
                if (table) {
                    table.style.width = '100%';
                    const containerWidth = table.parentElement.offsetWidth;
                    if (table.offsetWidth > containerWidth) {
                        table.style.width = `${containerWidth}px`;
                    }
                }
            }

            autoResizeTable();
            window.addEventListener('resize', autoResizeTable);

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

                    const iconPickerModal = bootstrap.Modal.getInstance(document.getElementById(
                        'iconPickerModal'));
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
                        if (!submitButton || (!submitButton.classList.contains('btn-primary') && !
                                submitButton.classList.contains('btn-warning'))) {
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
        });
    </script>
@endpush
