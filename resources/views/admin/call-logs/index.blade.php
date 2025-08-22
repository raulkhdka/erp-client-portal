@extends('layouts.app')

@section('title', 'Call Logs')
@section('breadcrumb')
    <span class="breadcrumb-item active">Call Logs</span>
@endsection
@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.call-logs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Record New Call
        </a>
        <a href="{{ route('admin.call-logs.call-history') }}" class="btn btn-primary ms-2">
            <i class="fas fa-plus me-2"></i>Call History
        </a>
    </div>
@endsection

@push('styles')
    <style>
        /* Table Styling */
        .enhanced-table {
            border-collapse: separate;
            border-spacing: 0;
            border: 0.5px solid #000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 100%;
            table-layout: auto;
            border-radius: 0 !important;
        }

        .table-responsive {
            background: white;
            width: 100%;
            margin: 0 auto;
        }

        /* Button Styling */
        .btn-pdf,
        .btn-excel,
        .btn-print {
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-pdf {
            background-color: #DC2626;
        }

        .btn-excel {
            background-color: #1F6D38;
        }

        .btn-print {
            background-color: #4B5EAA;
        }

        .btn-pdf:hover {
            background-color: #B91C1C;
            transform: scale(1.05);
        }

        .btn-excel:hover {
            background-color: #155724;
            transform: scale(1.05);
        }

        .btn-print:hover {
            background-color: #3B4A8A;
            transform: scale(1.05);
        }

        /* Table Borders */
        .enhanced-table thead th,
        .enhanced-table tbody td {
            border-right: 0.5px solid #000;
            border-bottom: 0.5px solid #000;
            padding: 0.5rem;
            text-align: center;
            vertical-align: middle;
        }

        .enhanced-table thead th:first-child,
        .enhanced-table tbody td:first-child {
            border-left: none;
        }

        .enhanced-table thead th:last-child,
        .enhanced-table tbody td:last-child {
            border-right: none;
        }

        .enhanced-table thead th {
            border-top: none;
        }

        .enhanced-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Table Header and Rows */
        .enhanced-table thead th {
            background: #10b981;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 0.5rem;
        }

        .enhanced-table tbody td {
            font-size: 0.875rem;
            background-color: white;
        }

        .enhanced-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        /* Status Dropdown */
        .status-dropdown .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .status-dropdown .dropdown-menu {
            position: absolute;
            min-width: 120px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 2000;
            margin-top: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        .status-dropdown .dropdown-item {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
        }

        .status-dropdown .dropdown-item:hover {
            background-color: #f0fdf4;
            color: #065f46;
        }

        /* Search Form */
        .search-form {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            max-width: 500px;
        }

        .search-form-container {
            display: flex;
            justify-content: flex-end;
            width: 100%;
        }

        .search-form .form-control {
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .search-form .btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-overlay.show {
            display: flex;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #10b981;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .enhanced-table {
                font-size: 0.8rem;
            }

            .enhanced-table thead th,
            .enhanced-table tbody td {
                padding: 0.4rem 0.25rem;
            }

            .status-dropdown .btn {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
            }
        }

        @media (max-width: 768px) {
            .enhanced-table {
                font-size: 0.75rem;
            }

            .enhanced-table thead th,
            .enhanced-table tbody td {
                padding: 0.3rem 0.2rem;
            }

            .status-dropdown .btn {
                font-size: 0.65rem;
                padding: 0.15rem 0.3rem;
            }

            .search-form {
                flex-direction: column;
                align-items: stretch;
                max-width: 100%;
            }

            .search-form .form-control,
            .search-form .btn {
                width: 100%;
            }
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 0;
        }

        .pagination .page-link {
            border-radius: 50%;
            width: 42px;
            height: 42px;
            line-height: 42px;
            text-align: center;
            padding: 0;
            color: #333;
            background-color: #f4f4f4;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .pagination .page-link:hover {
            background-color: #007bff;
            color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .modal {
            z-index: 1055 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        /* Print Styles */
        @media print {
            .btn,
            .search-form-container,
            .pagination,
            .status-dropdown .dropdown-menu {
                display: none !important;
            }

            .enhanced-table {
                border: 1px solid #000;
            }

            .enhanced-table thead th {
                background: #f0f0f0;
                color: #000;
                font-size: 10pt;
                font-weight: bold;
                padding: 8px;
            }

            .enhanced-table tbody tr:nth-child(even) {
                background: #fff;
            }

            .enhanced-table thead th:nth-child(10),
            .enhanced-table tbody td:nth-child(10) {
                display: none;
            }

            .enhanced-table tbody td:nth-child(7) .status-dropdown .btn {
                display: inline-block !important;
                background: none;
                border: none;
                color: #000;
                font-size: 10pt;
                padding: 0;
            }

            .enhanced-table tbody td:nth-child(2) {
                font-size: 10pt;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="enhanced-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.call-logs.export') }}" class="btn btn-pdf" data-bs-toggle="tooltip"
                            title="Export to PDF">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </a>
                        <a href="{{ route('admin.call-logs.export-excel') }}" class="btn btn-excel" data-bs-toggle="tooltip"
                            title="Export to Excel">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-print" data-bs-toggle="tooltip" title="Print Table">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                    <div class="search-form-container">
                        <form class="search-form">
                            <input type="text" name="search" id="search-input" class="form-control"
                                placeholder="Search by subject, description, caller, or client"
                                value="{{ request('search') }}" title="Search by subject, description, caller, or client">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-container">
                    @if ($callLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table enhanced-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-list-ol me-2"></i>SN</th>
                                        <th><i class="fas fa-calendar-alt me-2"></i>Date/Time</th>
                                        <th><i class="fas fa-comment me-2"></i>Subject</th>
                                        <th><i class="fas fa-phone me-2"></i>Caller</th>
                                        <th><i class="fas fa-phone-square me-2"></i>Type</th>
                                        <th><i class="fas fa-exclamation-circle me-2"></i>Priority</th>
                                        <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                        <th><i class="fas fa-user-tie me-2"></i>Employee</th>
                                        <th><i class="fas fa-building me-2"></i>Company</th>
                                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="call-log-table-body">
                                    @foreach ($callLogs as $callLog)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <small class="text-muted">
                                                    {!! $callLog->call_date_formatted !!}<br>
                                                    @if ($callLog->call_time)
                                                         {{ \Carbon\Carbon::parse($callLog->call_time)->format('h:i A') }}
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <strong class="truncated-content" data-bs-toggle="tooltip"
                                                    title="{{ $callLog->subject }}">
                                                    {{ Str::limit($callLog->subject, 30) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if ($callLog->caller_name)
                                                    {{ $callLog->caller_name }}<br>
                                                @endif
                                                @if ($callLog->caller_phone)
                                                    <small class="text-muted">{{ $callLog->caller_phone }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $callLog->call_type === 'incoming' ? 'success' : 'info' }}">
                                                    {{ ucfirst($callLog->call_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $callLog->priority_color }}">
                                                    {{ ucfirst($callLog->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="status-dropdown">
                                                    <button
                                                        class="btn btn-sm btn-outline-{{ $callLog->status_color }} dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown" data-bs-toggle="tooltip"
                                                        title="Change Status">
                                                        <span class="status-text">{{ $callLog->status_label }}</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach (\App\Models\CallLog::getStatusOptions() as $statusValue => $statusLabel)
                                                            @if ($statusValue != $callLog->status)
                                                                <li>
                                                                    <button type="button"
                                                                        class="dropdown-item change-status-btn"
                                                                        data-id="{{ $callLog->id }}"
                                                                        data-status="{{ $statusValue }}">
                                                                        {{ $statusLabel }}
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $callLog->employee->name }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $callLog->client ? $callLog->client->company_name : 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.call-logs.show', $callLog) }}"
                                                        class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                                        title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.call-logs.edit', $callLog) }}"
                                                        class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $callLog->id }}"
                                                        data-id="{{ $callLog->id }}" data-bs-toggle="tooltip"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="loading-overlay" id="loading-overlay">
                                <div class="spinner"></div>
                            </div>
                        </div>
                        <div class="pagination mt-4">
                            {{ $callLogs->appends(request()->query())->links() }}
                        </div>

                        @foreach ($callLogs as $callLog)
                            <div class="modal fade" id="deleteModal{{ $callLog->id }}" tabindex="-1"
                                aria-hidden="true">
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
                                            <strong>Client:</strong> {{ $callLog->client ? $callLog->client->company_name : 'N/A' }}<br>
                                            <small class="text-muted">This action cannot be undone.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-danger delete-confirm-btn"
                                                data-id="{{ $callLog->id }}">Delete Call Log</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5" id="empty-state">
                            <i class="fas fa-phone fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No call logs found</h4>
                            <p class="text-muted mb-4">Start by recording your first call.</p>
                            <a href="{{ route('admin.call-logs.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Record First Call
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Perform search via AJAX
        function performSearch() {
            const searchInput = document.getElementById('search-input').value;
            const loadingOverlay = document.getElementById('loading-overlay');
            const tableBody = document.getElementById('call-log-table-body');

            loadingOverlay.classList.add('show');

            fetch(`{{ route('admin.call-logs.index') }}?search=${encodeURIComponent(searchInputទ

            searchInput)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = '';
                    data.callLogs.data.forEach((callLog, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td><small class="text-muted">
                                <span class="nepali-date" data-date="${callLog.call_date}">${formatNepaliDate(callLog.call_date)}</span>
                                ${callLog.call_time_formatted ? ', ' + callLog.call_time_formatted : ''}
                            </small></td>
                            <td><strong class="truncated-content" title="${callLog.subject}">
                                ${callLog.subject.length > 30 ? callLog.subject.substring(0, 30) + '...' : callLog.subject}
                            </strong></td>
                            <td>
                                ${callLog.caller_name ? callLog.caller_name + '<br>' : ''}
                                ${callLog.caller_phone ? '<small class="text-muted">' + callLog.caller_phone + '</small>' : ''}
                            </td>
                            <td><span class="badge bg-${callLog.call_type === 'incoming' ? 'success' : 'info'}">
                                ${callLog.call_type.charAt(0).toUpperCase() + callLog.call_type.slice(1)}
                            </span></td>
                            <td><span class="badge bg-${callLog.priority_color}">
                                ${callLog.priority.charAt(0).toUpperCase() + callLog.priority.slice(1)}
                            </span></td>
                            <td>
                                <div class="status-dropdown">
                                    <button class="btn btn-sm btn-outline-${callLog.status_color} dropdown-toggle"
                                            type="button" data-bs-toggle="dropdown" title="Change Status">
                                        <span class="status-text">${callLog.status_label}</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        ${Object.entries({{ json_encode(\App\Models\CallLog::getStatusOptions()) }})
                                            .filter(([value]) => value != callLog.status)
                                            .map(([value, label]) => `
                                                <li>
                                                    <button type="button" class="dropdown-item change-status-btn"
                                                            data-id="${callLog.id}" data-status="${value}">
                                                        ${label}
                                                    </button>
                                                </li>
                                            `).join('')}
                                    </ul>
                                </div>
                            </td>
                            <td><small>${callLog.employee.name}</small></td>
                            <td><small>${callLog.client ? callLog.client.company_name : 'N/A'}</small></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ url('admin/call-logs') }}/${callLog.id}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ url('admin/call-logs') }}/${callLog.id}/edit" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal${callLog.id}" data-id="${callLog.id}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);

                        // Create modal for each row
                        const modal = document.createElement('div');
                        modal.className = 'modal fade';
                        modal.id = `deleteModal${callLog.id}`;
                        modal.tabIndex = -1;
                        modal.innerHTML = `
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this call log?
                                        <br><br>
                                        <strong>Subject:</strong> ${callLog.subject}<br>
                                        <strong>Client:</strong> ${callLog.client ? callLog.client.company_name : 'N/A'}<br>
                                        <small class="text-muted">This action cannot be undone.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-danger delete-confirm-btn" data-id="${callLog.id}">Delete Call Log</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(modal);
                    });

                    // Update pagination
                    document.querySelector('.pagination').innerHTML = data.pagination;

                    // Reinitialize tooltips
                    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
                        new bootstrap.Tooltip(element);
                    });

                    // Reattach status change listeners
                    document.querySelectorAll('.change-status-btn').forEach(button => {
                        button.addEventListener('click', changeStatus);
                    });

                    // Reattach delete listeners
                    document.querySelectorAll('.delete-confirm-btn').forEach(button => {
                        button.addEventListener('click', confirmDelete);
                    });

                    // Reformat Nepali dates
                    document.querySelectorAll('.nepali-date').forEach(element => {
                        const dateInt = element.dataset.date;
                        element.textContent = formatNepaliDate(dateInt);
                    });
                })
                .catch(error => {
                    console.error('Search error:', error);
                    alert('An error occurred while searching. Please try again.');
                })
                .finally(() => {
                    loadingOverlay.classList.remove('show');
                });
        }

        // Handle status change
        function changeStatus(event) {
            const button = event.currentTarget;
            const callLogId = button.dataset.id;
            const newStatus = button.dataset.status;
            const loadingOverlay = document.getElementById('loading-overlay');

            loadingOverlay.classList.add('show');

            fetch(`{{ url('admin/call-logs') }}/${callLogId}/status`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        performSearch(); // Refresh table
                    } else {
                        alert('Failed to update status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Status update error:', error);
                    alert('An error occurred while updating status. Please try again.');
                })
                .finally(() => {
                    loadingOverlay.classList.remove('show');
                });
        }

        // Handle delete confirmation
        function confirmDelete(event) {
            const button = event.currentTarget;
            const callLogId = button.dataset.id;
            const loadingOverlay = document.getElementById('loading-overlay');

            loadingOverlay.classList.add('show');

            fetch(`{{ url('admin/call-logs') }}/${callLogId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById(`deleteModal${callLogId}`).querySelector('.btn-close').click();
                        performSearch(); // Refresh table
                    } else {
                        alert('Failed to delete call log: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    alert('An error occurred while deleting the call log. Please try again.');
                })
                .finally(() => {
                    loadingOverlay.classList.remove('show');
                });
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function () {

            // Search form submission
            document.querySelector('.search-form').addEventListener('submit', function (e) {
                e.preventDefault();
                performSearch();
            });

            // Status change buttons
            document.querySelectorAll('.change-status-btn').forEach(button => {
                button.addEventListener('click', changeStatus);
            });

            // Delete confirmation buttons
            document.querySelectorAll('.delete-confirm-btn').forEach(button => {
                button.addEventListener('click', confirmDelete);
            });

            // Initialize tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
                new bootstrap.Tooltip(element);
            });
        });
    </script>
@endpush