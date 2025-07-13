@extends('layouts.app')

@section('title', 'My Services')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">My Assigned Services</h3>
                    {{-- No "Create New Service" button here as clients have view-only access --}}
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Using $assignedServices as passed from ClientServicesController --}}
                    @if($assignedServices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Service Name</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Assigned Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedServices as $service)
                                        <tr>
                                            <td>
                                                <strong>{{ $service->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">Type {{ $service->type }}</span>
                                            </td>
                                            <td>
                                                {{-- Display full detail for clients --}}
                                                <span class="text-muted">{{ $service->detail ?? '-' }}</span>
                                            </td>
                                            <td>
                                                {{-- Assuming the pivot table has a 'status' for the assigned service --}}
                                                @if($service->pivot->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($service->pivot->status == 'inactive')
                                                    <span class="badge bg-warning">Inactive</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($service->pivot->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $service->pivot->created_at->format('M d, Y') }}</small>
                                            </td>
                                            {{-- Removed "Actions" column as clients have view-only access --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $assignedServices->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No services currently assigned to you.</h5>
                            <p class="text-muted">If you believe this is an error, please contact our team.</p>
                            {{-- No "Create First Service" button for clients --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- No specific scripts needed for view-only, but keeping the push directive --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips might still be useful for other elements, but not for service detail now.
    // var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    // var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    //     return new bootstrap.Tooltip(tooltipTriggerEl);
    // });
});
</script>
@endpush
@endsection