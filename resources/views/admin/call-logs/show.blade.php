@extends('layouts.app')

@section('title', 'Call Log Details')

@section('breadcrumb')
    <a href="{{ route('admin.call-logs.index') }}">Call Logs</a>
    <span class="breadcrumb-item active">Call Details</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.call-logs.edit', $callLog) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit Call Log
        </a>
        <a href="{{ route('admin.call-logs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Call Logs
        </a>
    </div>
@endsection

@push('styles')
    <style>
        :root {
            --primary-color: #10b981;
        }

        .modern-card {
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: #f7f7f7;
            overflow: hidden;
            position: relative;
            max-height: 600px;
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem !important;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .modern-card .card-header {
            padding: 1.5rem;
            border-bottom: none;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            z-index: 1;
        }

        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 16px;
            padding: 2px;
            background: linear-gradient(135deg, rgb(187, 34, 187) 0%, rgb(42, 42, 209) 100%);
            -webkit-mask:
                linear-gradient(rgb(179, 39, 179) 0 0) content-box,
                linear-gradient(rgb(41, 41, 218) 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modern-card .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .modern-card .card-header:hover::before {
            left: 100%;
        }

        .modern-card:hover::before {
            opacity: 1;
        }

        .modern-card .card-body {
            padding: 2rem;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #e5e7eb;
        }

        .modern-card .card-body::-webkit-scrollbar {
            width: 6px;
        }

        .modern-card .card-body::-webkit-scrollbar-track {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .modern-card .card-body::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .modern-card h5,
        .modern-card h6 {
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        .modern-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #000000;
            background: #f7f7f7;
        }

        .modern-table thead th {
            background: #e5e7eb;
            color: #1f2937;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            padding: 1rem;
            text-align: left;
            border-right: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }

        .modern-table thead th:last-child {
            border-right: none;
        }

        .modern-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .modern-table tbody tr:hover {
            background-color: #e5e7eb;
        }

        .modern-table tbody td {
            padding: 1rem;
            font-size: 0.875rem;
            border-right: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }

        .modern-table tbody td:last-child {
            border-right: none;
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        .modern-table thead th:first-child {
            border-top-left-radius: 10px;
        }

        .modern-table thead th:last-child {
            border-top-right-radius: 10px;
        }

        .modern-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        .modern-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

        a.text-decoration-none {
            color: #2563eb;
            transition: color 0.2s ease;
        }

        a.text-decoration-none:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            transition: transform 0.2s ease;
        }

        .badge:hover {
            transform: scale(1.05);
        }

        .side-panel {
            position: sticky;
            top: 1.5rem;
            max-height: calc(100vh - 3rem);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #e5e7eb;
            z-index: 0;
        }

        .side-panel::-webkit-scrollbar {
            width: 6px;
        }

        .side-panel::-webkit-scrollbar-track {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .side-panel::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .main-content {
            position: relative;
            z-index: 1;
        }

        @media (max-width: 992px) {
            .modern-card {
                border-radius: 12px;
            }

            .modern-card .card-body {
                padding: 1.5rem;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 0.75rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            .modern-card {
                border-radius: 8px;
            }

            .modern-card .card-header {
                padding: 1rem;
            }

            .modern-card .card-body {
                padding: 1rem;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 0.5rem;
                font-size: 0.75rem;
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 main-content">
            <!-- Call Information -->
            <div class="card modern-card mb-4 fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-phone me-2"></i>Call Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user me-2"></i>Client:</strong>
                            <p class="text-muted">
                                @if($callLog->client)
                                    <a href="{{ route('admin.clients.show', $callLog->client->id) }}" class="text-decoration-none">{{ $callLog->client->name }}</a>
                                @else
                                    No client assigned
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user-tie me-2"></i>Employee:</strong>
                            <p class="text-muted">
                                @if($callLog->employee)
                                    <a href="{{ route('admin.employees.show', $callLog->employee->id) }}" class="text-decoration-none">{{ $callLog->employee->name }}</a>
                                @else
                                    No employee assigned
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user me-2"></i>Caller Name:</strong>
                            <p class="text-muted">{{ $callLog->caller_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-phone me-2"></i>Caller Phone:</strong>
                            <p class="text-muted">
                                @if($callLog->caller_phone)
                                    <a href="tel:{{ $callLog->caller_phone }}" class="text-decoration-none">{{ $callLog->caller_phone }}</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-tag me-2"></i>Call Type:</strong>
                            <p class="text-muted">
                                @if($callLog->call_type)
                                    <span class="badge bg-info">{{ ucfirst($callLog->call_type) }}</span>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-calendar-alt me-2"></i>Call Date:</strong>
                            <p class="text-muted">{{ $callLog->call_date ? $callLog->call_date->format('M d, Y H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-clock me-2"></i>Duration:</strong>
                            <p class="text-muted">{{ $callLog->duration_minutes ?? 0 }} minutes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call Details -->
            <div class="card modern-card mb-4 fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Call Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-toggle-on me-2"></i>Status:</strong>
                            <p class="text-muted">
                                @if($callLog->status !== null && $callLog->status_label && $callLog->status_color)
                                    <span class="badge bg-{{ $callLog->status_color }}">
                                        {{ $callLog->status_label }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="updateStatus()">
                                        Change Status
                                    </button>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-exclamation-circle me-2"></i>Priority:</strong>
                            <p class="text-muted">
                                @if($callLog->priority && $callLog->priority_color)
                                    <span class="badge bg-{{ $callLog->priority_color }}">
                                        {{ ucfirst($callLog->priority) }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-bell me-2"></i>Follow-up Required:</strong>
                            <p class="text-muted">
                                @if($callLog->follow_up_required)
                                    <span class="badge bg-warning">Yes</span>
                                    @if($callLog->follow_up_date)
                                        <br><small>Due: {{ $callLog->follow_up_date->format('M d, Y') }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-success">No</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-calendar-plus me-2"></i>Created:</strong>
                            <p class="text-muted">{{ $callLog->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-sync-alt me-2"></i>Last Updated:</strong>
                            <p class="text-muted">{{ $callLog->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject and Description -->
            <div class="card modern-card mb-4 fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-file-alt me-2"></i>Subject and Description</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Subject:</strong>
                        <p class="text-muted">{{ $callLog->subject ?? 'No subject provided' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($callLog->description ?? 'No description provided')) !!}
                        </div>
                    </div>
                    @if($callLog->notes)
                    <div class="mb-3">
                        <strong>Notes:</strong>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($callLog->notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Related Tasks -->
            <div class="card modern-card mb-4 fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-tasks me-2"></i>Related Tasks</h5>
                </div>
                <div class="card-body">
                    @if($callLog->tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table modern-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-file-alt me-2"></i>Title</th>
                                        <th><i class="fas fa-user me-2"></i>Assigned To</th>
                                        <th><i class="fas fa-exclamation-circle me-2"></i>Priority</th>
                                        <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                        <th><i class="fas fa-calendar-alt me-2"></i>Due Date</th>
                                        <th><i class="fas fa-cog me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($callLog->tasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>
                                            @if($task->assignedTo && $task->assignedTo->name)
                                                <a href="{{ route('admin.employees.show', $task->assignedTo->id) }}" class="text-decoration-none">{{ $task->assignedTo->name }}</a>
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->priority && $task->priority_color)
                                                <span class="badge bg-{{ $task->priority_color }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->status_label && $task->status_color)
                                                <span class="badge bg-{{ $task->status_color }}">
                                                    {{ $task->status_label }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</td>
                                        <td>
                                            <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
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

        <div class="col-lg-4 side-panel">
            <!-- Quick Actions -->
            <div class="card modern-card fade-in">
                <div class="card-header">
                    <h6><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.call-logs.edit', $callLog) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-2"></i>Edit Call Log
                        </a>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="updateStatus()">
                            <i class="fas fa-toggle-on me-2"></i>Change Status
                        </button>
                        <a href="{{ route('admin.call-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Call Logs
                        </a>
                    </div>
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