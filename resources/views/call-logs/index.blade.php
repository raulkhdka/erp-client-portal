@extends('layouts.app')

@section('title', 'Call Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Call Logs Management</h3>
                    <a href="{{ route('call-logs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Record New Call
                    </a>
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

                    <!-- Filters -->
                    <form method="GET" action="{{ route('call-logs.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach(\App\Models\CallLog::getStatusOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="priority" class="form-select">
                                    <option value="">All Priorities</option>
                                    @foreach(\App\Models\CallLog::getPriorityOptions() as $value => $label)
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
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('call-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    @if($callLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>Client</th>
                                        <th>Subject</th>
                                        <th>Caller</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Employee</th>
                                        <th>Task</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($callLogs as $callLog)
                                        <tr>
                                            <td>
                                                <small class="text-muted">{{ $callLog->call_date->format('M d, Y') }}</small><br>
                                                <small class="text-muted">{{ $callLog->call_date->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $callLog->client->name }}</strong>
                                                @if($callLog->duration_minutes)
                                                    <br><small class="text-muted">Duration: {{ $callLog->duration_minutes }}min</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ Str::limit($callLog->subject, 30) }}</strong>
                                                @if(strlen($callLog->subject) > 30)
                                                    <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $callLog->subject }}"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if($callLog->caller_name)
                                                    {{ $callLog->caller_name }}<br>
                                                @endif
                                                @if($callLog->caller_phone)
                                                    <small class="text-muted">{{ $callLog->caller_phone }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $callLog->call_type === 'incoming' ? 'success' : 'info' }}">
                                                    <i class="fas fa-{{ $callLog->call_type === 'incoming' ? 'phone' : 'phone-alt' }} me-1"></i>
                                                    {{ ucfirst($callLog->call_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $callLog->priority_color }}">
                                                    {{ ucfirst($callLog->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-{{ $callLog->status_color }} dropdown-toggle"
                                                            type="button"
                                                            data-bs-toggle="dropdown">
                                                        {{ $callLog->status_label }}
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach(\App\Models\CallLog::getStatusOptions() as $statusValue => $statusLabel)
                                                            <li>
                                                                <a class="dropdown-item {{ $callLog->status == $statusValue ? 'active' : '' }}"
                                                                   href="#"
                                                                   onclick="updateStatus({{ $callLog->id }}, {{ $statusValue }})">
                                                                    {{ $statusLabel }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $callLog->employee->name }}</small>
                                            </td>
                                            <td>
                                                @if($callLog->task)
                                                    <a href="{{ route('tasks.show', $callLog->task) }}"
                                                       class="badge bg-primary text-decoration-none">
                                                        Task #{{ $callLog->task->id }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('call-logs.show', $callLog) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       data-bs-toggle="tooltip" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('call-logs.edit', $callLog) }}"
                                                       class="btn btn-sm btn-outline-warning"
                                                       data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal{{ $callLog->id }}"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $callLog->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete this call log?
                                                                <br><br>
                                                                <strong>Subject:</strong> {{ $callLog->subject }}<br>
                                                                <strong>Client:</strong> {{ $callLog->client->company_name }}<br>
                                                                <small class="text-muted">This action cannot be undone.</small>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="{{ route('call-logs.destroy', $callLog) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Delete Call Log</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $callLogs->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-phone fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No call logs found</h5>
                            <p class="text-muted">Start by recording your first call.</p>
                            <a href="{{ route('call-logs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Record First Call
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

function updateStatus(callLogId, status) {
    fetch(`/call-logs/${callLogId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
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
        alert('Network error occurred');
    });
}
</script>
@endpush
@endsection
