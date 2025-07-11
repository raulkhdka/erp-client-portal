@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Edit Task</h4>
                    <div>
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Tasks
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Task Information</h5>

                                <div class="form-group">
                                    <label for="title">Task Title *</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                           value="{{ old('title', $task->title) }}" required placeholder="Brief title for the task">
                                </div>

                                <div class="form-group">
                                    <label for="client_id">Client *</label>
                                    <select name="client_id" id="client_id" class="form-control client-select" required>
                                        <option value="">Select a client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $task->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="assigned_to">Assign To</label>
                                    <select name="assigned_to" id="assigned_to" class="form-control">
                                        <option value="">Select an employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('assigned_to', $task->assigned_to) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Leave empty to keep unassigned</small>
                                </div>

                                <div class="form-group">
                                    <label for="priority">Priority *</label>
                                    <select name="priority" id="priority" class="form-control" required>
                                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="1" {{ old('status', $task->status) == 1 ? 'selected' : '' }}>Pending</option>
                                        <option value="2" {{ old('status', $task->status) == 2 ? 'selected' : '' }}>In Progress</option>
                                        <option value="3" {{ old('status', $task->status) == 3 ? 'selected' : '' }}>On Hold</option>
                                        <option value="4" {{ old('status', $task->status) == 4 ? 'selected' : '' }}>Escalated</option>
                                        <option value="5" {{ old('status', $task->status) == 5 ? 'selected' : '' }}>Waiting for Client</option>
                                        <option value="6" {{ old('status', $task->status) == 6 ? 'selected' : '' }}>Testing</option>
                                        <option value="7" {{ old('status', $task->status) == 7 ? 'selected' : '' }}>Completed</option>
                                        <option value="8" {{ old('status', $task->status) == 8 ? 'selected' : '' }}>Resolved</option>
                                        <option value="9" {{ old('status', $task->status) == 9 ? 'selected' : '' }}>Backlog</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Scheduling & Planning</h5>

                                <!-- Date fields for status tracking -->
                                <div class="form-group" id="started_at_group" style="{{ old('status', $task->status) >= 2 ? '' : 'display: none;' }}">
                                    <label for="started_at">Started At</label>
                                    <input type="datetime-local" name="started_at" id="started_at" class="form-control"
                                           value="{{ old('started_at', $task->started_at ? $task->started_at->format('Y-m-d\TH:i') : '') }}">
                                </div>

                                <div class="form-group" id="completed_at_group" style="{{ old('status', $task->status) >= 7 ? '' : 'display: none;' }}">
                                    <label for="completed_at">Completed At</label>
                                    <input type="datetime-local" name="completed_at" id="completed_at" class="form-control"
                                           value="{{ old('completed_at', $task->completed_at ? $task->completed_at->format('Y-m-d\TH:i') : '') }}">
                                </div>

                                <!-- Task metadata -->
                                <div class="form-group">
                                    <label>Created By:</label><br>
                                    <span class="text-muted">
                                        @if($task->creator)
                                            {{ $task->creator->name }}
                                        @else
                                            System
                                        @endif
                                        on {{ $task->created_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Full Width Fields -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description *</label>
                                    <textarea name="description" id="description" class="form-control" rows="5" required
                                              placeholder="Detailed description of what needs to be done...">{{ old('description', $task->description) }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3"
                                              placeholder="Any additional notes or comments...">{{ old('notes', $task->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Task
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>

                    <!-- Task History -->
                    @if($task->updated_at != $task->created_at)
                    <hr>
                    <div class="mt-4">
                        <h6>Task History</h6>
                        <div class="bg-light p-3 rounded">
                            <small class="text-muted">
                                <strong>Created:</strong> {{ $task->created_at->format('M d, Y H:i') }}
                                @if($task->creator)
                                    by {{ $task->creator->name }}
                                @endif
                                <br>
                                <strong>Last Updated:</strong> {{ $task->updated_at->format('M d, Y H:i') }}
                                @if($task->updated_at->diffInMinutes($task->created_at) > 5)
                                    ({{ $task->updated_at->diffForHumans($task->created_at) }} after creation)
                                @endif
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Initialize Select2 for client dropdowns
$(document).ready(function() {
    $('.client-select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select a client',
        allowClear: true,
        width: '100%'
    });
});

// Show/hide date fields based on status
document.getElementById('status').addEventListener('change', function() {
    const status = parseInt(this.value);
    const startedGroup = document.getElementById('started_at_group');
    const completedGroup = document.getElementById('completed_at_group');

    // Show started_at field if status is in progress or higher
    if (status >= 2) {
        startedGroup.style.display = 'block';
    } else {
        startedGroup.style.display = 'none';
        document.getElementById('started_at').value = '';
    }

    // Show completed_at field if status is completed or resolved
    if (status >= 7) {
        completedGroup.style.display = 'block';
    } else {
        completedGroup.style.display = 'none';
        document.getElementById('completed_at').value = '';
    }
});

// Trigger the status change event on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('status').dispatchEvent(new Event('change'));
});

// Auto-fill started_at when status changes to in progress
document.getElementById('status').addEventListener('change', function() {
    const status = parseInt(this.value);
    const startedAtField = document.getElementById('started_at');

    if (status === 2 && !startedAtField.value) { // In Progress
        const now = new Date();
        const formattedDate = now.toISOString().slice(0, 16);
        startedAtField.value = formattedDate;
    }
});

// Auto-fill completed_at when status changes to completed/resolved
document.getElementById('status').addEventListener('change', function() {
    const status = parseInt(this.value);
    const completedAtField = document.getElementById('completed_at');

    if ((status === 7 || status === 8) && !completedAtField.value) { // Completed or Resolved
        const now = new Date();
        const formattedDate = now.toISOString().slice(0, 16);
        completedAtField.value = formattedDate;
    }
});
</script>
@endsection
