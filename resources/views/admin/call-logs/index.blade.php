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
        /* Enhanced Table Styling with Auto-Adjusting Sizes */
        .enhanced-table {
            border-collapse: separate !important;
            border-spacing: 0;
            border: 0.5px solid #000000 !important;
            border-radius: 12px !important;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            width: 100% !important;
            table-layout: auto !important;
            font-size: 0.75rem;
        }

        .table-responsive {
            border: 0.5px solid #000000 !important;
            border-radius: 12px !important;
            background: white;
            width: 100%;
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
            font-size: 0.7rem;
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
            font-size: 0.rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.5rem 0.5rem;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }

        .enhanced-table thead th::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .enhanced-table thead th:hover::before {
            left: 100%;
        }

        .enhanced-table tbody td {
            padding: 0.5rem 0.5rem;
            vertical-align: middle;
            text-align: center !important;
            transition: all 0.3s ease;
            background-color: white;
            word-wrap: break-word;
            font-size: 0.75rem;
        }

        .enhanced-table tbody td:nth-child(1) {
            white-space: nowrap;
        }

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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
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

        /* Allow dropdown to overflow table cells and rows */
        .enhanced-table tbody td,
        .enhanced-table tbody tr {
            overflow: visible !important;
            position: relative;
        }

        /* Status dropdown button styling */
        .status-dropdown {
            position: relative !important;
            overflow: visible !important;
        }

        .status-dropdown .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .status-dropdown .dropdown-menu {
            position: absolute !important;
            min-width: 120px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 2000 !important;
            margin-top: 0.5rem;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            animation: fadeInDropdown 0.2s ease-out;
            will-change: transform;
        }

        .status-dropdown .dropdown-menu.show {
            transform: translateY(-2px);
        }

        .status-dropdown .dropdown-item {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .status-dropdown .dropdown-item:hover {
            background-color: #f0fdf4;
            color: #065f46;
        }

        /* Dropdown animation */
        @keyframes fadeInDropdown {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1200px) {
            .enhanced-table {
                font-size: 0.875rem;
                border-radius: 12px !important;
                width: 100% !important;
            }

            .enhanced-table thead th,
            .enhanced-table tbody td {
                padding: 0.5rem 0.25rem;
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

            .table-responsive {
                border-radius: 12px !important;
                overflow: visible !important;
            }

            .status-dropdown .btn {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
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
                overflow: visible;
            }

            .enhanced-table thead th:first-child {
                border-top-left-radius: 10px;
            }

            .enhanced-table thead th:last-child {
                border-top-right-radius: 10px;
            }

            .enhanced-table tbody tr:last-child td:first-child {
                border-bottom-left-radius: 10px;
            }

            .enhanced-table tbody tr:last-child td:last-child {
                border-bottom-right-radius: 10px;
            }

            .table-responsive {
                border-radius: 10px !important;
            }

            .status-dropdown .btn {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
            }

            .status-dropdown .dropdown-menu {
                z-index: 1050;
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

            .enhanced-table thead th:first-child {
                border-top-left-radius: 8px;
            }

            .enhanced-table thead th:last-child {
                border-top-right-radius: 8px;
            }

            .enhanced-table tbody tr:last-child td:first-child {
                border-bottom-left-radius: 8px;
            }

            .enhanced-table tbody tr:last-child td:last-child {
                border-bottom-right-radius: 8px;
            }

            .table-responsive {
                border-radius: 8px !important;
            }

            .status-dropdown .btn {
                font-size: 0.65rem;
                padding: 0.15rem 0.3rem;
            }

            .status-dropdown .dropdown-menu {
                z-index: 1050;
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
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .empty-state i {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
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
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 0;
        }

        .pagination .page-item {
            transition: transform 0.2s ease;
        }

        .pagination .page-link {
            border: none;
            border-radius: 50%;
            width: 42px;
            height: 42px;
            line-height: 42px;
            text-align: center;
            padding: 0;
            font-weight: 500;
            color: #333;
            background-color: #f4f4f4;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.25s ease;
        }

        .pagination .page-link:hover {
            background-color: #007bff;
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.3);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
            font-weight: 600;
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
        }

        .pagination .page-link:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.4);
        }

        .truncated-content {
            cursor: help;
        }

        .modal-content {
            background-color: #fff !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3) !important;
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

        .icon-wrapper {
            transition: transform 0.3s ease;
        }

        .icon-wrapper:hover {
            transform: scale(1.1);
        }

        .filter-container {
            transition: height 0.3s ease, opacity 0.3s ease, margin-bottom 0.3s ease;
            overflow: hidden;
        }

        .filter-container.collapsed {
            height: 0;
            opacity: 0;
            margin-bottom: 0;
        }

        .filter-toggle-btn {
            transition: all 0.3s ease;
        }

        .filter-toggle-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .filter-toggle-btn i {
            transition: transform 0.3s ease;
            display: inline-block;
            transform-origin: center;
        }

        .filter-toggle-btn i.rotate {
            transform: rotate(180deg);
        }

        .filter-buttons {
            transition: opacity 0.3s ease;
        }

        .filter-buttons.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .filter-header.collapsed {
            margin-bottom: 0.5rem !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card enhanced-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3 filter-header" id="filterHeader">
                            <h5 class="mb-0">Filter Call Logs</h5>
                            <div>
                                <span class="filter-buttons" id="filterButtons">
                                    <button type="submit" class="btn btn-outline-primary btn-sm me-2" form="filterForm">
                                        <i class="fas fa-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('admin.call-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                </span>
                                <button class="btn btn-outline-secondary btn-sm filter-toggle-btn" id="filterToggleBtn"
                                    data-bs-toggle="tooltip" title="Toggle Filters">
                                    <i id="filterIcon" class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                        <form method="GET" action="{{ route('admin.call-logs.index') }}" id="filterContainer"
                            class="filter-container mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        @foreach (\App\Models\CallLog::getStatusOptions() as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ request('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="priority" class="form-select">
                                        <option value="">All Priorities</option>
                                        @foreach (\App\Models\CallLog::getStatusOptions() as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ request('priority') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="client_id" class="form-select client-select">
                                        <option value="">All Clients</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control" placeholder="Search..."
                                        value="{{ request('search') }}">
                                </div>
                            </div>
                        </form>

                        @if ($callLogs->count() > 0)
                            <div class="table-container">
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
                                        <tbody>
                                            @foreach ($callLogs as $callLog)
                                                <tr>
                                                    <td>
                                                        <span class="truncated-content" data-bs-toggle="tooltip"
                                                            title="Call Log #{{ $loop->iteration }}">
                                                            {{ $loop->iteration }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small
                                                            class="text-muted">{{ $callLog->call_date->format('M d, Y') }}</small><br>
                                                        <small
                                                            class="text-muted">{{ $callLog->call_date->format('h:i A') }}</small>
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
                                                            class="badge bg-{{ $callLog->call_type === 'incoming' ? 'success' : 'info' }} animated-badge">
                                                            <i
                                                                class="fas fa-{{ $callLog->call_type === 'incoming' ? 'phone' : 'phone-alt' }} me-1"></i>
                                                            {{ ucfirst($callLog->call_type) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $callLog->priority_color }} animated-badge">
                                                            {{ ucfirst($callLog->priority) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="status-dropdown">
                                                            <button
                                                                class="btn btn-sm btn-outline-{{ $callLog->status_color }} dropdown-toggle animated-badge"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false" data-bs-toggle="tooltip"
                                                                title="Change Status">
                                                                <span
                                                                    class="status-text">{{ $callLog->status_label }}</span>
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
                                                        <small>{{ $callLog->client->company_name }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.call-logs.show', $callLog) }}"
                                                                class="btn btn-sm btn-outline-primary"
                                                                data-bs-toggle="tooltip" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.call-logs.edit', $callLog) }}"
                                                                class="btn btn-sm btn-outline-secondary"
                                                                data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger delete-btn"
                                                                data-id="{{ $callLog->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal{{ $callLog->id }}"
                                                                data-bs-toggle="tooltip" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
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

                            @foreach ($callLogs as $callLog)
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $callLog->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this call log?
                                                <br><br>
                                                <strong>Subject:</strong> {{ $callLog->subject }}<br>
                                                <strong>Client:</strong> {{ $callLog->client->company_name }}<br>
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
                            <div class="text-center py-5 empty-state">
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
    </div>
@endsection

@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(tooltipTriggerEl => {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize Select2 for client dropdowns
            $('.client-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'All Clients',
                allowClear: true,
                width: '100%'
            });

            // Add loading animation to table rows
            const tableRows = document.querySelectorAll('.enhanced-table tbody tr');
            tableRows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.1}s`;
                row.style.animation = 'fadeInUp 0.6s ease-out forwards';
            });

            // Add click animation to badges and buttons
            const animatedElements = document.querySelectorAll('.animated-badge, .btn');
            animatedElements.forEach(element => {
                element.addEventListener('click', function() {
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

            // Auto-resize table
            function autoResizeTable() {
                const table = document.querySelector('.enhanced-table');
                if (table) {
                    table.style.width = '100%';
                    const containerWidth = table.parentElement.offsetWidth;
                    if (table.offsetWidth > containerWidth) {
                        table.style.width = `${containerWidth}px`;
                    }
                }
            }

            autoResizeTable();
            window.addEventListener('resize', autoResizeTable);

            // Filter toggle functionality
            const filterToggleBtn = document.getElementById('filterToggleBtn');
            const filterContainer = document.getElementById('filterContainer');
            const filterButtons = document.getElementById('filterButtons');
            const filterHeader = document.getElementById('filterHeader');
            const filterIcon = filterToggleBtn ? filterToggleBtn.querySelector('i') : null;

            if (filterToggleBtn && filterContainer && filterButtons && filterHeader && filterIcon) {
                filterToggleBtn.addEventListener('click', function() {
                    filterContainer.classList.toggle('collapsed');
                    filterButtons.classList.toggle('hidden');
                    filterHeader.classList.toggle('collapsed');
                    filterIcon.classList.toggle('rotate');
                });
            } else {
                console.warn('Filter toggle elements not found.');
            }

            // Handle dropdown menu positioning
            const dropdowns = document.querySelectorAll('.status-dropdown');
            dropdowns.forEach(dropdown => {
                const button = dropdown.querySelector('button.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');

                button.addEventListener('show.bs.dropdown', () => {
                    document.body.appendChild(menu);
                    const rect = button.getBoundingClientRect();
                    menu.style.position = 'absolute';
                    menu.style.top = `${rect.bottom + window.scrollY}px`;
                    menu.style.left = `${rect.left + window.scrollX}px`;
                    menu.style.zIndex = '3000';
                    menu.classList.add('show');
                });

                button.addEventListener('hide.bs.dropdown', () => {
                    dropdown.appendChild(menu);
                    menu.style.position = '';
                    menu.style.top = '';
                    menu.style.left = '';
                    menu.style.zIndex = '';
                    menu.classList.remove('show');
                });
            });


            // Handle status change with event delegation
            document.body.addEventListener('click', function(event) {
                const button = event.target.closest('.change-status-btn');
                if (!button) return;

                event.preventDefault();

                const callLogId = button.dataset.id;
                const newStatus = button.dataset.status;

                console.log('Call Log ID:', callLogId);
                console.log('New Status:', newStatus);

                if (!callLogId || !newStatus) {
                    console.error('Missing call log ID or status');
                    showToast('error', 'Unable to update status. Missing required information.');
                    return;
                }

                const baseUrl = window.location.origin;
                const url = `${baseUrl}/admin/call-logs/${callLogId}/status`;
                console.log('Making request to:', url);

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    showToast('error', 'Security token not found. Please refresh the page.');
                    return;
                }

                axios.post(url, {
                    status: newStatus,
                    _method: 'PATCH'
                }, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    console.log('Response:', response);

                    if (response.data.status === 'success') {
                        const dropdown = button.closest('.status-dropdown');
                        const statusText = dropdown.querySelector('.status-text');
                        const btn = dropdown.querySelector('button.dropdown-toggle');
                        const statusOptions = @json(\App\Models\CallLog::getStatusOptions());
                        const newStatusLabel = statusOptions[newStatus];

                        // Update button text
                        statusText.textContent = newStatusLabel;

                        // Update button classes based on status_color
                        btn.classList.remove('btn-outline-primary', 'btn-outline-success',
                            'btn-outline-warning', 'btn-outline-secondary');
                        btn.classList.add(`btn-outline-${response.data.status_color || 'primary'}`);

                        // Update dropdown menu items
                        const menu = dropdown.querySelector('.dropdown-menu');
                        menu.innerHTML = '';
                        Object.entries(statusOptions).forEach(([statusValue, statusLabel]) => {
                            if (statusValue !== newStatus) {
                                const li = document.createElement('li');
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'dropdown-item change-status-btn';
                                btn.dataset.id = callLogId;
                                btn.dataset.status = statusValue;
                                btn.textContent = statusLabel;
                                li.appendChild(btn);
                                menu.appendChild(li);
                            }
                        });

                        showToast('success', `Status updated to ${newStatusLabel}`);
                    } else {
                        showToast('error', response.data.message || 'Failed to update status.');
                    }
                }).catch(error => {
                    console.error('Status update error:', error);
                    console.error('Error response:', error.response);

                    let errorMessage = 'An error occurred while updating status.';
                    if (error.response) {
                        if (error.response.status === 405) {
                            errorMessage =
                                'Method not allowed. Please check the server configuration.';
                        } else if (error.response.status === 404) {
                            errorMessage = 'Call log not found.';
                        } else if (error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }
                    } else if (error.request) {
                        errorMessage = 'No response from server. Please check your connection.';
                    }

                    showToast('error', errorMessage);
                });
            });

            // Handle delete with event delegation
            document.body.addEventListener('click', function(event) {
                const button = event.target.closest('.delete-confirm-btn');
                if (!button) return;

                event.preventDefault();

                const callLogId = button.dataset.id;

                console.log('Call Log ID to delete:', callLogId);

                if (!callLogId) {
                    console.error('Missing call log ID');
                    showToast('error', 'Unable to delete call log. Missing required information.');
                    return;
                }

                const baseUrl = window.location.origin;
                const url = `${baseUrl}/admin/call-logs/${callLogId}`;
                console.log('Making delete request to:', url);

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    showToast('error', 'Security token not found. Please refresh the page.');
                    return;
                }

                axios.delete(url, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    console.log('Delete response:', response);

                    // Show toast immediately on success
                    if (response.data.status === 'success') {
                        showToast('success', response.data.message ||
                            'Call log deleted successfully.');

                        const modal = button.closest('.modal');
                        const bootstrapModal = bootstrap.Modal.getInstance(modal);
                        if (bootstrapModal) {
                            bootstrapModal.hide();
                        } else {
                            console.warn('Bootstrap modal instance not found');
                        }

                        // Find the row using a more reliable selector
                        const row = document.querySelector(`tr td button[data-id="${callLogId}"]`)
                            ?.closest('tr');
                        if (row) {
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                // Re-index serial numbers
                                const rows = document.querySelectorAll(
                                    '.enhanced-table tbody tr');
                                rows.forEach((r, index) => {
                                    const snCell = r.querySelector(
                                        'td:first-child span');
                                    if (snCell) {
                                        snCell.textContent = index + 1;
                                        snCell.title = `Call Log #${index + 1}`;
                                    }
                                });
                                if (rows.length === 0) {
                                    window.location.reload();
                                }
                            }, 300);
                        } else {
                            console.warn('Row not found for call log ID:', callLogId);
                            // Reload the page if row isn't found to ensure UI consistency
                            setTimeout(() => window.location.reload(), 300);
                        }
                    } else {
                        showToast('error', response.data.message || 'Failed to delete call log.');
                    }
                }).catch(error => {
                    console.error('Delete error:', error);
                    console.error('Error response:', error.response);

                    let errorMessage = 'An error occurred while deleting the call log.';
                    if (error.response) {
                        if (error.response.status === 405) {
                            errorMessage =
                                'Method not allowed. Please check the server configuration.';
                        } else if (error.response.status === 404) {
                            errorMessage = 'Call log not found.';
                        } else if (error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }
                    } else if (error.request) {
                        errorMessage = 'No response from server. Please check your connection.';
                    }

                    showToast('error', errorMessage);
                });
            });
        });
    </script>
@endpush
