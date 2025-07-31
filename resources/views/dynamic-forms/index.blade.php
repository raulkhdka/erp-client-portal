@extends('layouts.app')

@section('title', 'Dynamic Forms')

@section('breadcrumb')
    <span class="breadcrumb-item active">Dynamic Forms</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('dynamic-forms.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Form
        </a>
    </div>
@endsection

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1rem; /* Reduced padding from 2rem */
        margin-bottom: 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .page-header h1 {
        font-weight: 700;
        margin: 0;
        font-size: 2rem; /* Reduced font size from 2.5rem */
    }

    .page-header .subtitle {
        opacity: 0.9;
        margin-top: 0.5rem;
        font-size: 1rem; /* Reduced font size from 1.1rem */
    }

    .stats-card {
        border-radius: 15px;
        /* padding: 1.5rem; */
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 1rem;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        text-decoration: none;
        color: inherit;
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1rem;
    }

    .stats-icon.primary { background: linear-gradient(45deg, #667eea, #764ba2); }
    .stats-icon.success { background: linear-gradient(45deg, #56ab2f, #a8e6cf); }
    .stats-icon.warning { background: linear-gradient(45deg, #f093fb, #f5576c); }
    .stats-icon.info { background: linear-gradient(45deg, #4facfe, #00f2fe); }

    .main-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 1.5rem 2rem;
        border-radius: 20px 20px 0 0 !important;
    }

    .table-container {
        padding: 0;
        display: flex; /* Added to wrap table in flex */
        flex-direction: column; /* Ensures rows stack vertically */
    }

    .table-modern {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #dee2e6; /* Added border to the table */
    }

    .table-modern thead th {
        background: #f8f9fa;
        border: 1px solid #dee2e6; /* Added border to th */
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .table-modern thead th:hover {
        background: #e9ecef;
    }

    .table-modern thead th.sortable::after {
        content: '\f0dc';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 10px;
        opacity: 0.3;
        transition: opacity 0.3s ease;
    }

    .table-modern thead th.sortable:hover::after {
        opacity: 0.7;
    }

    .table-modern thead th.sort-asc::after {
        content: '\f0de';
        opacity: 1;
        color: #667eea;
    }

    .table-modern thead th.sort-desc::after {
        content: '\f0dd';
        opacity: 1;
        color: #667eea;
    }

    .table-modern tbody td {
        padding: 1.25rem 1.5rem;
        border: 1px solid #dee2e6; /* Added border to td */
        border-top: none; /* Avoid double border on top */
        vertical-align: middle;
    }

    .table-modern tbody tr {
        transition: all 0.3s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9ff;
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .badge-modern {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 500;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.9rem;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .btn-create-new {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 50px;
        padding: 1rem 2.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 1rem;
        color: white;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        position: relative;
        overflow: hidden;
        min-width: 200px;
    }

    .btn-create-new::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-create-new:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
        color: white;
        text-decoration: none;
    }

    .btn-create-new:hover::before {
        left: 100%;
    }

    .btn-create-new:active {
        transform: translateY(-1px) scale(1.02);
    }

    .btn-create-new i {
        margin-right: 0.75rem;
        font-size: 1.2rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .alert-modern {
        border: none;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .empty-state h3 {
        margin-bottom: 1rem;
        color: #495057;
    }

    .pagination-modern .page-link {
        border: none;
        border-radius: 10px;
        margin: 0 2px;
        padding: 0.5rem 1rem;
        color: #667eea;
        transition: all 0.3s ease;
    }

    .pagination-modern .page-link:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .pagination-modern .page-item.active .page-link {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .field-count {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        text-wrap: nowrap;
    }

    .sn-number {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .search-filter-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .filter-select {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .search-input {
        border-radius: 25px;
        border: 2px solid #e9ecef;
        padding: 0.75rem 1.5rem;
        padding-left: 3rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background: white;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
    }

    .modal-modern .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    .modal-modern .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        border: none;
        padding: 1.5rem 2rem;
    }

    .modal-modern .modal-title {
        font-weight: 700;
    }

    .modal-modern .btn-close {
        filter: invert(1);
    }

    .modal-list-item {
        padding: 1rem;
        border-bottom: 1px solid #f1f3f4;
        transition: all 0.3s ease;
    }

    .modal-list-item:hover {
        background: #f8f9ff;
        padding-left: 1.5rem;
    }

    .modal-list-item:last-child {
        border-bottom: none;
    }

    .btn-share {
        background: linear-gradient(45deg, #00c4b4, #00e7eb);
        color: white;
    }

    .btn-share:hover {
        background: linear-gradient(45deg, #00a896, #00ced1);
        color: white;
    }

    .copied-tooltip {
        position: absolute;
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.8rem;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 1000;
    }

    .copied-tooltip.show {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
            text-align: center;
        }

        .page-header h1 {
            font-size: 1.5rem; /* Reduced font size for mobile */
        }

        .stats-card {
            margin-bottom: 1rem;
        }

        .table-responsive {
            border-radius: 15px;
        }

        .btn-action {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }

        .btn-create-new {
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            min-width: 150px;
        }

        .search-filter-container {
            padding: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1><i class="fas fa-file-alt me-3"></i>Dynamic Forms</h1>
                <p class="subtitle mb-0">Create, manage, and track your dynamic forms</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('dynamic-forms.create') }}" class="btn-create-new">
                    <i class="fas fa-plus-circle"></i>Create New Form
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6">
            <a href="#" class="stats-card" data-bs-toggle="modal" data-bs-target="#totalFormsModal">
                <div class="stats-icon primary">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3 class="mb-1">{{ $forms->total() }}</h3>
                <p class="text-muted mb-0">Total Forms</p>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="#" class="stats-card" data-bs-toggle="modal" data-bs-target="#activeFormsModal">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="mb-1">{{ $forms->where('is_active', true)->count() }}</h3>
                <p class="text-muted mb-0">Active Forms</p>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="#" class="stats-card" data-bs-toggle="modal" data-bs-target="#inactiveFormsModal">
                <div class="stats-icon warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <h3 class="mb-1">{{ $forms->where('is_active', false)->count() }}</h3>
                <p class="text-muted mb-0">Inactive Forms</p>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="#" class="stats-card" data-bs-toggle="modal" data-bs-target="#thisWeekFormsModal">
                <div class="stats-icon info">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3 class="mb-1">{{ $forms->where('created_at', '>=', now()->subDays(7))->count() }}</h3>
                <p class="text-muted mb-0">This Week</p>
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search and Filter -->
    <div class="search-filter-container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control search-input" placeholder="Search forms by name or description..." id="searchInput">
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-select filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select filter-select" id="sortFilter">
                            <option value="created_at_desc">Newest First</option>
                            <option value="created_at_asc">Oldest First</option>
                            <option value="name_asc">Name A-Z</option>
                            <option value="name_desc">Name Z-A</option>
                            <option value="fields_desc">Most Fields</option>
                            <option value="fields_asc">Least Fields</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-secondary w-100" id="clearFilters">
                            <i class="fas fa-times me-2"></i>Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="main-card">
        <div class="card-header-custom">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Forms Management</h5>
                </div>
                <div class="col-auto">
                    <span class="badge bg-primary">{{ $forms->count() }} forms displayed</span>
                </div>
            </div>
        </div>

        <div class="table-container">
            @if($forms->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern" id="formsTable">
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th class="sortable" data-sort="name"><i class="fas fa-file-signature me-2"></i>Form Name</th>
                                <th><i class="fas fa-align-left me-2"></i>Description</th>
                                <th class="sortable" data-sort="status"><i class="fas fa-toggle-on me-2"></i>Status</th>
                                <th class="sortable" data-sort="fields"><i class="fas fa-list-ul me-2"></i>Fields</th>
                                <th class="sortable" data-sort="created_at"><i class="fas fa-calendar me-2"></i>Created</th>
                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($forms as $index => $form)
                            <tr data-form-name="{{ strtolower($form->name) }}"
                                data-form-description="{{ strtolower($form->description ?? '') }}"
                                data-form-status="{{ $form->is_active ? 'active' : 'inactive' }}"
                                data-form-fields="{{ $form->fields->count() }}"
                                data-form-created="{{ $form->created_at->timestamp }}">
                                <td>
                                    <span class="sn-number">{{ $forms->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $form->name }}</div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($form->description, 50) ?? 'No description' }}</span>
                                </td>
                                <td>
                                    @if ($form->is_active)
                                        <span class="badge badge-modern bg-success">
                                            <i class="fas fa-check me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge badge-modern bg-danger">
                                            <i class="fas fa-times me-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="field-count">{{ $form->fields->count() }} fields</span>
                                </td>
                                <td>
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $form->created_at->format('M d, Y') }}
                                    </div>
                                    <small class="text-muted">{{ $form->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dynamic-forms.show', $form->id) }}"
                                           class="btn btn-info btn-action"
                                           title="View Form"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('dynamic-forms.edit', $form->id) }}"
                                           class="btn btn-warning btn-action"
                                           title="Edit Form"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('dynamic-forms.public-show', $form->id) }}"
                                           target="_blank"
                                           class="btn btn-secondary btn-action"
                                           title="View Public Form"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="{{ route('dynamic-forms.share', $form->id) }}"
                                            class="btn btn-share"
                                            title="Share Form"
                                            data-bs-toggle="tooltip">
                                             <i class="fas fa-share-alt"></i>
                                         </a>
                                        <form action="{{ route('dynamic-forms.destroy', $form->id) }}"
                                              method="POST"
                                              class="d-inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete this form and all its associated fields and responses?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-danger btn-action"
                                                    title="Delete Form"
                                                    data-bs-toggle="tooltip">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center p-4">
                    <nav aria-label="Forms pagination">
                        <ul class="pagination pagination-modern">
                            {{ $forms->links() }}
                        </ul>
                    </nav>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h3>No Forms Found</h3>
                    <p class="mb-4">You haven't created any dynamic forms yet. Get started by creating your first form!</p>
                    <a href="{{ route('dynamic-forms.create') }}" class="btn-create-new">
                        <i class="fas fa-plus-circle"></i>Create Your First Form
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Total Forms Modal -->
<div class="modal fade modal-modern" id="totalFormsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>All Forms ({{ $forms->total() }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($forms as $form)
                <div class="modal-list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $form->name }}</h6>
                            <small class="text-muted">{{ $form->fields->count() }} fields • Created {{ $form->created_at->diffForHumans() }}</small>
                        </div>
                        <div>
                            @if ($form->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Active Forms Modal -->
<div class="modal fade modal-modern" id="activeFormsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Active Forms ({{ $forms->where('is_active', true)->count() }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($forms->where('is_active', true) as $form)
                <div class="modal-list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $form->name }}</h6>
                            <small class="text-muted">{{ $form->fields->count() }} fields • Created {{ $form->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Inactive Forms Modal -->
<div class="modal fade modal-modern" id="inactiveFormsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pause-circle me-2"></i>Inactive Forms ({{ $forms->where('is_active', false)->count() }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($forms->where('is_active', false) as $form)
                <div class="modal-list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $form->name }}</h6>
                            <small class="text-muted">{{ $form->fields->count() }} fields • Created {{ $form->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-danger">Inactive</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- This Week Forms Modal -->
<div class="modal fade modal-modern" id="thisWeekFormsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Forms Created This Week ({{ $forms->where('created_at', '>=', now()->subDays(7))->count() }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($forms->where('created_at', '>=', now()->subDays(7)) as $form)
                <div class="modal-list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $form->name }}</h6>
                            <small class="text-muted">{{ $form->fields->count() }} fields • Created {{ $form->created_at->diffForHumans() }}</small>
                        </div>
                        <div>
                            @if ($form->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter = document.getElementById('sortFilter');
    const clearFilters = document.getElementById('clearFilters');
    const tableRows = document.querySelectorAll('#formsTable tbody tr');

    // Search functionality
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        tableRows.forEach(row => {
            const formName = row.dataset.formName;
            const formDescription = row.dataset.formDescription;
            const formStatus = row.dataset.formStatus;

            const matchesSearch = formName.includes(searchTerm) || formDescription.includes(searchTerm);
            const matchesStatus = !statusValue || formStatus === statusValue;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Sort functionality
    function sortTable() {
        const sortValue = sortFilter.value;
        const tbody = document.querySelector('#formsTable tbody');
        const rows = Array.from(tableRows);

        rows.sort((a, b) => {
            let aValue, bValue;

            switch(sortValue) {
                case 'name_asc':
                    aValue = a.dataset.formName;
                    bValue = b.dataset.formName;
                    return aValue.localeCompare(bValue);
                case 'name_desc':
                    aValue = a.dataset.formName;
                    bValue = b.dataset.formName;
                    return bValue.localeCompare(aValue);
                case 'created_at_asc':
                    aValue = parseInt(a.dataset.formCreated);
                    bValue = parseInt(b.dataset.formCreated);
                    return aValue - bValue;
                case 'created_at_desc':
                    aValue = parseInt(a.dataset.formCreated);
                    bValue = parseInt(b.dataset.formCreated);
                    return bValue - aValue;
                case 'fields_asc':
                    aValue = parseInt(a.dataset.formFields);
                    bValue = parseInt(b.dataset.formFields);
                    return aValue - bValue;
                case 'fields_desc':
                    aValue = parseInt(a.dataset.formFields);
                    bValue = parseInt(b.dataset.formFields);
                    return bValue - aValue;
                default:
                    return 0;
            }
        });

        rows.forEach(row => tbody.appendChild(row));
        filterTable(); // Reapply filters after sorting
    }

    // Copy to clipboard functionality
    window.copyFormLink = function(url, button) {
        navigator.clipboard.writeText(url).then(() => {
            const tooltip = button.querySelector('.copied-tooltip');
            tooltip.classList.add('show');
            setTimeout(() => {
                tooltip.classList.remove('show');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    };

    // Event listeners
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    sortFilter.addEventListener('change', sortTable);

    clearFilters.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        sortFilter.value = 'created_at_desc';
        sortTable();
    });

    // Column sorting
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const sortType = this.dataset.sort;
            const currentSort = sortFilter.value;

            // Toggle sort direction
            if (currentSort === sortType + '_asc') {
                sortFilter.value = sortType + '_desc';
            } else {
                sortFilter.value = sortType + '_asc';
            }

            // Update header classes
            document.querySelectorAll('.sortable').forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });

            if (sortFilter.value.includes('_asc')) {
                this.classList.add('sort-asc');
            } else {
                this.classList.add('sort-desc');
            }

            sortTable();
        });
    });

    // Add smooth animations
    const cards = document.querySelectorAll('.stats-card, .main-card, .search-filter-container');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection
