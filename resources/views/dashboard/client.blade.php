@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tachometer-alt me-2"></i>My Dashboard</h1>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Assigned Services</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAssignedServices }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-puzzle-piece fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Services</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeServices }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Inactive Services</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inactiveServices }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Assigned Employee Card --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Your Assigned Employee</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $assignedEmployee->user->name ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Suspended / Expired Services --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Suspended / Expired Services</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $suspendedServices + $expiredServices }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-secondary shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Documents</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDocuments }}</div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">My Services</h6>
                <a href="{{ route('client.services.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($clientServices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Status</th>
                                <th>Assigned Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientServices as $service)
                            <tr>
                                <td>{{ $service->name }}</td>
                                <td>
                                    @php
                                        $statusColor = '';
                                        switch($service->pivot->status) {
                                            case 'active': $statusColor = 'success'; break;
                                            case 'inactive': $statusColor = 'warning'; break;
                                            case 'suspended': $statusColor = 'danger'; break;
                                            case 'expired': $statusColor = 'secondary'; break;
                                            default: $statusColor = 'info'; break;
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ ucfirst($service->pivot->status) }}
                                    </span>
                                </td>
                                <td>{{ $service->pivot->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">No services found for your account.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-info">My Assigned Employees</h6>
                <a href="{{ route('client.employees.index') }}" class="btn btn-sm btn-info">View All</a>
            </div>
            <div class="card-body">
                @if($clientEmployees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Access Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientEmployees as $employee)
                            <tr>
                                <td>{{ $employee->user->name ?? 'N/A' }}</td>
                                <td>{{ $employee->user->email ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $employee->pivot->is_active ? 'success' : 'secondary' }}">
                                        {{ $employee->pivot->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">No employees assigned to your account.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-secondary">Recent Documents</h6>
                <a href="{{ route('client.documents.index') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body">
                @if($recentDocuments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Uploaded By</th>
                                <th>Upload Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDocuments as $document)
                            <tr>
                                <td>{{ $document->title }}</td>
                                <td>{{ $document->uploadedByUser->name ?? 'N/A' }}</td>
                                <td>{{ $document->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">No documents found.</p>
                @endif
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
/* Ensure these styles are included if they are not globally available */
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
.border-left-secondary { border-left: 0.25rem solid #858796 !important; }
/* Additional styles for consistency if needed */
.text-xs { font-size: 0.7rem; }
.font-weight-bold { font-weight: 700; }
.text-uppercase { text-transform: uppercase; }
.text-primary { color: #4e73df !important; }
.text-success { color: #1cc88a !important; }
.text-warning { color: #f6c23e !important; }
.text-danger { color: #e74a3b !important; }
.text-gray-300 { color: #dddfeb !important; }
.text-gray-800 { color: #5a5c69 !important; }

/* Background colors for badges (based on SB Admin 2 colors) */
.badge.bg-primary { background-color: #4e73df !important; }
.badge.bg-success { background-color: #1cc88a !important; }
.badge.bg-info { background-color: #36b9cc !important; }
.badge.bg-warning { background-color: #f6c23e !important; }
.badge.bg-danger { background-color: #e74a3b !important; }
.badge.bg-secondary { background-color: #858796 !important; }
</style>
@endpush
@endsection