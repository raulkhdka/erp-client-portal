@extends('layouts.app')

@section('title', 'My Call Logs')

@section('breadcrumb')
    <span class="breadcrumb-item active">My Call Logs</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('employees.call-logs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Record New Call
        </a>
    </div>
@endsection

@push('styles')
<style>
    /* Enhanced Table Styling with Auto-Adjusting Sizes */
    .enhanced-table {
        border-collapse: separate !important;
        border-spacing: 0;
        border: 0.5px solid #000000 !important;
        border-radius: 12px !important;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        width: 100% !important; /* Full width to fit container */
        table-layout: fixed !important; /* Fixed layout to control column widths */
        min-width: 0;
    }

    .table-responsive {
        border: 0.5px solid #000000 !important;
        border-radius: 12px !important;
        /* Remove overflow: auto to avoid scrolling */
        background: white;
        width: 100%; /* Full width of container */
        margin: 0 auto;
        box-sizing: border-box;
    }

    .enhanced-table thead th:first-child,
    .enhanced-table tbody td:first-child {
        border-left: none !important;
    }

    .enhanced-table thead th:last-child,
    .enhanced-table tbody td:last-child {
        border-right: none !important;
    }

    .enhanced-table thead th {
        border-top: none !important;
        border-bottom: 0.5px solid #000000 !important;
    }

    .enhanced-table tbody tr:last-child td {
        border-bottom: none !important;
    }

    .enhanced-table thead th:first-child {
        border-top-left-radius: 12px;
    }

    .enhanced-table thead th:last-child {
        border-top-right-radius: 12px;
    }

    .enhanced-table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 12px;
    }

    .enhanced-table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 12px;
    }

    .enhanced-table thead th,
    .enhanced-table tbody td {
        border-right: 0.5px solid #000000 !important;
        border-bottom: 0.5px solid #000000 !important;
    }

    .enhanced-table thead th:last-child,
    .enhanced-table tbody td:last-child {
        border-right: none !important;
    }

    .enhanced-table tbody tr:last-child td {
        border-bottom: none !important;
    }

    .table-responsive .table {
        margin-bottom: 0 !important;
    }

    .enhanced-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center !important;
        vertical-align: middle;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.75rem 0.5rem; /* Reduced padding to save space */
        position: relative;
        overflow: hidden;
        white-space: nowrap;
        width: auto;
    }

    /* Adjusted column widths to fit within 100% */
    .enhanced-table thead th:nth-child(1) { width: 12%; min-width: 110px; } /* Date/Time */
    .enhanced-table thead th:nth-child(2) { width: 15%; min-width: 120px; } /* Client */
    .enhanced-table thead th:nth-child(3) { width: 18%; min-width: 150px; max-width: 200px; } /* Subject */
    .enhanced-table thead th:nth-child(4) { width: 12%; min-width: 100px; } /* Caller */
    .enhanced-table thead th:nth-child(5) { width: 10%; min-width: 80px; } /* Type */
    .enhanced-table thead th:nth-child(6) { width: 11%; min-width: 90px; } /* Priority */
    .enhanced-table thead th:nth-child(7) { width: 11%; min-width: 90px; } /* Status */
    .enhanced-table thead th:nth-child(8) { width: 11%; min-width: 120px; } /* Actions */

    .enhanced-table thead th::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .enhanced-table thead th:hover::before {
        left: 100%;
    }

    .enhanced-table tbody td {
        padding: 0.75rem 0.5rem; /* Reduced padding to save space */
        vertical-align: middle;
        text-align: center !important;
        transition: all 0.3s ease;
        background-color: white;
        width: auto;
        max-width: none;
        word-wrap: break-word;
    }

    .enhanced-table tbody td:nth-child(1) { width: 12%; white-space: nowrap; } /* Date/Time */
    .enhanced-table tbody td:nth-child(2) { width: 15%; text-align: center !important; } /* Client */
    .enhanced-table tbody td:nth-child(3) { width: 18%; max-width: 200px; overflow: hidden; text-overflow: ellipsis; } /* Subject */
    .enhanced-table tbody td:nth-child(4) { width: 12%; text-align: center !important; } /* Caller */
    .enhanced-table tbody td:nth-child(5) { width: 10%; text-align: center !important; } /* Type */
    .enhanced-table tbody td:nth-child(6) { width: 11%; text-align: center !important; } /* Priority */
    .enhanced-table tbody td:nth-child(7) { width: 11%; text-align: center !important; } /* Status */
    .enhanced-table tbody td:nth-child(8) { width: 11%; text-align: center !important; } /* Actions */

    .enhanced-table tbody tr {
        transition: all 0.3s ease;
        position: relative;
        height: auto;
    }

    .enhanced-table tbody tr:hover {
        background-color: #f8fafc !important;
        transform: scale(1.01);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .enhanced-table tbody tr:hover td {
        background-color: #f8fafc !important;
    }

    .enhanced-table tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }

    .enhanced-table tbody tr:nth-child(even) td {
        background-color: #f9fafb;
    }

    .enhanced-table tbody tr:nth-child(even):hover td {
        background-color: #f1f5f9 !important;
    }

    .animated-badge {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: inline-block;
        white-space: nowrap;
    }

    .animated-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .animated-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .animated-badge:hover::before {
        left: 100%;
    }

    .enhanced-table tbody td strong,
    .enhanced-table tbody td small,
    .enhanced-table tbody td span {
        display: inline-block;
        text-align: center;
    }

    @media (max-width: 1200px) {
        .enhanced-table {
            font-size: 0.875rem;
            border-radius: 12px !important;
            width: 100% !important;
        }

        .enhanced-table thead th,
        .enhanced-table tbody td {
            padding: 0.5rem 0.25rem; /* Further reduced padding for smaller screens */
        }

        .enhanced-table thead th:nth-child(1) { width: 12%; min-width: 90px; }
        .enhanced-table thead th:nth-child(2) { width: 15%; min-width: 100px; }
        .enhanced-table thead th:nth-child(3) { width: 18%; min-width: 120px; max-width: 180px; }
        .enhanced-table thead th:nth-child(4) { width: 12%; min-width: 80px; }
        .enhanced-table thead th:nth-child(5) { width: 10%; min-width: 70px; }
        .enhanced-table thead th:nth-child(6) { width: 11%; min-width: 80px; }
        .enhanced-table thead th:nth-child(7) { width: 11%; min-width: 80px; }
        .enhanced-table thead th:nth-child(8) { width: 11%; min-width: 100px; }

        .enhanced-table thead th:first-child { border-top-left-radius: 12px; }
        .enhanced-table thead th:last-child { border-top-right-radius: 12px; }
        .enhanced-table tbody tr:last-child td:first-child { border-bottom-left-radius: 12px; }
        .enhanced-table tbody tr:last-child td:last-child { border-bottom-right-radius: 12px; }

        .table-responsive {
            border-radius: 12px !important;
        }
    }

    @media (max-width: 992px) {
        .enhanced-table {
            font-size: 0.8rem;
            border-radius: 10px !important;
        }

        .enhanced-table thead th {
            font-size: 0.75rem;
            padding: 0.4rem 0.25rem;
        }

        .enhanced-table tbody td {
            padding: 0.4rem 0.25rem;
        }

        .enhanced-table thead th:nth-child(1) { width: 12%; min-width: 80px; }
        .enhanced-table thead th:nth-child(2) { width: 15%; min-width: 90px; }
        .enhanced-table thead th:nth-child(3) { width: 18%; min-width: 100px; max-width: 150px; }
        .enhanced-table thead th:nth-child(4) { width: 12%; min-width: 70px; }
        .enhanced-table thead th:nth-child(5) { width: 10%; min-width: 60px; }
        .enhanced-table thead th:nth-child(6) { width: 11%; min-width: 70px; }
        .enhanced-table thead th:nth-child(7) { width: 11%; min-width: 70px; }
        .enhanced-table thead th:nth-child(8) { width: 11%; min-width: 90px; }

        .enhanced-table thead th:first-child { border-top-left-radius: 10px; }
        .enhanced-table thead th:last-child { border-top-right-radius: 10px; }
        .enhanced-table tbody tr:last-child td:first-child { border-bottom-left-radius: 10px; }
        .enhanced-table tbody tr:last-child td:last-child { border-bottom-right-radius: 10px; }

        .table-responsive {
            border-radius: 10px !important;
        }
    }

    @media (max-width: 768px) {
        .enhanced-table {
            font-size: 0.75rem;
            border-radius: 8px !important;
        }

        .enhanced-table thead th {
            padding: 0.3rem 0.2rem;
        }

        .enhanced-table tbody td {
            padding: 0.3rem 0.2rem;
        }

        .enhanced-table thead th:nth-child(1) { width: 12%; min-width: 70px; }
        .enhanced-table thead th:nth-child(2) { width: 15%; min-width: 80px; }
        .enhanced-table thead th:nth-child(3) { width: 18%; min-width: 90px; max-width: 120px; }
        .enhanced-table thead th:nth-child(4) { width: 12%; min-width: 60px; }
        .enhanced-table thead th:nth-child(5) { width: 10%; min-width: 50px; }
        .enhanced-table thead th:nth-child(6) { width: 11%; min-width: 60px; }
        .enhanced-table thead th:nth-child(7) { width: 11%; min-width: 60px; }
        .enhanced-table thead th:nth-child(8) { width: 11%; min-width: 80px; }

        .enhanced-table thead th:first-child { border-top-left-radius: 8px; }
        .enhanced-table thead th:last-child { border-top-right-radius: 8px; }
        .enhanced-table tbody tr:last-child td:first-child { border-bottom-left-radius: 8px; }
        .enhanced-table tbody tr:last-child td:last-child { border-bottom-right-radius: 8px; }

        .table-responsive {
            border-radius: 8px !important;
        }
    }

    .table-container {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .enhanced-card {
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }

    .enhanced-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .empty-state {
        animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .empty-state i {
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }

    .pagination {
        animation: slideInUp 0.5s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .truncated-content {
        cursor: help;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card enhanced-card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filters -->
                    <form method="GET" action="{{ route('employees.call-logs.index') }}" class="mb-4">
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
                                <a href="{{ route('employees.call-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    @if($callLogs->count() > 0)
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table enhanced-table">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-calendar-alt me-2"></i>Date/Time</th>
                                            <th><i class="fas fa-users me-2"></i>Client</th>
                                            <th><i class="fas fa-comment me-2"></i>Subject</th>
                                            <th><i class="fas fa-phone me-2"></i>Caller</th>
                                            <th><i class="fas fa-exchange-alt me-2"></i>Type</th>
                                            <th><i class="fas fa-exclamation-circle me-2"></i>Priority</th>
                                            <th><i class="fas fa-signal me-2"></i>Status</th>
                                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
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
                                                    <strong class="text-primary">{{ $callLog->client->name }}</strong>
                                                    @if($callLog->duration_minutes)
                                                        <br><small class="text-muted">Duration: {{ $callLog->duration_minutes }}min</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $isLong = strlen($callLog->subject) > 30;
                                                    @endphp
                                                    <span class="{{ $isLong ? 'truncated-content' : '' }}"
                                                          @if($isLong) data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $callLog->subject }}" @endif>
                                                        {{ $isLong ? Str::limit($callLog->subject, 30) : $callLog->subject }}
                                                    </span>
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
                                                    <span class="badge bg-{{ $callLog->call_type === 'incoming' ? 'success' : 'info' }} animated-badge">
                                                        <i class="fas fa-{{ $callLog->call_type === 'incoming' ? 'phone' : 'phone-alt' }} me-1"></i>
                                                        {{ ucfirst($callLog->call_type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $callLog->priority_color }} animated-badge">
                                                        {{ ucfirst($callLog->priority) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-{{ $callLog->status_color }} dropdown-toggle animated-badge"
                                                                type="button" data-bs-toggle="dropdown">
                                                            {{ $callLog->status_label }}
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @foreach(\App\Models\CallLog::getStatusOptions() as $statusValue => $statusLabel)
                                                                <li>
                                                                    <a class="dropdown-item {{ $callLog->status == $statusValue ? 'active' : '' }}"
                                                                       href="#" onclick="updateStatus({{ $callLog->id }}, {{ $statusValue }})">
                                                                        {{ $statusLabel }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('employees.call-logs.show', $callLog) }}"
                                                           class="btn btn-sm btn-outline-primary"
                                                           data-bs-toggle="tooltip" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('employees.call-logs.edit', $callLog) }}"
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
                                                                    Are you sure you want to delete this call log?<br><br>
                                                                    <strong>Subject:</strong> {{ $callLog->subject }}<br>
                                                                    <strong>Client:</strong> {{ $callLog->client->name }}<br>
                                                                    <small class="text-muted">This action cannot be undone.</small>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <form action="{{ route('employees.call-logs.destroy', $callLog) }}" method="POST" class="d-inline">
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

                            <div class="d-flex justify-content-center mt-4">
                                {{ $callLogs->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5 empty-state">
                            <i class="fas fa-phone fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No Call Logs Found</h4>
                            <p class="text-muted mb-4">Start by recording your first call.</p>
                            <div class="mt-4">
                                <a href="{{ route('employees.call-logs.create') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus me-2"></i>Record First Call
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
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
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add loading animation to table rows
    const tableRows = document.querySelectorAll('.enhanced-table tbody tr');
    tableRows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
        row.style.animation = 'fadeInUp 0.6s ease-out forwards';
    });

    // Add click animation to badges and status dropdown
    const badges = document.querySelectorAll('.animated-badge');
    badges.forEach(badge => {
        badge.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1.05)';
            }, 100);
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });

    // Smooth scroll for pagination
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const tableContainer = document.querySelector('.table-container');
            if (tableContainer) {
                tableContainer.style.opacity = '0.7';
                tableContainer.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    tableContainer.style.opacity = '1';
                    tableContainer.style.transform = 'translateY(0)';
                }, 300);
            }
        });
    });

    // Auto-resize table based on content
    function autoResizeTable() {
        const table = document.querySelector('.enhanced-table');
        if (table) {
            table.style.width = '100%'; /* Ensure table fits container */
            const containerWidth = table.parentElement.offsetWidth;
            if (table.offsetWidth > containerWidth) {
                table.style.width = `${containerWidth}px`;
            }
        }
    }

    autoResizeTable();
    window.addEventListener('resize', autoResizeTable);

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