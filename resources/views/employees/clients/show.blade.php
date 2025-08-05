@extends('layouts.app')

@section('title', $client->company_name . ' - Client Details')

@section('breadcrumb')
    <a href="{{ route('employees.clients.index') }}">Assigned Clients</a>
    <span class="breadcrumb-item active">{{ $client->company_name }}</span>
@endsection

@section('actions')
    <div class="btn-group me-2">
        <a href="{{ route('employees.clients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Contact Information -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Contact Person:</strong> {{ $client->user->name ?? 'N/A' }}</p>
                        <p><strong>Primary Email:</strong> {{ $client->user->email ?? 'N/A' }}</p>
                        @if($client->address)
                        <p><strong>Address:</strong><br>{{ $client->address }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($client->tax_id)
                        <p><strong>Tax ID:</strong> {{ $client->tax_id }}</p>
                        @endif
                        @if($client->business_license)
                        <p><strong>Business License:</strong> {{ $client->business_license }}</p>
                        @endif
                        <p><strong>Status:</strong>
                            <span class="badge bg-{{ $client->status === 'active' ? 'success' : ($client->status === 'inactive' ? 'secondary' : 'warning') }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </p>
                        <p><strong>Member Since:</strong> {{ $client->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Contact Methods -->
        @if($client->phones->count() > 0 || $client->emails->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-address-book me-2"></i>Additional Contact Methods</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($client->phones->count() > 0)
                    <div class="col-md-6">
                        <h6>Phone Numbers</h6>
                        @foreach($client->phones as $phone)
                        <p class="mb-1">
                            <i class="fas fa-phone me-2"></i>{{ $phone->phone }}
                            <span class="badge bg-secondary ms-2">{{ ucfirst($phone->type) }}</span>
                            @if($phone->is_primary)
                                <span class="badge bg-primary ms-1">Primary</span>
                            @endif
                        </p>
                        @endforeach
                    </div>
                    @endif

                    @if($client->emails->count() > 0)
                    <div class="col-md-6">
                        <h6>Additional Emails</h6>
                        @foreach($client->emails as $email)
                        <p class="mb-1">
                            <i class="fas fa-envelope me-2"></i>{{ $email->email }}
                            <span class="badge bg-secondary ms-2">{{ ucfirst($email->type) }}</span>
                            @if($email->is_primary)
                                <span class="badge bg-primary ms-1">Primary</span>
                            @endif
                        </p>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Documents -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-folder me-2"></i>Client Documents ({{ $client->newDocuments->count() }})</h6>
            </div>
            <div class="card-body">
                @if($client->newDocuments->count() > 0)
                    <div class="row">
                        @foreach($client->newDocuments->take(6) as $document)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-start">
                                            @php
                                                $iconClass = match(strtolower($document->file_type)) {
                                                    'pdf' => 'fas fa-file-pdf text-danger',
                                                    'doc', 'docx' => 'fas fa-file-word text-primary',
                                                    'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                                    'ppt', 'pptx' => 'fas fa-file-powerpoint text-warning',
                                                    'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info',
                                                    'zip', 'rar' => 'fas fa-file-archive text-secondary',
                                                    default => 'fas fa-file text-muted'
                                                };
                                            @endphp
                                            <i class="{{ $iconClass }} fa-2x me-3"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1">
                                                    <a href="{{ route('employees.documents.show', $document) }}" class="text-decoration-none">
                                                        {{ Str::limit($document->title, 25) }}
                                                    </a>
                                                </h6>
                                                @if($document->category)
                                                    <span class="badge badge-sm mb-1" style="background-color: {{ $document->category->color }}">
                                                        {{ $document->category->name }}
                                                    </span>
                                                @endif
                                                <div class="small text-muted">
                                                    <div>{{ $document->formatted_file_size }}</div>
                                                    <div>{{ $document->created_at->format('M d, Y') }}</div>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-primary me-1">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('employees.documents.show', $document) }}" class="btn btn-sm btn-outline-secondary">
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
                    @if($client->newDocuments->count() > 6)
                        <div class="text-center mt-3">
                            <a href="{{ route('employees.documents.index', ['client_id' => $client->id]) }}" class="btn btn-outline-primary">
                                <i class="fas fa-folder-open me-2"></i>View All Documents ({{ $client->newDocuments->count() }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No documents uploaded yet</h6>
                        <p class="text-muted">No documents are available for this client.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Legacy Documents (if any) -->
        @if($client->documents->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-archive me-2"></i>Legacy Documents ({{ $client->documents->count() }})</h6>
            </div>
            <div class="card-body">
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
                            @foreach($client->documents as $document)
                            <tr>
                                <td>{{ $document->name }}</td>
                                <td>{{ $document->document_type ?? 'General' }}</td>
                                <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                <td>{{ $document->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-primary">Download</a>
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
        @if($client->formResponses->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Form Submissions ({{ $client->formResponses->count() }})</h6>
            </div>
            <div class="card-body">
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
                            @foreach($client->formResponses as $response)
                            <tr>
                                <td>{{ $response->dynamicForm->name }}</td>
                                <td>{{ $response->submitted_at->format('M d, Y g:i A') }}</td>
                                <td>
                                    <a href="{{ route('employees.formResponses.show', $response) }}" class="btn btn-sm btn-outline-primary">View Response</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if($client->notes)
        <!-- Notes -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $client->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Services -->
        @if(isset($client->services) && $client->services instanceof \Illuminate\Database\Eloquent\Collection && $client->services->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-briefcase me-2"></i>Services</h6>
            </div>
            <div class="card-body">
                @foreach($client->services as $service)
                    <span class="badge bg-primary me-1 mb-1" title="{{ $service->detail }}">{{ $service->name }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Quick Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $client->newDocuments->count() }}</h4>
                            <small class="text-muted">Documents</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $client->formResponses->count() }}</h4>
                        <small class="text-muted">Forms</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-info">{{ $client->phones->count() }}</h4>
                            <small class="text-muted">Phones</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning">{{ $client->emails->count() + 1 }}</h4>
                        <small class="text-muted">Emails</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection