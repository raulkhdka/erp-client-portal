@extends('layouts.app')

@section('title', 'Task Details')

@section('breadcrumb')
    <a href="{{ route('employees.tasks.index') }}">My Tasks</a>
    <span class="breadcrumb-item active">Task Details</span>
@endsection

@section('content')
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@section('actions')
    <div class="btn-group">
        <a href="{{ route('employees.tasks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
        </a>
    </div>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Task Information</h5>

                            <div class="mb-3">
                                <strong>Title:</strong><br>
                                {{ $task->title }}
                            </div>

                            <div class="mb-3">
                                <strong>Description:</strong><br>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($task->description ?? 'No description provided')) !!}
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Status:</strong><br>
                                <div class="d-flex align-items-center">
                                    <span id="statusBadge" class="badge bg-{{ $task->status_color }} me-2">
                                        {{ $task->status_label }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary" onclick="updateStatus()">
                                        <i class="fas fa-edit me-1"></i>Change Status
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Priority:</strong><br>
                                <span class="badge bg-{{ $task->priority_color }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Dates & Timeline</h5>

                            <div class="mb-3">
                                <strong>Due Date:</strong><br>
                                @if($task->due_date)
                                    {{ \App\Helpers\DateHelper::formatAdToBs($task->due_date, 'M d, Y') }}
                                @else
                                    <span class="text-muted">No due date set</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong>Created:</strong><br>
                                {{ \App\Helpers\DateHelper::formatAdToBs($task->created_at, 'M d, Y H:i') }}
                            </div>

                            <div class="mb-3">
                                <strong>Last Updated:</strong><br>
                                {{ \App\Helpers\DateHelper::formatAdToBs($task->updated_at, 'M d, Y H:i') }}
                            </div>

                            @if($task->completed_at)
                                <div class="mb-3">
                                    <strong>Completed:</strong><br>
                                    {{ \App\Helpers\DateHelper::formatAdToBs($task->completed_at, 'M d, Y H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($task->notes)
                    <hr>
                    <div class="mb-3">
                        <strong>Notes:</strong><br>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($task->notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Task Status</h5>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            @foreach(\App\Models\Task::getStatusOptions() as $value => $label)
                                <option value="{{ $value }}" {{ $task->status == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatus() {
    $('#statusModal').modal('show');
}

function saveStatus() {
    const status = document.getElementById('status').value;
    const statusBadge = document.getElementById('statusBadge');

    fetch(`{{ route('employees.tasks.update-status', $task) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the status badge
            statusBadge.className = `badge bg-${data.status_color} me-2`;
            statusBadge.textContent = data.status_label;

            // Close the modal
            $('#statusModal').modal('hide');

            // Show success message
            alert('Status updated successfully');

            // Refresh the page to update all status-dependent elements
            location.reload();
        } else {
            alert('Error updating status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
    });
}

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endpush
@endsection
