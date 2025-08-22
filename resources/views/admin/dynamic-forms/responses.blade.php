```html
@extends('layouts.app')
@section('title', 'Dynamic Form Responses')
@section('breadcrumb')
    <span class="breadcrumb-item active">Dynamic Form Responses</span>
@endsection
@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.dynamic-forms.index') }}" class="btn btn-light">
            <i class="fas fa-arrow-left me-2"></i>Back to Forms
        </a>
    </div>
@endsection
@section('styles')
<style>
    :root {
        --primary-color: #4361ee;
        --primary-light: #4895ef;
        --secondary-color: #3f37c9;
        --success-color: #4cc9f0;
        --dark-color: #2b2d42;
        --light-color: #f8f9fa;
        --border-color: #e0e0e0;
        --card-shadow: 0 10px 20px rgba(0,0,0,0.05);
        --transition: all 0.3s ease;
    }

    body {
        background-color: #f5f7fb;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .main-card {
        background: white;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        border: none;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .card-header-custom {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border-bottom: 1px solid var(--border-color);
        padding: 1.5rem 2rem;
        border-radius: 20px 20px 0 0 !important;
        color: white;
    }

    .card-header-custom h5 {
        font-weight: 600;
        margin: 0;
    }

    .table-container {
        padding: 0;
    }

    .table-modern {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern thead th {
        background: #f8f9ff;
        border: 1px solid var(--border-color);
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: var(--dark-color);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        text-align: center;
    }

    .table-modern thead th:hover {
        background: #eef2ff;
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
        color: var(--primary-color);
    }

    .table-modern thead th.sort-desc::after {
        content: '\f0dd';
        opacity: 1;
        color: var(--primary-color);
    }

    .table-modern tbody td {
        padding: 1.25rem 1.5rem;
        border: 1px solid var(--border-color);
        border-top: none;
        vertical-align: middle;
        transition: var(--transition);
    }

    .table-modern tbody tr {
        transition: var(--transition);
    }

    .table-modern tbody tr:hover {
        background-color: #f0f4ff;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
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
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        margin-bottom: 1rem;
        color: var(--dark-color);
    }

    .pagination-modern .page-link {
        border: none;
        border-radius: 10px;
        margin: 0 2px;
        padding: 0.5rem 1rem;
        color: var(--primary-color);
        transition: var(--transition);
        background: white;
        border: 1px solid var(--border-color);
    }

    .pagination-modern .page-link:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        border-color: var(--primary-color);
    }

    .pagination-modern .page-item.active .page-link {
        background: var(--primary-color);
        border-color: var(--primary-color);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        transition: var(--transition);
        border: none;
        font-size: 0.9rem;
        background: #f0f4ff;
        color: var(--primary-color);
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        background: var(--primary-color);
        color: white;
    }

    .modal-modern .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    .modal-modern .modal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
        transition: var(--transition);
    }

    .modal-list-item:hover {
        background: #f8f9ff;
        padding-left: 1.5rem;
    }

    .modal-list-item:last-child {
        border-bottom: none;
    }

    .modal-body pre {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.25rem;
        font-size: 0.9rem;
    }

    .sn-number {
        background: var(--primary-color);
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
        box-shadow: var(--card-shadow);
    }

    .filter-select {
        border-radius: 10px;
        border: 2px solid var(--border-color);
        padding: 0.5rem 1rem;
        transition: var(--transition);
        background: #f8f9ff;
    }

    .filter-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        background: white;
    }

    .search-input {
        border-radius: 25px;
        border: 2px solid var(--border-color);
        padding: 0.75rem 1.5rem;
        padding-left: 3rem;
        transition: var(--transition);
        background: #f8f9ff;
    }

    .search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
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

    @media (max-width: 768px) {
        .table-responsive {
            border-radius: 15px;
        }

        .btn-action {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }

        .search-filter-container {
            padding: 1rem;
        }

        .table-modern thead th,
        .table-modern tbody td {
            padding: 0.75rem 1rem;
        }
    }

    /* Enhanced Response Modal Styles */
    .response-modal .modal-dialog {
        max-width: 1000px;
    }

    @media (min-width: 1200px) {
        .response-modal .modal-dialog {
            max-width: 1200px;
        }
    }

    .response-modal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.18);
        overflow: hidden;
    }

    .response-modal .modal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: #fff;
        border: none;
        padding: 1.5rem 1.5rem 1rem 1.5rem;
    }

    .response-modal .modal-title {
        font-weight: 700;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .response-modal .modal-title small {
        font-weight: 500;
        opacity: 0.9;
    }

    .response-modal .header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .response-modal .header-actions .btn {
        border: none;
        border-radius: 12px;
        padding: 0.5rem 0.75rem;
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        box-shadow: none;
    }

    .response-modal .header-actions .btn:hover {
        transform: translateY(-1px);
        background: rgba(255, 255, 255, 0.25);
    }

    .response-modal .btn-close {
        filter: invert(1);
        opacity: 0.9;
    }

    .response-modal .modal-body {
        background: linear-gradient(180deg, #fbfbfd 0%, #f6f8fb 100%);
        padding: 1.5rem;
    }

    .response-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    @media (min-width: 992px) {
        .response-grid {
            grid-template-columns: 320px 1fr;
            gap: 1.5rem;
        }
    }

    /* Sidebar meta card */
    .meta-card {
        background: #ffffff;
        border: 1px solid #eef2f6;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 10px 24px rgba(67, 97, 238, 0.06);
    }

    .meta-card .meta-title {
        font-weight: 700;
        margin-bottom: 1rem;
        color: #0f172a;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .meta-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 1rem;
    }

    .meta-item {
        display: grid;
        grid-template-columns: 32px 1fr;
        gap: 0.75rem;
        align-items: center;
    }

    .meta-item .meta-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #f0f4ff;
        color: var(--primary-color);
        display: grid;
        place-items: center;
        font-size: 0.9rem;
    }

    .meta-item .meta-text {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .meta-item .meta-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .meta-item .meta-value {
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 600;
    }

    .meta-divider {
        height: 1px;
        background: #eef2f6;
        margin: 1rem 0;
    }

    .meta-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .meta-badges .badge-soft {
        border-radius: 999px;
        background: #f0f4ff;
        color: #3f37c9;
        border: 1px solid #d3ddf4;
        font-weight: 600;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Fields area */
    .fields-panel {
        background: #ffffff;
        border: 1px solid #eef2f6;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 10px 24px rgba(2, 6, 23, 0.04);
    }

    .fields-toolbar {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    @media (min-width: 576px) {
        .fields-toolbar {
            grid-template-columns: 1fr auto auto;
            align-items: center;
        }
    }

    .fields-search {
        position: relative;
    }

    .fields-search .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .fields-search input {
        padding-left: 2.75rem;
        border-radius: 12px;
        border: 2px solid #eef2f6;
        background: #f8fafc;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        height: 42px;
    }

    .fields-search input:focus {
        background: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
    }

    .fields-toggles .btn {
        border-radius: 12px;
        background: #f0f4ff;
        border: 1px solid #e0e6f5;
        color: var(--primary-color);
        font-weight: 600;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .fields-toggles .btn:hover {
        background: var(--primary-color);
        color: white;
    }

    .field-list {
        display: grid;
        gap: 1rem;
    }

    .field-card {
        border: 1px solid #eef2f6;
        border-radius: 14px;
        padding: 1.25rem;
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .field-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(2, 6, 23, 0.06);
    }

    .field-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    .field-label {
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.05rem;
    }

    .field-required {
        color: #ef4444;
        font-weight: 800;
    }

    .field-type {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .field-help {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 0.75rem;
        line-height: 1.5;
    }

    .field-value {
        position: relative;
        background: #f8fafc;
        border: 1px dashed #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        color: #0f172a;
        overflow: hidden;
    }

    .field-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .field-actions .btn {
        border-radius: 10px;
        background: #f0f4ff;
        border: 1px solid #e0e6f5;
        color: var(--primary-color);
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .field-actions .btn:hover {
        background: var(--primary-color);
        color: white;
    }

    .field-value input.form-control,
    .field-value textarea.form-control {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        padding: 0;
        resize: vertical;
        font-size: 0.95rem;
    }

    .field-file a {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        background: #f0f8ff;
        border: 1px solid #d3e9f5;
        transition: all 0.2s ease;
    }

    .field-file a:hover {
        background: #e0f0ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .empty-value {
        color: #9ca3af;
        font-style: italic;
    }

    /* JSON view inside the panel */
    .json-view {
        display: none;
    }

    .json-view.active {
        display: block;
    }

    .json-view pre {
        background: #0b1220;
        color: #d1e9ff;
        border-radius: 12px;
        padding: 1.25rem;
        overflow: auto;
        border: 1px solid #0f1a33;
        box-shadow: inset 0 0 0 1px rgba(67, 97, 238, 0.2);
        max-height: 400px;
        margin-top: 1rem;
    }

    .response-modal .modal-footer {
        border-top: 1px solid #eef2f6;
        background: #ffffff;
        padding: 1rem 1.5rem;
    }

    /* Print: focus on fields panel only */
    @media print {
        body * {
            visibility: hidden;
        }
        .modal.show .fields-panel,
        .modal.show .fields-panel * {
            visibility: visible;
        }
        .modal.show .fields-panel {
            position: absolute;
            inset: 0.5in;
            box-shadow: none !important;
            border: none !important;
        }
    }

    /* Additional modern touches */
    .badge-count {
        background: var(--primary-color);
        color: white;
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        font-weight: 600;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }

    .btn-light {
        background: white;
        border: 1px solid var(--border-color);
        color: var(--dark-color);
        transition: var(--transition);
    }

    .btn-light:hover {
        background: #f0f4ff;
        border-color: var(--primary-light);
        color: var(--primary-color);
    }

    .text-primary {
        color: var(--primary-color) !important;
    }

    .bg-light {
        background-color: #f8f9ff !important;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
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
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <div class="position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control search-input" placeholder="Search responses by form name or client..." id="searchInput">
                </div>
            </div>
            <div class="col-md-6">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select class="form-select filter-select" id="formFilter">
                            <option value="">All Forms</option>
                            @foreach ($forms as $form)
                                <option value="{{ $form->id }}">{{ $form->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select filter-select" id="sortFilter">
                            <option value="submitted_at_desc">Newest First</option>
                            <option value="submitted_at_asc">Oldest First</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-light w-100" id="clearFilters">
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
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Responses Management</h5>
                </div>
                <div class="col-auto">
                    <span class="badge-count">{{ $responses->total() }} responses</span>
                </div>
            </div>
        </div>
        <div class="table-container">
            @if($responses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern" id="responsesTable">
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th class="sortable" data-sort="form"><i class="fas fa-file-signature me-2"></i>Form Name</th>
                                <th><i class="fas fa-user me-2"></i>Client</th>
                                <th class="sortable" data-sort="submitted_at"><i class="fas fa-calendar me-2"></i>Submitted At</th>
                                <th><i class="fas fa-align-left me-2"></i>Response Summary</th>
                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($responses as $index => $response)
                            <tr data-form-id="{{ $response->dynamic_form_id }}"
                                data-form-name="{{ strtolower($response->dynamicForm ? $response->dynamicForm->name : '') }}"
                                data-client-name="{{ strtolower($response->client ? $response->client->name : '') }}"
                                data-submitted-at="{{ $response->submitted_at->timestamp }}">
                                <td>
                                    <span class="sn-number">{{ $responses->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $response->dynamicForm ? $response->dynamicForm->name : 'Unknown Form' }}</div>
                                </td>
                                <td>
                                    {{ $response->client ? $response->client->name : 'N/A' }}
                                </td>
                                <td>
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $response->submitted_at->format('M d, Y H:i') }}
                                    </div>
                                    <small class="text-muted">{{ $response->submitted_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @php
                                        $responseData = json_decode($response->response_data, true);
                                        $summary = [];
                                        if (is_array($responseData)) {
                                            foreach ($responseData as $key => $value) {
                                                if (is_array($value) && isset($value['original_name'])) {
                                                    $summary[] = "$key: File ({$value['original_name']})";
                                                } else {
                                                    $summary[] = "$key: " . (is_string($value) ? \Illuminate\Support\Str::limit($value, 50) : 'N/A');
                                                }
                                            }
                                        }
                                        echo implode(', ', $summary);
                                    @endphp
                                </td>
                                <td>
                                    <button type="button" class="btn btn-action" data-bs-toggle="modal" data-bs-target="#responseModal{{ $response->id }}" title="View Details" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-center p-4">
                    <nav aria-label="Responses pagination">
                        <ul class="pagination pagination-modern">
                            {{ $responses->links() }}
                        </ul>
                    </nav>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h3>No Responses Found</h3>
                    <p class="mb-4">No responses have been submitted yet.</p>
                    <a href="{{ route('admin.dynamic-forms.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Forms
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Response Modals -->
@foreach ($responses as $response)
  @php
    $responseData = json_decode($response->response_data, true) ?? [];
    $formFields = $response->dynamicForm ? $response->dynamicForm->fields : collect([]);
    $attachmentsCount = collect($responseData)
      ->filter(fn($v) => is_array($v) && isset($v['original_name']))
      ->count();
    $prettyJson = json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  @endphp
  <div class="modal fade modal-modern response-modal" id="responseModal{{ $response->id }}" tabindex="-1" aria-labelledby="responseModalLabel{{ $response->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header align-items-start">
          <div class="modal-title" id="responseModalLabel{{ $response->id }}">
            <span><i class="fas fa-file-alt me-2"></i>Response Details</span>
            <small>
              Form: {{ $response->dynamicForm ? $response->dynamicForm->name : 'Unknown Form' }}
              • ID: #{{ $response->id }}
            </small>
          </div>
          <div class="header-actions ms-auto">
            <button
              class="btn btn-sm"
              type="button"
              data-bs-toggle="tooltip"
              title="Toggle JSON view"
              data-action="toggle-json"
              data-target="#jsonView-{{ $response->id }}"
            >
              <i class="fas fa-code"></i>
            </button>
            <button
              class="btn btn-sm"
              type="button"
              data-bs-toggle="tooltip"
              title="Copy JSON"
              data-action="copy-json"
              data-json-target="#jsonSource-{{ $response->id }}"
            >
              <i class="fas fa-copy"></i>
            </button>
            <button
              class="btn btn-sm"
              type="button"
              data-bs-toggle="tooltip"
              title="Download JSON"
              data-action="download-json"
              data-json-target="#jsonSource-{{ $response->id }}"
              data-filename="response-{{ $response->id }}.json"
            >
              <i class="fas fa-download"></i>
            </button>
            <button
              class="btn btn-sm"
              type="button"
              data-bs-toggle="tooltip"
              title="Print"
              data-action="print"
            >
              <i class="fas fa-print"></i>
            </button>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        </div>
        <div class="modal-body">
          <div class="response-grid">
             <!-- Sidebar Meta -->
            <aside class="meta-card" aria-label="Response details">
              <div class="meta-title"><i class="fas fa-info-circle"></i> Overview</div>
              <ul class="meta-list">
                <li class="meta-item">
                  <div class="meta-icon"><i class="fas fa-user"></i></div>
                  <div class="meta-text">
                    <span class="meta-label">Client</span>
                    <span class="meta-value">{{ $response->client ? $response->client->name : 'N/A' }}</span>
                  </div>
                </li>
                <li class="meta-item">
                  <div class="meta-icon"><i class="fas fa-calendar-alt"></i></div>
                  <div class="meta-text">
                    <span class="meta-label">Submitted</span>
                    <span class="meta-value">{{ $response->submitted_at->format('M d, Y H:i') }}</span>
                  </div>
                </li>
                <li class="meta-item">
                  <div class="meta-icon"><i class="fas fa-clock"></i></div>
                  <div class="meta-text">
                    <span class="meta-label">When</span>
                    <span class="meta-value">{{ $response->submitted_at->diffForHumans() }}</span>
                  </div>
                </li>
              </ul>
              <div class="meta-divider"></div>
              <div class="meta-badges">
                <span class="badge badge-soft">
                  <i class="fas fa-hashtag"></i>{{ $formFields->count() }} fields
                </span>
                <span class="badge badge-soft">
                  <i class="fas fa-paperclip"></i>{{ $attachmentsCount }} attachments
                </span>
              </div>
            </aside>
             <!-- Fields Panel -->
            <section class="fields-panel">
              @if ($response->dynamicForm && $response->dynamicForm->description)
                <div class="mb-3 text-muted">{{ $response->dynamicForm->description }}</div>
              @endif
              <div class="fields-toolbar">
                <div class="fields-search">
                  <i class="fas fa-search search-icon"></i>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Search by label or value..."
                    aria-label="Search fields"
                    data-action="search-fields"
                    data-scope="#fields-{{ $response->id }}"
                  />
                </div>
                <div class="fields-toggles">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm w-100"
                    data-action="collapse-fields"
                    data-scope="#fields-{{ $response->id }}"
                  >
                    <i class="fas fa-compress"></i>Compact
                  </button>
                </div>
                <div class="fields-toggles">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm w-100"
                    data-action="expand-fields"
                    data-scope="#fields-{{ $response->id }}"
                  >
                    <i class="fas fa-expand"></i>Comfortable
                  </button>
                </div>
              </div>
              {{-- JSON view (hidden by default) --}}
              <div id="jsonView-{{ $response->id }}" class="json-view" aria-live="polite">
                <pre id="jsonSource-{{ $response->id }}" data-json='@json($responseData, JSON_UNESCAPED_SLASHES)'>{{ $prettyJson }}</pre>
              </div>
              {{-- Fields list --}}
              <div id="fields-{{ $response->id }}" class="field-list">
                @if ($formFields->isNotEmpty() && is_array($responseData))
                  @foreach ($formFields as $field)
                    @php
                      $fieldValue = $responseData[$field->field_name] ?? null;
                      $isFile = is_array($fieldValue) && isset($fieldValue['original_name']);
                      $fieldType = strtoupper($field->field_type);
                      $displayValue = '';
                      if ($isFile) {
                        $displayValue = $fieldValue['original_name'];
                      } elseif (is_array($fieldValue)) {
                        $displayValue = implode(', ', $fieldValue);
                      } elseif (is_string($fieldValue)) {
                        $displayValue = $fieldValue;
                      }
                    @endphp
                    <article class="field-card" data-field-label="{{ Str::lower($field->field_label) }}" data-field-value="{{ Str::lower($displayValue) }}">
                      <header class="field-head">
                        <div class="field-label">
                          <i class="fas fa-align-left text-primary"></i>
                          <span>{{ $field->field_label }}</span>
                          @if ($field->is_required)
                            <span class="field-required" aria-hidden="true">*</span>
                          @endif
                        </div>
                        <span class="field-type">{{ $fieldType }}</span>
                      </header>
                      @if ($field->help_text)
                        <div class="field-help">{{ $field->help_text }}</div>
                      @endif
                      <div class="field-value @if($isFile) field-file @endif">
                        @if ($isFile)
                          <a href="{{ asset('storage/' . $fieldValue['path']) }}" target="_blank" rel="noopener">
                            <i class="fas fa-paperclip"></i>
                            <span>{{ $fieldValue['original_name'] }}</span>
                            <small class="text-muted">Download</small>
                          </a>
                        @elseif ($field->field_type === 'textarea')
                          @if (is_string($fieldValue) && $fieldValue !== '')
                            <textarea class="form-control" rows="4" readonly>{{ $fieldValue }}</textarea>
                          @else
                            <span class="empty-value">No value</span>
                          @endif
                        @else
                          @if ($displayValue !== '')
                            <input type="text" class="form-control" value="{{ $displayValue }}" readonly />
                          @else
                            <span class="empty-value">No value</span>
                          @endif
                        @endif
                      </div>
                      <div class="field-actions">
                        @if (!$isFile && $displayValue !== '')
                          <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm"
                            data-action="copy-field"
                            data-copy-value="{{ $displayValue }}"
                            title="Copy value"
                          >
                            <i class="fas fa-copy"></i>Copy
                          </button>
                        @endif
                      </div>
                    </article>
                  @endforeach
                @else
                  <div class="alert alert-warning m-0" role="alert">
                    No form fields or response data available.
                  </div>
                @endif
              </div>
            </section>
          </div>
        </div>
        <div class="modal-footer">
          <span class="text-muted me-auto">
            <i class="fas fa-info-circle me-1"></i>
            Tip: Use the search box to quickly locate a field.
          </span>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endforeach
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const searchInput = document.getElementById('searchInput');
    const formFilter = document.getElementById('formFilter');
    const sortFilter = document.getElementById('sortFilter');
    const clearFilters = document.getElementById('clearFilters');
    const tableRows = document.querySelectorAll('#responsesTable tbody tr');

    // Search and filter functionality
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const formId = formFilter.value;
        tableRows.forEach(row => {
            const formName = row.dataset.formName;
            const clientName = row.dataset.clientName;
            const formIdValue = row.dataset.formId;
            const matchesSearch = formName.includes(searchTerm) || clientName.includes(searchTerm);
            const matchesForm = !formId || formIdValue === formId;
            if (matchesSearch && matchesForm) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Sort functionality
    function sortTable() {
        const sortValue = sortFilter.value;
        const tbody = document.querySelector('#responsesTable tbody');
        const rows = Array.from(tableRows);
        rows.sort((a, b) => {
            let aValue, bValue;
            if (sortValue === 'submitted_at_asc') {
                aValue = parseInt(a.dataset.submittedAt);
                bValue = parseInt(b.dataset.submittedAt);
                return aValue - bValue;
            } else if (sortValue === 'submitted_at_desc') {
                aValue = parseInt(a.dataset.submittedAt);
                bValue = parseInt(b.dataset.submittedAt);
                return bValue - aValue;
            } else if (sortValue === 'form_asc') {
                aValue = a.dataset.formName;
                bValue = b.dataset.formName;
                return aValue.localeCompare(bValue);
            } else if (sortValue === 'form_desc') {
                aValue = a.dataset.formName;
                bValue = b.dataset.formName;
                return bValue.localeCompare(aValue);
            }
            return 0;
        });
        rows.forEach(row => tbody.appendChild(row));
        filterTable(); // Reapply filters after sorting
    }

    // Event listeners
    searchInput.addEventListener('input', filterTable);
    formFilter.addEventListener('change', filterTable);
    sortFilter.addEventListener('change', sortTable);
    clearFilters.addEventListener('click', function() {
        searchInput.value = '';
        formFilter.value = '';
        sortFilter.value = 'submitted_at_desc';
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
});

document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
      return new bootstrap.Tooltip(el);
    });

    function closestModal(el) {
      return el.closest('.modal');
    }

    function toggleJson(targetSel) {
      const node = document.querySelector(targetSel);
      if (!node) return;
      node.classList.toggle('active');
    }

    function copyTextToClipboard(text) {
      if (!navigator.clipboard) {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta);
      } else {
        navigator.clipboard.writeText(text).catch(() => {});
      }
    }

    function downloadJsonFromPre(preSelector, filename = 'response.json') {
      const pre = document.querySelector(preSelector);
      if (!pre) return;
      const json = pre.getAttribute('data-json') || pre.textContent || '{}';
      const blob = new Blob([json], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    }

    function filterFields(scopeSel, term) {
      const scope = document.querySelector(scopeSel);
      if (!scope) return;
      const q = term.trim().toLowerCase();
      const cards = scope.querySelectorAll('.field-card');
      cards.forEach((card) => {
        const lbl = card.getAttribute('data-field-label') || '';
        const val = card.getAttribute('data-field-value') || '';
        const visible = !q || lbl.includes(q) || val.includes(q);
        card.style.display = visible ? '' : 'none';
      });
    }

    function toggleDensity(scopeSel, dense) {
      const scope = document.querySelector(scopeSel);
      if (!scope) return;
      scope.querySelectorAll('.field-card').forEach((card) => {
        card.style.padding = dense ? '0.8rem' : '1.25rem';
      });
      scope.querySelectorAll('.field-value textarea').forEach((ta) => {
        ta.rows = dense ? 2 : 4;
      });
    }

    // Delegated events for all modals
    document.addEventListener('click', function (e) {
      const target = e.target.closest('[data-action]');
      if (!target) return;
      const action = target.getAttribute('data-action');
      if (action === 'toggle-json') {
        e.preventDefault();
        toggleJson(target.getAttribute('data-target'));
      }
      if (action === 'copy-json') {
        e.preventDefault();
        const sel = target.getAttribute('data-json-target');
        const pre = document.querySelector(sel);
        if (pre) {
          const raw = pre.getAttribute('data-json') || pre.textContent || '';
          copyTextToClipboard(raw);
          target.innerHTML = '<i class="fas fa-check"></i>';
          setTimeout(() => (target.innerHTML = '<i class="fas fa-copy"></i>'), 1200);
        }
      }
      if (action === 'download-json') {
        e.preventDefault();
        const sel = target.getAttribute('data-json-target');
        const name = target.getAttribute('data-filename') || 'response.json';
        downloadJsonFromPre(sel, name);
      }
      if (action === 'print') {
        e.preventDefault();
        window.print();
      }
      if (action === 'copy-field') {
        e.preventDefault();
        const val = target.getAttribute('data-copy-value') || '';
        copyTextToClipboard(val);
        target.innerHTML = '<i class="fas fa-check me-1"></i>Copied';
        setTimeout(() => (target.innerHTML = '<i class="fas fa-copy me-1"></i>Copy'), 1200);
      }
      if (action === 'collapse-fields' || action === 'expand-fields') {
        const scopeSel = target.getAttribute('data-scope');
        toggleDensity(scopeSel, action === 'collapse-fields');
      }
    });

    // Input search (delegated)
    document.addEventListener('input', function (e) {
      const input = e.target.closest('input[data-action="search-fields"]');
      if (!input) return;
      const scopeSel = input.getAttribute('data-scope');
      filterFields(scopeSel, input.value);
    });
  });
</script>
@endpush
@endsection