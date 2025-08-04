@extends('layouts.app')

@section('title', 'Call Log Details')

@section('breadcrumb')
    <a href="{{ route('employees.call-logs.index') }}">Call Logs</a>
    <span class="breadcrumb-item active">Call Details</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('employees.call-logs.edit', $callLog) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit Call Log
        </a>
        <a href="{{ route('employees.call-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Call Logs
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

                            <!-- Call Type -->
                            <div class="mb-3">
                                <strong>Call Type:</strong><br>
                                @if($callLog->call_type)
                                    <span class="badge bg-info">{{ ucfirst($callLog->call_type) }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
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
                                @if($callLog->status !== null && $callLog->status_label && $callLog->status_color)
                                    <span class="badge bg-{{ $callLog->status_color }}">
                                        {{ $callLog->status_label }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="updateStatus()">
                                        Change Status
                                    </button>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>

                            <!-- Priority -->
                            <div class="mb-3">
                                <strong>Priority:</strong><br>
                                @if($callLog->priority && $callLog->priority_color)
                                    <span class="badge bg-{{ $callLog->priority_color }}">
                                        {{ ucfirst($callLog->priority) }}
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong>Follow-up Required:</strong><br>
                                @if($callLog->follow_up_required)
                                    <span class="badge bg-warning">Yes</span>
                                    @if($callLog->follow_up_date)
                                        <br><small>Due: {{ $callLog->follow_up_date->format('M d, Y') }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-success">No</span>
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
                <h5 class="modal-title" id="statusModalLabel">Update Call Log Status</h5>
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
                            @foreach(\App\Models\CallLog::getStatusOptions() as $value => $label)
                                <option value="{{ $value }}" {{ $callLog->status == $value ? 'selected' : '' }}>
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
@endsection

@section('scripts')
<script>
function updateStatus() {
    $('#statusModal').modal('show');
}

function saveStatus() {
    const status = document.getElementById('status').value;

    fetch(`{{ route('admin.call-logs.update-status', $callLog) }}`, {
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
