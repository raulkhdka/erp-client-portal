@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clients</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalClients }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Clients</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeClients }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Employees</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEmployees }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Call Logs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCallLogs }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-phone fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row of Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pending Call Logs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCallLogs }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-phone-slash fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Tasks</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTasks }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Tasks</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingTasks }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">In Progress Tasks</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inProgressTasks }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-spinner fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data Tables -->
<div class="row">
    <!-- Recent Clients -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Clients</h6>
                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentClients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentClients as $client)
                            <tr>
                                <td>{{ $client->company_name }}</td>
                                <td>{{ $client->user->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $client->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($client->status) }}
                                    </span>
                                </td>
                                <td>{{ $client->created_at->format('M d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">No clients found.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Call Logs -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-success">Recent Call Logs</h6>
                <a href="{{ route('call-logs.index') }}" class="btn btn-sm btn-success">View All</a>
            </div>
            <div class="card-body">
                @if($recentCallLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCallLogs as $callLog)
                            <tr>
                                <td>{{ $callLog->client->company_name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($callLog->subject, 20) }}</td>
                                <td>
                                    <span class="badge bg-{{ $callLog->getStatusColor() }}">
                                        {{ $callLog->getStatusText() }}
                                    </span>
                                </td>
                                <td>{{ $callLog->created_at->format('M d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">No call logs found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Tasks -->
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-info">Recent Tasks</h6>
                <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-info">View All</a>
            </div>
            <div class="card-body">
                @if($recentTasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Client</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTasks as $task)
                            <tr>
                                <td>{{ Str::limit($task->title, 30) }}</td>
                                <td>{{ $task->client->company_name ?? 'N/A' }}</td>
                                <td>
                                    @if($task->assignedEmployee)
                                        {{ $task->assignedEmployee->user->name }}
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $priorityColor = $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success');
                                    @endphp
                                    <span class="badge bg-{{ $priorityColor }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->getStatusColor() }}">
                                        {{ $task->getStatusText() }}
                                    </span>
                                </td>
                                <td>
                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                </td>
                                <td>{{ $task->created_at->format('M d') }}</td>
                                <td>
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">No tasks found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.border-left-secondary {
    border-left: 0.25rem solid #858796 !important;
}
</style>
@endpush
@endsection
