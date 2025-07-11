@extends('layouts.app')

@section('title', 'Service Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Service Details</h3>
                    <div class="btn-group">
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit Service
                        </a>
                        <a href="{{ route('services.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Services
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Name:</th>
                                    <td><strong>{{ $service->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td><span class="badge bg-info">Type {{ $service->type }}</span></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($service->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $service->created_at->format('M d, Y \a\t H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated:</th>
                                    <td>{{ $service->updated_at->format('M d, Y \a\t H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Actions</h6>
                            <div class="d-grid gap-2">
                                <form action="{{ route('services.toggle-status', $service) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $service->is_active ? 'secondary' : 'success' }} w-100">
                                        <i class="fas fa-{{ $service->is_active ? 'pause' : 'play' }} me-1"></i>
                                        {{ $service->is_active ? 'Deactivate' : 'Activate' }} Service
                                    </button>
                                </form>

                                @if($service->clients->count() == 0)
                                    <button type="button"
                                            class="btn btn-outline-danger w-100"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">
                                        <i class="fas fa-trash me-1"></i>Delete Service
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($service->detail)
                        <hr>
                        <div>
                            <h6 class="text-muted">Service Detail</h6>
                            <p class="lead">{{ $service->detail }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Assigned Clients
                        <span class="badge bg-primary">{{ $service->clients->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($service->clients->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($service->clients as $client)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="mb-1">{{ $client->name }}</h6>
                                        @if($client->email)
                                            <small class="text-muted">{{ $client->email }}</small>
                                        @endif
                                    </div>
                                    <a href="{{ route('clients.show', $client) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="View Client">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No clients assigned to this service</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($service->clients->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Usage Statistics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <h4 class="text-primary mb-1">{{ $service->clients->count() }}</h4>
                                    <small class="text-muted">Total Assignments</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($service->clients->count() == 0)
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5>Are you sure?</h5>
                        <p>You are about to delete the service <strong>"{{ $service->name }}"</strong>.</p>
                        <p class="text-muted">This action cannot be undone.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete Service
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection
