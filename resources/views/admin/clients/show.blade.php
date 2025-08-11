@extends('layouts.app')

@section('title', $client->company_name . ' - Client Details')

@section('breadcrumb')
    <a href="{{ route('admin.clients.index') }}" class="breadcrumb-item">Clients</a>
    <span class="breadcrumb-item active">{{ $client->company_name }}</span>
@endsection

@section('actions')
    <div class="btn-group me-2">
        <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>
@endsection

@section('content')
    @push('styles')
        <style>
            :root {
                --primary-color: #10b981; /* Define primary color for consistency */
            }

            /* Modern Card */
            .card-modern {
                border: none;
                border-radius: 16px;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                max-height: 600px;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                margin-bottom: 1.5rem !important;
                background: #ffffff;
            }

            .card-modern:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            }

            /* Header */
            .card-header-modern {
                background: linear-gradient(135deg, var(--primary-color), #059669);
                color: #ffffff;
                padding: 1.25rem 1.5rem;
                border-radius: 16px 16px 0 0;
                font-weight: 600;
                font-size: 1.1rem;
                display: flex;
                align-items: center;
                z-index: 1;
                transition: background 0.3s ease;
            }

            /* Scrollable Body */
            .card-body-modern {
                padding: 1rem 1.5rem !important;
                background: #fafafa;
                flex-grow: 1;
                scrollbar-width: thin;
                scrollbar-color: var(--primary-color) #e5e7eb;
                margin-bottom: 0 !important;
            }

            .card-body-modern::-webkit-scrollbar {
                width: 6px;
            }

            .card-body-modern::-webkit-scrollbar-track {
                background: #e5e7eb;
                border-radius: 10px;
            }

            .card-body-modern::-webkit-scrollbar-thumb {
                background-color: var(--primary-color);
                border-radius: 10px;
                transition: background-color 0.3s ease;
            }

            /* Remove margin bottom on last info/contact items */
            .info-item:last-child,
            .contact-item:last-child {
                margin-bottom: 0 !important;
            }

            /* Reduce spacing between rows inside cards */
            .card-body-modern .row {
                margin-bottom: 0 !important;
            }

            /* Remove bottom margin for last rows in card-body */
            .card-body-modern .row:last-child {
                margin-bottom: 0 !important;
            }

            /* Specific margin between main sections */
            .client-info-section,
            .contact-methods-section,
            .documents-section,
            .legacy-documents-section,
            .form-responses-section {
                margin-bottom: 1rem !important;
            }

            /* Adjust inner columns vertical spacing */
            .client-info-section .col-md-6,
            .contact-methods-section .col-md-6 {
                padding-bottom: 0 !important;
                margin-bottom: 0 !important;
            }

            /* Remove hr margins if used */
            .card-body-modern hr {
                margin: 0.75rem 0;
                border-color: #e5e7eb;
            }

            /* Tighten table margins inside card-body */
            .card-body-modern table {
                margin-bottom: 0;
                border-collapse: separate;
                border-spacing: 0 0.5rem;
            }

            /* Badge styling */
            .badge-modern {
                font-size: 0.75rem;
                padding: 0.4em 0.9em;
                border-radius: 50px;
                margin-bottom: 0;
                background: var(--primary-color) !important;
                color: #ffffff;
                transition: background 0.3s ease, transform 0.2s ease;
            }

            .badge-modern:hover {
                background: #2563eb !important;
                transform: scale(1.05);
            }

            /* Contact item text styling */
            .contact-item a {
                font-size: 0.9rem; /* Smaller font size for phone and email */
                color: #1f2937;
                text-decoration: none;
                transition: color 0.2s ease;
            }

            .contact-item a:hover {
                color: var(--primary-color);
            }

            /* Client info text styling */
            .info-item {
                font-size: 0.95rem; /* Slightly smaller font size for client information */
            }

            .info-item strong {
                font-weight: 600;
                color: #1f2937;
            }

            .info-item span {
                color: #4b5563;
            }

            /* Document card */
            .document-card {
                border: none;
                border-radius: 12px;
                background: #f1f5f9;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .document-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            }

            .document-icon {
                font-size: 2.2rem;
                color: var(--primary-color);
                transition: color 0.3s ease;
            }

            /* Side Panel Sticky Scroll */
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

            /* Ensure proper layout for main content and sidebar */
            .main-content {
                position: relative;
                z-index: 1;
            }

            /* Button styling */
            .btn-outline-primary {
                border-color: var(--primary-color);
                color: var(--primary-color);
                transition: all 0.3s ease;
            }

            .btn-outline-primary:hover {
                background: var(--primary-color);
                color: #ffffff;
                transform: translateY(-2px);
            }

            .btn-outline-secondary {
                border-color: #6b7280;
                color: #6b7280;
                transition: all 0.3s ease;
            }

            .btn-outline-secondary:hover {
                background: #6b7280;
                color: #ffffff;
                transform: translateY(-2px);
            }

            /* Icon styling */
            .info-item i,
            .contact-item i {
                color: var(--primary-color);
                margin-right: 0.75rem;
                font-size: 1.1rem;
                transition: color 0.3s ease;
            }
        </style>
    @endpush

    <div class="row">
        <div class="col-lg-8 main-content">
            <!-- Client Information -->
            <div class="card card-modern mb-4 client-info-section">
                <div class="card-header card-header-modern">
                    <i class="fas fa-user-tie me-2"></i>Client Information
                </div>
                <div class="card-body card-body-modern">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <i class="fas fa-user me-2"></i>
                                <strong>Full Name:</strong> <span>{{ $client->name }}</span>
                            </div>
                            @if ($client->address)
                                <div class="info-item mb-3">
                                    <i class="fas fa-map-marked-alt me-2"></i>
                                    <strong>Address:</strong> <span>{{ $client->address }}</span>
                                </div>
                            @endif
                            @if ($client->notes)
                                <div class="info-item mb-3">
                                    <i class="fas fa-clipboard me-2"></i>
                                    <strong>Notes:</strong> <span>{{ $client->notes }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if ($client->tax_id)
                                <div class="info-item mb-3">
                                    <i class="fas fa-id-badge me-2"></i>
                                    <strong>Tax ID:</strong> <span>{{ $client->tax_id }}</span>
                                </div>
                            @endif
                            @if ($client->business_license)
                                <div class="info-item mb-3">
                                    <i class="fas fa-file-signature me-2"></i>
                                    <strong>Business License:</strong> <span>{{ $client->business_license }}</span>
                                </div>
                            @endif
                            <div class="info-item mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <strong>Member Since:</strong> <span>{{ $client->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Methods -->
            @if ($client->phones->count() > 0 || $client->emails->count() > 0)
                <div class="card card-modern mb-4 contact-methods-section">
                    <div class="card-header card-header-modern">
                        <i class="fas fa-address-card me-2"></i>Contact Methods
                    </div>
                    <div class="card-body card-body-modern">
                        <div class="row">
                            @if ($client->phones->count() > 0)
                                <div class="col-md-6">
                                    <h6 class="mb-3 fw-bold">Phone Numbers</h6>
                                    @foreach ($client->phones as $phone)
                                        <div class="contact-item mb-2">
                                            <i class="fas fa-phone-alt me-2"></i>
                                            <a href="tel:{{ $phone->phone }}">{{ $phone->phone }}</a>
                                            <span class="badge badge-modern ms-2">{{ ucfirst($phone->type) }}</span>
                                            @if ($phone->is_primary)
                                                <span class="badge bg-primary badge-modern ms-1">Primary</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="col-md-6">
                                <h6 class="mb-3 fw-bold">Email Addresses</h6>
                                <div class="contact-item mb-2">
                                    <i class="fas fa-envelope-open-text me-2"></i>
                                    <a href="mailto:{{ $client->user->email }}">{{ $client->user->email }}</a>
                                    <span class="badge bg-primary badge-modern ms-2">Primary</span>
                                </div>
                                @foreach ($client->emails as $email)
                                    <div class="contact-item mb-2">
                                        <i class="fas fa-envelope-open-text me-2"></i>
                                        <a href="mailto:{{ $email->email }}">{{ $email->email }}</a>
                                        <span class="badge badge-modern ms-2">{{ ucfirst($email->type) }}</span>
                                        @if ($email->is_primary)
                                            <span class="badge bg-primary badge-modern ms-1">Primary</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Documents -->
            <div class="card card-modern mb-4 documents-section">
                <div class="card-header card-header-modern d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-folder me-2"></i>Client Documents ({{ $client->newDocuments->count() }})</h6>
                    <a href="{{ route('admin.documents.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-plus me-1"></i>Upload Document
                    </a>
                </div>
                <div class="card-body card-body-modern">
                    @if ($client->newDocuments->count() > 0)
                        <div class="row">
                            @foreach ($client->newDocuments->take(6) as $document)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card document-card h-100">
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
                                                <i class="{{ $iconClass }} document-icon me-3"></i>
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-1">
                                                        <a href="{{ route('admin.documents.show', $document) }}" class="text-decoration-none text-dark">
                                                            {{ Str::limit($document->title, 25) }}
                                                        </a>
                                                    </h6>
                                                    @if ($document->category)
                                                        <span class="badge badge-modern mb-1" style="background-color: {{ $document->category->color }} !important">
                                                            {{ $document->category->name }}
                                                        </span>
                                                    @endif
                                                    <div class="small text-muted">
                                                        <div>{{ $document->formatted_file_size }}</div>
                                                        <div>{{ $document->created_at->format('M d, Y') }}</div>
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

            <!-- Legacy Documents (if any) -->
            @if ($client->documents->count() > 0)
                <div class="card card-modern mb-4 legacy-documents-section">
                    <div class="card-header card-header-modern">
                        <h6 class="mb-0"><i class="fas fa-archive me-2"></i>Legacy Documents ({{ $client->documents->count() }})</h6>
                    </div>
                    <div class="card-body card-body-modern">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Document Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($client->documents as $document)
                                        <tr>
                                            <td>{{ $document->name }}</td>
                                            <td>{{ $document->document_type ?? 'General' }}</td>
                                            <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                            <td>{{ $client->created_at->format('M d, Y') }}</td>
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
                <div class="card card-modern mb-4 form-responses-section">
                    <div class="card-header card-header-modern">
                        <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Form Submissions ({{ $client->formResponses->count() }})</h6>
                    </div>
                    <div class="card-body card-body-modern">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Form Name</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($client->formResponses as $response)
                                        <tr>
                                            <td>{{ $response->dynamicForm->name }}</td>
                                            <td>{{ $response->submitted_at->format('M d, Y g:i A') }}</td>
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
        <div class="col-lg-4 side-panel">
            <!-- Services -->
            @if (isset($client->services) && $client->services instanceof \Illuminate\Database\Eloquent\Collection && $client->services->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-briefcase me-2"></i>Services
                    </div>
                    <div class="card-body">
                        @foreach ($client->services as $service)
                            <span class="badge bg-primary me-1 mb-1" title="{{ $service->detail }}">{{ $service->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Stats -->
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
            <div class="card shadow mb-4">
                <div class="card-header">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.tasks.create', ['client_id' => $client->id]) }}" class="btn btn-outline-warning">
                            <i class="fas fa-tasks me-2"></i>Create Task
                        </a>
                        <a href="{{ route('admin.documents.create', ['client_id' => $client->id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-upload me-2"></i>Upload Document
                        </a>
                        <a href="{{ route('admin.documents.index', ['client_id' => $client->id]) }}" class="btn btn-outline-info">
                            <i class="fas fa-folder me-2"></i>View All Documents
                        </a>
                        <a href="#" class="btn btn-outline-success">
                            <i class="fas fa-clipboard-list me-2"></i>Send Form
                        </a>
                        <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-edit me-2"></i>Edit Client
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection