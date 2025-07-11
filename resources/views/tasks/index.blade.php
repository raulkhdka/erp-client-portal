@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">📋 Tasks Management</h3>
                    <div>
                        @if(Auth::user()->isEmployee())
                        <a href="{{ route('tasks.my-tasks') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-user me-1"></i>My Tasks
                        </a>
                        @endif
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create Task
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filters -->
                    <form method="GET" action="{{ route('tasks.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach(\App\Models\Task::getStatusOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="priority" class="form-select">
                                    <option value="">All Priorities</option>
                                    @foreach(\App\Models\Task::getPriorityOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ request('priority') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="client_id" class="form-select client-select">
                                    <option value="">All Clients</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(Auth::user()->isAdmin())
                            <div class="col-md-3">
                                <select name="assigned_to" class="form-select">
                                    <option value="">All Employees</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
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
                                        <th>Assigned To</th>
                                        <th>Call Log</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr class="{{ $task->is_overdue ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>#{{ $task->id }}</strong>
                                                @if($task->is_overdue)
                                                    <br><small class="text-danger">Overdue</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ Str::limit($task->title, 40) }}</strong>
                                                @if(strlen($task->title) > 40)
                                                    <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $task->title }}"></i>
                                                @endif
                                            </td>
                                            <td>{{ $task->client->company_name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $task->priority_color }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->status_color }}">
                                                    {{ $task->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    {{ $task->due_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $task->assignedTo->user->name }}</td>
                                            <td>
                                                @if($task->callLog)
                                                    <a href="{{ route('call-logs.show', $task->callLog) }}"
                                                       class="badge bg-info text-decoration-none">
                                                        Call #{{ $task->callLog->id }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('tasks.show', $task) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   data-bs-toggle="tooltip" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
                            <h5 class="text-muted">No tasks found</h5>
                            <p class="text-muted">Create tasks directly or automatically from call logs.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Create Task
                                </a>
                                <a href="{{ route('call-logs.create') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-phone me-1"></i>Record a Call
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Select2 for client dropdowns
    $('.client-select').select2({
        theme: 'bootstrap-5',
        placeholder: 'All Clients',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
@endsection
