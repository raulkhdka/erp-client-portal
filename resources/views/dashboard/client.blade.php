@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-building me-2"></i>Client Dashboard</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Recent Documents -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Documents</h6>
            </div>
            <div class="card-body">
                @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                            @foreach($documents as $document)
                            <tr>
                                <td>{{ $document->name }}</td>
                                <td>{{ $document->document_type ?? 'General' }}</td>
                                <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                <td>{{ $document->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">Download</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">No documents uploaded yet.</p>
                @endif
            </div>
        </div>

        <!-- Form Responses -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Form Submissions</h6>
            </div>
            <div class="card-body">
                @if($formResponses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Form Name</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formResponses as $response)
                            <tr>
                                <td>{{ $response->dynamicForm->name }}</td>
                                <td>{{ $response->submitted_at->format('M d, Y g:i A') }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">No form submissions yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Client Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Company Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Company:</strong> {{ $client->company_name }}</p>
                <p><strong>Contact Person:</strong> {{ $client->user->name }}</p>
                <p><strong>Email:</strong> {{ $client->user->email }}</p>
                @if($client->address)
                <p><strong>Address:</strong> {{ $client->address }}</p>
                @endif
                @if($client->tax_id)
                <p><strong>Tax ID:</strong> {{ $client->tax_id }}</p>
                @endif
                <p><strong>Status:</strong>
                    <span class="badge bg-{{ $client->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Services -->
        @if($client->services)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Services</h6>
            </div>
            <div class="card-body">
                @foreach($client->services as $service)
                    <span class="badge bg-primary me-1 mb-1">{{ $service }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary">Upload Document</a>
                    <a href="#" class="btn btn-outline-success">Fill Form</a>
                    <a href="#" class="btn btn-outline-info">View All Documents</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
