@extends('layouts.app')

@section('title', $client->company_name . ' - Client Details')

@section('breadcrumb')
    <a href="{{ route('admin.clients.index') }}" class="breadcrumb-item">Clients</a>
    <span class="breadcrumb-item active">{{ $client->company_name }}</span>
@endsection

@section('actions')
    <div class="btn-group me-2">
        <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="fas fa-trash me-2"></i>Delete
        </button>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Clients
    </a>
@endsection

@push('styles')
    <style>
        :root {
            --primary-color: #10b981;
        }

        .modern-card {
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: #f7f7f7;
            overflow: hidden;
            position: relative;
            max-height: 600px;
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem !important;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .modern-card .card-header {
            padding: 1.5rem;
            border-bottom: none;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 16px;
            padding: 2px;
            background: linear-gradient(135deg, rgb(187, 34, 187) 0%, rgb(42, 42, 209) 100%);
            -webkit-mask:
                linear-gradient(rgb(179, 39, 179) 0 0) content-box,
                linear-gradient(rgb(41, 41, 218) 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modern-card .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .modern-card .card-header:hover::before {
            left: 100%;
        }

        .modern-card:hover::before {
            opacity: 1;
        }

        .modern-card .card-body {
            padding: 2rem;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #e5e7eb;
        }

        .modern-card .card-body::-webkit-scrollbar {
            width: 6px;
        }

        .modern-card .card-body::-webkit-scrollbar-track {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .modern-card .card-body::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .modern-card h5,
        .modern-card h6 {
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        .modern-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #000000;
            background: #f7f7f7;
        }

        .modern-table thead th {
            background: #e5e7eb;
            color: #1f2937;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            padding: 1rem;
            text-align: left;
            border-right: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }

        .modern-table thead th:last-child {
            border-right: none;
        }

        .modern-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .modern-table tbody tr:hover {
            background-color: #e5e7eb;
        }

        .modern-table tbody td {
            padding: 1rem;
            font-size: 0.875rem;
            border-right: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }

        .modern-table tbody td:last-child {
            border-right: none;
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        .modern-table thead th:first-child {
            border-top-left-radius: 10px;
        }

        .modern-table thead th:last-child {
            border-top-right-radius: 10px;
        }

        .modern-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        .modern-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

        a.text-decoration-none {
            color: #2563eb;
            transition: color 0.2s ease;
        }

        a.text-decoration-none:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            transition: transform 0.2s ease;
        }

        .badge:hover {
            transform: scale(1.05);
        }

        .side-panel {
            position: sticky;
            top: 1.5rem;
            max-height: calc(100vh - 3rem);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #e5e7eb;
            z-index: 0;
        }

        .side-panel::-webkit-scrollbar {
            width: 6px;
        }

        .side-panel::-webkit-scrollbar-track {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .side-panel::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .main-content {
            position: relative;
            z-index: 1;
        }

        /* Nepali Date Styling */
        .nepali-date-display {
            display: inline-block;
        }

        .date-toggle {
            cursor: pointer;
            margin-left: 5px;
            color: #007bff;
            text-decoration: underline;
            font-size: 0.9em;
            border: none;
            background: none;
            padding: 0;
            transition: color 0.3s ease;
        }

        .date-toggle:hover {
            color: #0056b3;
        }

        @media (max-width: 992px) {
            .modern-card {
                border-radius: 12px;
            }

            .modern-card .card-body {
                padding: 1.5rem;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 0.75rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            .modern-card {
                border-radius: 8px;
            }

            .modern-card .card-header {
                padding: 1rem;
            }

            .modern-card .card-body {
                padding: 1rem;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 0.5rem;
                font-size: 0.75rem;
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom flex layout for main content and quick actions */
        .main-content-flex {
            display: flex;
            align-items: flex-start;
            gap: 2rem;
            width: 100%;
        }
        .main-content-section {
            flex: 1 1 0;
            min-width: 0;
        }
        .side-panel {
            width: 340px;
            max-width: 100%;
            margin-left: 0;
            position: sticky;
            top: 1.5rem;
            align-self: flex-start;
            z-index: 0;
        }
        @media (max-width: 1200px) {
            .side-panel {
                width: 280px;
            }
        }
        @media (max-width: 992px) {
            .main-content-flex {
                flex-direction: column;
                gap: 1.5rem;
            }
            .side-panel {
                width: 100%;
                position: static;
            }
        }

        /* Layout for sidebar, main content, and quick actions */
        .show-layout-flex {
            display: flex;
            align-items: flex-start;
            width: 100%;
            min-height: 100vh;
        }
        .show-layout-flex #sidebar {
            position: static;
            width: 250px;
            min-width: 250px;
            height: 100vh;
            z-index: 2;
        }
        .show-layout-flex .main-content-section {
            flex: 1 1 0;
            min-width: 0;
            padding: 0 2rem 0 2rem;
            margin-left: 0;
        }
        .show-layout-flex .side-panel {
            width: 340px;
            max-width: 100%;
            margin-left: 0;
            position: sticky;
            top: 1.5rem;
            align-self: flex-start;
            z-index: 0;
        }
        @media (max-width: 1200px) {
            .show-layout-flex .side-panel {
                width: 280px;
            }
        }
        @media (max-width: 992px) {
            .show-layout-flex {
                flex-direction: column;
            }
            .show-layout-flex #sidebar,
            .show-layout-flex .side-panel {
                width: 100%;
                position: static;
                min-width: 0;
                height: auto;
            }
            .show-layout-flex .main-content-section {
                padding: 0 1rem;
            }
        }

        /* Quick Stats Specific Styles (Preserved from Original) */
        .card.shadow {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
        }
        .card.shadow:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }
        .card-header {
            background: #f8f9fa;
            color: #1f2937;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .card-body.d-flex {
            padding: 1rem 1.5rem;
        }
        .flex-fill {
            flex: 1;
        }
        .border-end {
            border-right: 1px solid #e5e7eb;
        }
        .border-bottom {
            border-bottom: 1px solid #e5e7eb;
        }
        .text-primary {
            color: #2563eb !important;
        }
        .text-success {
            color: #10b981 !important;
        }
        .text-info {
            color: #0ea5e9 !important;
        }
        .text-warning {
            color: #f59e0b !important;
        }
        .text-muted {
            color: #6b7280 !important;
        }
        .text-center {
            text-align: center !important;
        }
        .py-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        /* Font Size Reduction for Content Section */
        .show-layout-flex h5 {
            font-size: 1.125rem; /* Reduced from 1.25rem (20px to 15px) */
        }
        .show-layout-flex h6 {
            font-size: 1rem; /* Reduced from 1.25rem (20px to 15px) */
        }
        .show-layout-flex p,
        .show-layout-flex .text-muted,
        .show-layout-flex a.text-decoration-none {
            font-size: 0.8125rem; /* Reduced from 0.9375rem (15px to 10px) */
        }
        .show-layout-flex .modern-table thead th {
            font-size: 0.75rem; /* Reduced from 0.875rem (14px to 9px) */
        }
        .show-layout-flex .modern-table tbody td {
            font-size: 0.75rem; /* Reduced from 0.875rem (14px to 9px) */
        }
        .show-layout-flex .badge {
            font-size: 0.625rem; /* Reduced from 0.75rem (12px to 7px) */
        }
        .show-layout-flex .btn-sm {
            font-size: 0.75rem; /* Reduced from 0.875rem (14px to 9px) */
        }
        .show-layout-flex .small {
            font-size: 0.6875rem; /* Reduced from 0.8125rem (13px to 8px) */
        }
        /* Adjust responsive font sizes */
        @media (max-width: 992px) {
            .show-layout-flex .modern-table thead th,
            .show-layout-flex .modern-table tbody td {
                font-size: 0.6875rem; /* Reduced from 0.8rem (12.8px to 7.8px) */
            }
        }
        @media (max-width: 768px) {
            .show-layout-flex .modern-table thead th,
            .show-layout-flex .modern-table tbody td {
                font-size: 0.625rem; /* Reduced from 0.75rem (12px to 7px) */
            }
        }
    </style>
@endpush

@section('content')
<div class="show-layout-flex">
    <div class="main-content-section">
        <!-- Client Information -->
        <div class="card modern-card mb-4 fade-in client-info-section">
            <div class="card-header">
                <h5><i class="fas fa-user-tie me-2"></i>Client Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-user me-2"></i>Full Name:</strong>
                        <p class="text-muted">{{ $client->name }}</p>
                    </div>
                    @if ($client->address)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-map-marked-alt me-2"></i>Address:</strong>
                            <p class="text-muted">{{ $client->address }}</p>
                        </div>
                    @endif
                    @if ($client->tax_id)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-id-badge me-2"></i>Tax ID:</strong>
                            <p class="text-muted">{{ $client->tax_id }}</p>
                        </div>
                    @endif
                    @if ($client->business_license)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-file-signature me-2"></i>Business License:</strong>
                            <p class="text-muted">{{ $client->business_license }}</p>
                        </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-calendar-alt me-2"></i>Member Since:</strong>
                        <p class="text-muted">
                            @if($client->created_at)
                                {!! $client->created_at_nepali_html !!}
                            @else
                                Not provided
                            @endif
                        </p>
                    </div>
                    @if ($client->notes)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-clipboard me-2"></i>Notes:</strong>
                            <p class="text-muted">{{ $client->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Methods -->
        @if ($client->phones->count() > 0 || $client->emails->count() > 0)
            <div class="card modern-card mb-4 fade-in contact-methods-section">
                <div class="card-header">
                    <h5><i class="fas fa-address-card me-2"></i>Contact Methods</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if ($client->phones->count() > 0)
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold"><i class="fas fa-phone-alt me-2"></i>Phone Numbers</h6>
                                @foreach ($client->phones as $phone)
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-phone-alt me-2"></i>
                                        <a href="tel:{{ $phone->phone }}" class="text-decoration-none">{{ $phone->phone }}</a>
                                        <span class="badge bg-primary ms-2">{{ ucfirst($phone->type) }}</span>
                                        @if ($phone->is_primary)
                                            <span class="badge bg-success ms-1">Primary</span>
                                        @endif
                                    </p>
                                @endforeach
                            </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold"><i class="fas fa-envelope-open-text me-2"></i>Email Addresses</h6>
                            <p class="text-muted mb-2">
                                <i class="fas fa-envelope-open-text me-2"></i>
                                <a href="mailto:{{ $client->user->email }}" class="text-decoration-none">{{ $client->user->email }}</a>
                                <span class="badge bg-primary ms-2">Primary</span>
                            </p>
                            @foreach ($client->emails as $email)
                                <p class="text-muted mb-2">
                                    <i class="fas fa-envelope-open-text me-2"></i>
                                    <a href="mailto:{{ $email->email }}" class="text-decoration-none">{{ $email->email }}</a>
                                    <span class="badge bg-primary ms-2">{{ ucfirst($email->type) }}</span>
                                    @if ($email->is_primary)
                                        <span class="badge bg-success ms-1">Primary</span>
                                    @endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Documents -->
        <div class="card modern-card mb-4 fade-in documents-section">
            <div class="card-header">
                <h5><i class="fas fa-folder me-2"></i>Client Documents ({{ $client->newDocuments->count() }})</h5>
                <a href="{{ route('admin.documents.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-plus me-1"></i>Upload Document
                </a>
            </div>
            <div class="card-body">
                @if ($client->newDocuments->count() > 0)
                    <div class="row">
                        @foreach ($client->newDocuments->take(6) as $document)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card modern-card h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-start">
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
                                            <i class="{{ $iconClass }} me-3" style="font-size: 2.2rem;"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1">
                                                    <a href="{{ route('admin.documents.show', $document) }}" class="text-decoration-none text-dark">
                                                        {{ Str::limit($document->title, 25) }}
                                                    </a>
                                                </h6>
                                                @if ($document->category)
                                                    <span class="badge bg-primary mb-1" style="background-color: {{ $document->category->color }} !important">
                                                        {{ $document->category->name }}
                                                    </span>
                                                @endif
                                                <div class="small text-muted">
                                                    <div>{{ $document->formatted_file_size }}</div>
                                                    <div>{!! \App\Helpers\NepaliDateHelper::auto_nepali_date($document->created_at, 'readable') !!}</div>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-sm btn-outline-primary me-1">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($client->newDocuments->count() > 6)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.documents.index', ['client_id' => $client->id]) }}" class="btn btn-outline-primary">
                                <i class="fas fa-folder-open me-2"></i>View All Documents ({{ $client->newDocuments->count() }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No documents uploaded yet</h6>
                        <p class="text-muted">Upload documents related to this client to keep everything organized.</p>
                        <a href="{{ route('admin.documents.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload First Document
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Legacy Documents -->
        @if ($client->documents->count() > 0)
            <div class="card modern-card mb-4 fade-in legacy-documents-section">
                <div class="card-header">
                    <h5><i class="fas fa-archive me-2"></i>Legacy Documents ({{ $client->documents->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-file me-2"></i>Document Name</th>
                                    <th><i class="fas fa-tag me-2"></i>Type</th>
                                    <th><i class="fas fa-weight me-2"></i>Size</th>
                                    <th><i class="fas fa-calendar-alt me-2"></i>Uploaded</th>
                                    <th><i class="fas fa-cog me-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($client->documents as $document)
                                    <tr>
                                        <td>{{ $document->name }}</td>
                                        <td>{{ $document->document_type ?? 'General' }}</td>
                                        <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                        <td>{!! \App\Helpers\NepaliDateHelper::auto_nepali_date($client->created_at, 'readable') !!}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Responses -->
        @if ($client->formResponses->count() > 0)
            <div class="card modern-card mb-4 fade-in form-responses-section">
                <div class="card-header">
                    <h5><i class="fas fa-clipboard-check me-2"></i>Form Submissions ({{ $client->formResponses->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-file-alt me-2"></i>Form Name</th>
                                    <th><i class="fas fa-calendar-check me-2"></i>Submitted</th>
                                    <th><i class="fas fa-cog me-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($client->formResponses as $response)
                                    <tr>
                                        <td>{{ $response->dynamicForm->name }}</td>
                                        <td>{!! \App\Helpers\NepaliDateHelper::auto_nepali_date($response->submitted_at, 'readable') !!}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">View Response</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Side Panel -->
    <div class="side-panel">
        <!-- Services -->
        @if (isset($client->services) && $client->services instanceof \Illuminate\Database\Eloquent\Collection && $client->services->count() > 0)
            <div class="card modern-card mb-4 fade-in">
                <div class="card-header">
                    <h6><i class="fas fa-briefcase me-2"></i>Services</h6>
                </div>
                <div class="card-body">
                    @foreach ($client->services as $service)
                        <span class="badge bg-primary me-1 mb-1" title="{{ $service->detail }}">{{ $service->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Quick Stats (Preserved Original Design) -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Quick Stats
            </div>
            <div class="card-body d-flex flex-column h-100">
                <div class="d-flex border-bottom">
                    <div class="flex-fill border-end d-flex flex-column justify-content-center text-center py-3">
                        <h4 class="text-primary mb-1">{{ $client->newDocuments->count() }}</h4>
                        <small class="text-muted">Documents</small>
                    </div>
                    <div class="flex-fill d-flex flex-column justify-content-center text-center py-3">
                        <h4 class="text-success mb-1">{{ $client->formResponses->count() }}</h4>
                        <small class="text-muted">Forms</small>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-fill border-end d-flex flex-column justify-content-center text-center py-3">
                        <h4 class="text-info mb-1">{{ $client->phones->count() }}</h4>
                        <small class="text-muted">Phones</small>
                    </div>
                    <div class="flex-fill d-flex flex-column justify-content-center text-center py-3">
                        <h4 class="text-warning mb-1">{{ $client->emails->count() + 1 }}</h4>
                        <small class="text-muted">Emails</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card modern-card fade-in">
            <div class="card-header">
                <h6><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.tasks.create', ['client_id' => $client->id]) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-tasks me-2"></i>Create Task
                    </a>
                    <a href="{{ route('admin.documents.create', ['client_id' => $client->id]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-upload me-2"></i>Upload Document
                    </a>
                    <a href="{{ route('admin.documents.index', ['client_id' => $client->id]) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-folder me-2"></i>View All Documents
                    </a>
                
                    <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit Client
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $client->company_name }}</strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone and will also delete their user account.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.clients.destroy', $client->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Client
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
