@extends('layouts.app')

@section('title', 'My Tasks')

@section('breadcrumb')
    <a href="{{ route('admin.tasks.index') }}">Tasks</a>
    <span class="breadcrumb-item active">My Tasks</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-list me-2"></i>All Tasks
        </a>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Task
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $tasks->where('status', 1)->count() }}</h4>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $tasks->where('status', 2)->count() }}</h4>
                                    <small>In Progress</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $tasks->where('is_overdue', true)->count() }}</h4>
                                    <small>Overdue</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $tasks->whereIn('status', [7, 8])->count() }}</h4>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('tasks.my-tasks') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach(\App\Models\Task::getStatusOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="priority" class="form-select">
                                    <option value="">All Priorities</option>
                                    @foreach(\App\Models\Task::getPriorityOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ request('priority') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="client_id" class="form-select client-select">
                                    <option value="">All Clients</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('tasks.my-tasks') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Client</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Source</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr class="{{ $task->is_overdue ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>#{{ $task->id }}</strong>
                                                @if($task->is_overdue)
                                                    <br><small class="text-danger">⚠️ Overdue</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ Str::limit($task->title, 40) }}</strong>
                                                @if(strlen($task->title) > 40)
                                                    <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $task->title }}"></i>
                                                @endif
                                                @if($task->description)
                                                    <br><small class="text-muted">{{ Str::limit($task->description, 60) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $task->client->company_name }}</td>
                                            <td>
                                                @php
                                                    $priorityColors = [
                                                        'low' => 'secondary',
                                                        'medium' => 'info',
                                                        'high' => 'warning',
                                                        'urgent' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $priorityColors[$task->priority] ?? 'secondary' }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        1 => 'secondary',    // pending
                                                        2 => 'primary',      // in progress
                                                        3 => 'warning',      // on hold
                                                        4 => 'danger',       // escalated
                                                        5 => 'info',         // waiting client
                                                        6 => 'warning',      // testing
                                                        7 => 'success',      // completed
                                                        8 => 'success',      // resolved
                                                        9 => 'dark'          // backlog
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                                    {{ \App\Models\Task::getStatusOptions()[$task->status] }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    {{ $task->due_date->format('M d, Y') }}
                                                    @if($task->due_date->isToday())
                                                        <br><small class="text-warning">📅 Due Today</small>
                                                    @elseif($task->due_date->isTomorrow())
                                                        <br><small class="text-info">📅 Due Tomorrow</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No due date</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->callLog)
                                                    <a href="{{ route('admin.call-logs.show', $task->callLog) }}"
                                                       class="badge bg-info text-decoration-none">
                                                        📞 Call #{{ $task->callLog->id }}
                                                    </a>
                                                @else
                                                    <span class="badge bg-primary">📋 Standalone</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.tasks.show', $task) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.tasks.edit', $task) }}"
                                                       class="btn btn-sm btn-outline-secondary"
                                                       data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tasks assigned to you yet</h5>
                            <p class="text-muted">Tasks will appear here when they are assigned to you by administrators or created from call logs.</p>
                            <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create a Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Initialize Select2 for client dropdowns
    $('.client-select').select2({
        theme: 'bootstrap-5',
        placeholder: 'All Clients',
        allowClear: true,
        width: '100%'
    });
</script>
@endpush
@endsection
