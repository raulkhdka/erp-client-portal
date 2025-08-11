@extends('layouts.app')

@section('title', 'Employee Details - ' . $employee->user->name)

@section('breadcrumb')
    <a href="{{ route('admin.employees.index') }}">Employees</a>
    <span class="breadcrumb-item active">{{ $employee->user->name }}</span>
@endsection

@section('actions')
    <div class="btn-group me-2">
        <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="fas fa-trash me-2"></i>Delete
        </button>
    </div>
    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Employees
    </a>
@endsection

@push('styles')
    <style>
        :root {
            --primary-color: #10b981;
            /* Define primary color for consistency */
        }

        /* Modern Card Styling */
        .modern-card {
            /* border: 1px solid #000000; */
            /* Black border for modern look */
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: #f7f7f7;
            /* Light grey background */
            overflow: hidden;
            position: relative;
            max-height: 600px;
            /* Set max height for scrollable cards */
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem !important;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            /* background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.2)); Gradient border effect */
        }

        .modern-card .card-header {
            padding: 1.5rem;
            border-bottom: none;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            /* Unified gradient for all headers */
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
            /* Match the card's border-radius */
            padding: 2px;
            /* This defines the thickness of the border */
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
            /* Enable vertical scrolling */
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

        /* Table Styling */
        .modern-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #000000;
            background: #f7f7f7;
            /* Light grey background */
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

        /* Links and Badges */
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

        /* Side Panel Sticky Scroll */
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

        /* Main Content */
        .main-content {
            position: relative;
            z-index: 1;
        }

        /* Responsive Design */
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

        /* Animations */
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
    <div class="row">
        <div class="col-lg-8 main-content">
            <!-- Employee Information -->
            <div class="card modern-card mb-4 fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-user-tie me-2"></i>Employee Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user me-2"></i>Full Name:</strong>
                            <p class="text-muted">{{ $employee->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-id-badge me-2"></i>Employee ID:</strong>
                            <p class="text-muted">{{ $employee->employee_id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-briefcase me-2"></i>Position:</strong>
                            <p class="text-muted">{{ $employee->position }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-building me-2"></i>Department:</strong>
                            <p class="text-muted">{{ $employee->department ?: 'Not assigned' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-calendar-alt me-2"></i>Hire Date:</strong>
                            <p class="text-muted">{{ $employee->hire_date->format('F j, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-money-bill-wave me-2"></i>Salary:</strong>
                            <p class="text-muted">
                                @if ($employee->salary)
                                    रु{{ number_format($employee->salary, 2) }}
                                @else
                                    Not disclosed
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Methods -->
            <div class="card modern-card mb-4 fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-address-book me-2"></i>Contact Methods</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-phone me-2"></i>Phone:</strong>
                            <p class="text-muted">
                                @if ($employee->phone)
                                    <a href="tel:{{ $employee->phone }}"
                                        class="text-decoration-none">{{ $employee->phone }}</a>
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-envelope me-2"></i>Email Address:</strong>
                            <p class="text-muted">
                                <a href="mailto:{{ $employee->user->email }}"
                                    class="text-decoration-none">{{ $employee->user->email }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Access -->
            <div class="card modern-card mb-4 fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-users me-2"></i>Client Access</h5>
                </div>
                <div class="card-body">
                    @if ($employee->accessibleClients->count() > 0)
                        <div class="table-responsive">
                            <table class="table modern-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-user me-2"></i>Client Name</th>
                                        <th><i class="fas fa-building me-2"></i>Company</th>
                                        <th><i class="fas fa-calendar-check me-2"></i>Access Granted</th>
                                        <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee->accessibleClients as $client)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.clients.show', $client->id) }}"
                                                    class="text-decoration-none">
                                                    {{ $client->name }}
                                                </a>
                                            </td>
                                            <td>{{ $client->company_name ?: '-' }}</td>
                                            <td>{{ $client->pivot->access_granted_date ? \Carbon\Carbon::parse($client->pivot->access_granted_date)->format('M j, Y') : '-' }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $client->pivot->is_active ? 'success' : 'secondary' }}">
                                                    {{ $client->pivot->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No client access assigned</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 side-panel">
            <!-- Activity Summary -->
            <div class="card modern-card mb-4 fade-in">
                <div class="card-header">
                    <h6><i class="fas fa-chart-line me-2"></i>Activity Summary</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="fas fa-users me-2"></i>Accessible Clients:</span>
                        <span class="badge bg-primary">{{ $employee->accessibleClients->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="fas fa-clock me-2"></i>Active Since:</span>
                        <span class="text-muted">{{ $employee->hire_date->diffForHumans() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="fas fa-user-plus me-2"></i>Account Created:</span>
                        <span class="text-muted">{{ $employee->user->created_at->format('M j, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-sync-alt me-2"></i>Last Updated:</span>
                        <span class="text-muted">{{ $employee->updated_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card modern-card fade-in">
                <div class="card-header">
                    <h6><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.employees.edit', $employee->id) }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-2"></i>Edit Employee
                        </a>
                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#statusModal">
                            <i class="fas fa-toggle-on me-2"></i>Change Status
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-envelope me-2"></i>Send Email
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-history me-2"></i>View Activity Log
                        </button>
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
                    <h5 class="modal-title" id="deleteModalLabel">Delete Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $employee->user->name }}</strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone
                        and will also delete their user account.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.employees.destroy', $employee->id) }}"
                        class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Employee
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Change Employee Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $employee->user->name }}">
                    <input type="hidden" name="email" value="{{ $employee->user->email }}">
                    <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                    <input type="hidden" name="department" value="{{ $employee->department }}">
                    <input type="hidden" name="position" value="{{ $employee->position }}">
                    <input type="hidden" name="hire_date" value="{{ $employee->hire_date->format('Y-m-d') }}">
                    <input type="hidden" name="salary" value="{{ $employee->salary }}">
                    @if ($employee->permissions)
                        @foreach ($employee->permissions as $permission)
                            <input type="hidden" name="permissions[]" value="{{ $permission }}">
                        @endforeach
                    @endif

                    <div class="modal-body">
                        <p>Current status: <span
                                class="badge bg-{{ $employee->status === 'active' ? 'success' : ($employee->status === 'inactive' ? 'warning' : 'danger') }}">{{ ucfirst($employee->status) }}</span>
                        </p>

                        <label for="status" class="form-label">New Status:</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                            <option value="terminated" {{ $employee->status === 'terminated' ? 'selected' : '' }}>
                                Terminated</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
