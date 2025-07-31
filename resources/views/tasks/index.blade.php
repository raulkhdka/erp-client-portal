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
                                        <th>S.N.</th>
                                        <th>Title</th>
                                        <th>Client</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created By:</th>
                                        <th>Assigned To</th>
                                        <th>Call Log</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $key => $task)
                                        <tr class="{{ $task->is_overdue ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>{{ $loop->iteration }}</strong>
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
                                            <td>{{ $task->client->name }}</td>
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
                                            <td>{{ $task->adminCreator->name ?? 'Admin' }}</td>
                                            <td>
                                                @if($task->assignedTo && $task->assignedTo->user)
                                                    {{ $task->assignedTo->name }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
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
                                                <div class="btn-group" role="group">
                                                    <!-- View Button -->
                                                    <a href="{{ route('tasks.show', $task) }}"
                                                       class="btn btn-sm btn-outline-info"
                                                       data-bs-toggle="tooltip" title="View Task">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if(Auth::user()->isAdmin())
                                                        <!-- Edit Button -->
                                                        <a href="{{ route('tasks.edit', $task) }}"
                                                           class="btn btn-sm btn-outline-warning"
                                                           data-bs-toggle="tooltip" title="Edit Task">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete Button -->
                                                        <form action="{{ route('tasks.destroy', $task) }}"
                                                              method="POST"
                                                              class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Delete Task"
                                                                    data-task-title="{{ $task->title }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the task:</p>
                <p><strong id="taskTitle"></strong></p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i>Delete Task
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

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

    // Delete confirmation functionality
    let currentForm = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    // Handle delete button clicks
    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            currentForm = this.closest('.delete-form');
            const taskTitle = this.getAttribute('data-task-title');

            document.getElementById('taskTitle').textContent = taskTitle;
            deleteModal.show();
        });
    });

    // Handle confirm delete
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (currentForm) {
            currentForm.submit();
        }
        deleteModal.hide();
    });
});
</script>
@endpush