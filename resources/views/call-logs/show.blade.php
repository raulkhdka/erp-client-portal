@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Call Log Details</h4>
                    <div>
                        <a href="{{ route('call-logs.edit', $callLog) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('call-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Call Information</h5>

                            <div class="mb-3">
                                <strong>Client:</strong><br>
                                @if($callLog->client)
                                    <span class="text-primary">{{ $callLog->client->name }}</span>
                                @else
                                    <span class="text-muted">No client assigned</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong>Employee:</strong><br>
                                @if($callLog->employee)
                                    {{ $callLog->employee->name }}
                                @else
                                    <span class="text-muted">No employee assigned</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong>Caller Name:</strong><br>
                                {{ $callLog->caller_name ?? 'N/A' }}
                            </div>

                            <div class="mb-3">
                                <strong>Caller Phone:</strong><br>
                                {{ $callLog->caller_phone ?? 'N/A' }}
                            </div>

                            <div class="mb-3">
                                <strong>Call Type:</strong><br>
                                <span class="badge badge-info">{{ ucfirst($callLog->call_type) }}</span>
                            </div>

                            <div class="mb-3">
                                <strong>Call Date:</strong><br>
                                {{ $callLog->call_date ? $callLog->call_date->format('M d, Y H:i') : 'N/A' }}
                            </div>

                            <div class="mb-3">
                                <strong>Duration:</strong><br>
                                {{ $callLog->duration_minutes ?? 0 }} minutes
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Call Details</h5>

                            <div class="mb-3">
                                <strong>Status:</strong><br>
                                <span class="badge badge-{{ $callLog->getStatusColor() }}">
                                    {{ $callLog->getStatusText() }}
                                </span>
                                <button class="btn btn-sm btn-outline-primary ml-2" onclick="updateStatus()">
                                    Change Status
                                </button>
                            </div>

                            <div class="mb-3">
                                <strong>Priority:</strong><br>
                                @php
                                    $priorityColor = $callLog->priority === 'high' ? 'danger' : ($callLog->priority === 'medium' ? 'warning' : 'success');
                                @endphp
                                <span class="badge badge-{{ $priorityColor }}">
                                    {{ ucfirst($callLog->priority) }}
                                </span>
                            </div>

                            <div class="mb-3">
                                <strong>Follow-up Required:</strong><br>
                                @if($callLog->follow_up_required)
                                    <span class="badge badge-warning">Yes</span>
                                    @if($callLog->follow_up_date)
                                        <br><small>Due: {{ $callLog->follow_up_date->format('M d, Y') }}</small>
                                    @endif
                                @else
                                    <span class="badge badge-success">No</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong>Created:</strong><br>
                                {{ $callLog->created_at->format('M d, Y H:i') }}
                            </div>

                            <div class="mb-3">
                                <strong>Last Updated:</strong><br>
                                {{ $callLog->updated_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Subject -->
                    <div class="mb-3">
                        <strong>Subject:</strong><br>
                        <p class="mb-0">{{ $callLog->subject ?? 'No subject provided' }}</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <strong>Description:</strong><br>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($callLog->description ?? 'No description provided')) !!}
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($callLog->notes)
                    <div class="mb-3">
                        <strong>Notes:</strong><br>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($callLog->notes)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Related Tasks -->
                    <hr>
                    <h5>Related Tasks</h5>
                    @if($callLog->tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Assigned To</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($callLog->tasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>
                                            @if($task->assignedEmployee)
                                                {{ $task->assignedEmployee->name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $priorityColor = $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success');
                                            @endphp
                                            <span class="badge badge-{{ $priorityColor }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $task->getStatusColor() }}">
                                                {{ $task->getStatusText() }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No tasks created for this call log yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Call Log Status</h5>
                <button type="button" class="close" data-dismiss="modal">
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
                            <option value="1" {{ $callLog->status == 1 ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ $callLog->status == 2 ? 'selected' : '' }}>In Progress</option>
                            <option value="3" {{ $callLog->status == 3 ? 'selected' : '' }}>On Hold</option>
                            <option value="4" {{ $callLog->status == 4 ? 'selected' : '' }}>Escalated</option>
                            <option value="5" {{ $callLog->status == 5 ? 'selected' : '' }}>Waiting for Client</option>
                            <option value="6" {{ $callLog->status == 6 ? 'selected' : '' }}>Testing</option>
                            <option value="7" {{ $callLog->status == 7 ? 'selected' : '' }}>Completed</option>
                            <option value="8" {{ $callLog->status == 8 ? 'selected' : '' }}>Resolved</option>
                            <option value="9" {{ $callLog->status == 9 ? 'selected' : '' }}>Backlog</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function updateStatus() {
    $('#statusModal').modal('show');
}

function saveStatus() {
    const status = document.getElementById('status').value;

    fetch(`{{ route('call-logs.update-status', $callLog) }}`, {
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
</script>
@endsection
