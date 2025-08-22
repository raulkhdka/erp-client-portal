@extends('layouts.app')

@section('title', 'Employees')
@section('breadcrumb')
    <span class="breadcrumb-item active">Employees</span>
@endsection
@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Employee
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

            .enhanced-table thead th:nth-child(8),
            .enhanced-table tbody td:nth-child(8) {
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
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="enhanced-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.employees.export') }}" class="btn btn-pdf" data-bs-toggle="tooltip" title="Export to PDF">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </a>
                        <a href="{{ route('admin.employees.export-excel') }}" class="btn btn-excel" data-bs-toggle="tooltip" title="Export to Excel">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-print" data-bs-toggle="tooltip" title="Print Table">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                    <div class="search-form-container">
                        <form class="search-form">
                            <input type="text" name="search" id="search-input" class="form-control"
                                placeholder="Search by name, email, department, or position" value="{{ request('search') }}" title="Search by name, email, department, or position">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-container">
                    @if ($employees->count() > 0)
                        <div class="table-responsive">
                            <table class="table enhanced-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-list-ol me-2"></i>SN</th>
                                        <th><i class="fas fa-user me-2"></i>Name</th>
                                        <th><i class="fas fa-envelope me-2"></i>Email</th>
                                        <th><i class="fas fa-building me-2"></i>Department</th>
                                        <th><i class="fas fa-briefcase me-2"></i>Position</th>
                                        <th><i class="fas fa-calendar-alt me-2"></i>Hire Date</th>
                                        <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employee-table-body">
                                    @foreach ($employees as $employee)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $employee->name }}</td>
                                            <td>{{ $employee->user->email }}</td>
                                            <td>{{ $employee->department ?? 'Not specified' }}</td>
                                            <td>{{ $employee->position }}</td>
                                            <td>{{ $employee->hire_date_formatted ?? 'N/A' }}</td>
                                            <td>
                                                <div class="status-dropdown">
                                                    <button
                                                        class="btn btn-sm btn-outline-{{ $employee->status === 'active' ? 'success' : ($employee->status === 'inactive' ? 'secondary' : 'warning') }} dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                        data-bs-toggle="tooltip" title="Change Status">
                                                        <span class="status-text">{{ ucfirst($employee->status) }}</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach (['active', 'inactive', 'terminated'] as $status)
                                                            @if ($status !== $employee->status)
                                                                <li>
                                                                    <button type="button"
                                                                        class="dropdown-item change-status-btn"
                                                                        data-id="{{ $employee->id }}"
                                                                        data-status="{{ $status }}">
                                                                        {{ ucfirst($status) }}
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.employees.show', $employee->id) }}"
                                                        class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                                        title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.employees.edit', $employee->id) }}"
                                                        class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-employee-btn"
                                                        data-bs-toggle="tooltip" title="Delete"
                                                        data-id="{{ $employee->id }}">
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
                            {{ $employees->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5" id="empty-state">
                            <i class="fas fa-user-tie fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No employees found</h4>
                            <p class="text-muted mb-4">Start by adding your first employee to the system.</p>
                            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Add First Employee
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
        function deleteEmployee(employeeId) {
            if (!employeeId || isNaN(employeeId)) {
                showToast('error', 'Invalid employee ID.');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) {
                        showToast('error', 'Security token not found.');
                        return;
                    }

                    axios.delete(`${window.location.origin}/admin/employees/${employeeId}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(response => {
                        if (response.data.status === 'success') {
                            const row = document.querySelector(`tr td button[data-id="${employeeId}"]`)?.closest('tr');
                            if (row) row.remove();
                            showToast('success', 'Employee deleted successfully.');
                        } else {
                            showToast('error', response.data.message || 'Failed to delete employee.');
                        }
                    }).catch(error => {
                        let errorMessage = 'An error occurred while deleting the employee.';
                        if (error.response) {
                            if (error.response.status === 405) errorMessage = 'Method not allowed.';
                            else if (error.response.status === 404) errorMessage = 'Employee not found.';
                            else if (error.response.data?.message) errorMessage = error.response.data.message;
                        } else if (error.request) {
                            errorMessage = 'No response from server.';
                        }
                        showToast('error', errorMessage);
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });

            // Handle status change
            document.body.addEventListener('click', function(event) {
                const button = event.target.closest('.change-status-btn');
                if (!button) return;

                event.preventDefault();
                const employeeId = button.dataset.id;
                const newStatus = button.dataset.status;

                if (!employeeId || !newStatus) {
                    showToast('error', 'Missing required information.');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    showToast('error', 'Security token not found.');
                    return;
                }

                axios.patch(`${window.location.origin}/admin/employees/${employeeId}/status`, {
                    status: newStatus
                }, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (response.data.status === 'success') {
                        const dropdown = button.closest('.status-dropdown');
                        const statusText = dropdown.querySelector('.status-text');
                        const btn = dropdown.querySelector('button.dropdown-toggle');

                        statusText.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        btn.classList.remove('btn-outline-success', 'btn-outline-secondary', 'btn-outline-warning');
                        btn.classList.add(`btn-outline-${newStatus === 'active' ? 'success' : (newStatus === 'inactive' ? 'secondary' : 'warning')}`);

                        const menu = dropdown.querySelector('.dropdown-menu');
                        menu.innerHTML = ['active', 'inactive', 'terminated'].filter(status => status !== newStatus).map(status => `
                            <li>
                                <button type="button" class="dropdown-item change-status-btn" data-id="${employeeId}" data-status="${status}">
                                    ${status.charAt(0).toUpperCase() + status.slice(1)}
                                </button>
                            </li>
                        `).join('');

                        showToast('success', `Status updated to ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`);
                    } else {
                        showToast('error', response.data.message || 'Failed to update status.');
                    }
                }).catch(error => {
                    let errorMessage = 'An error occurred while updating status.';
                    if (error.response) {
                        if (error.response.status === 405) errorMessage = 'Method not allowed.';
                        else if (error.response.status === 404) errorMessage = 'Employee not found.';
                        else if (error.response.data?.message) errorMessage = error.response.data.message;
                        else errorMessage = 'Failed to update status.';
                    } else if (error.request) {
                        errorMessage = 'No response from server.';
                    }
                    showToast('error', errorMessage);
                });
            });

            // Handle delete button clicks
            document.body.addEventListener('click', function(event) {
                const button = event.target.closest('.delete-employee-btn');
                if (!button) return;
                event.preventDefault();
                deleteEmployee(button.dataset.id);
            });

            // Real-time search
            const searchInput = document.getElementById('search-input');
            const searchForm = document.querySelector('.search-form');
            const tableBody = document.getElementById('employee-table-body');
            const tableContainer = document.querySelector('.table-container');
            const emptyState = document.getElementById('empty-state');
            const pagination = document.querySelector('.pagination');

            function performSearch() {
                const searchTerm = searchInput.value.trim();
                axios.get(`${window.location.origin}/admin/employees?search=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (response.data.employees?.data.length > 0) {
                        tableBody.innerHTML = response.data.employees.data.map((employee, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${employee.name}</td>
                                <td>${employee.user ? employee.user.email : 'N/A'}</td>
                                <td>${employee.department || 'Not specified'}</td>
                                <td>${employee.position}</td>
                                <td>${employee.hire_date_formatted || 'N/A'}</td>
                                <td>
                                    <div class="status-dropdown">
                                        <button class="btn btn-sm btn-outline-${employee.status === 'active' ? 'success' : (employee.status === 'inactive' ? 'secondary' : 'warning')} dropdown-toggle"
                                            type="button" data-bs-toggle="dropdown" data-bs-toggle="tooltip" title="Change Status">
                                            <span class="status-text">${employee.status.charAt(0).toUpperCase() + employee.status.slice(1)}</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            ${['active', 'inactive', 'terminated'].filter(status => status !== employee.status).map(status => `
                                                <li>
                                                    <button type="button" class="dropdown-item change-status-btn"
                                                        data-id="${employee.id}" data-status="${status}">
                                                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                                                    </button>
                                                </li>
                                            `).join('')}
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="${window.location.origin}/admin/employees/${employee.id}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="${window.location.origin}/admin/employees/${employee.id}/edit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-employee-btn" data-bs-toggle="tooltip" title="Delete" data-id="${employee.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                        tableContainer.style.display = 'block';
                        if (emptyState) emptyState.style.display = 'none';
                        if (pagination) pagination.innerHTML = response.data.pagination || '';
                        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
                    } else {
                        tableBody.innerHTML = '';
                        tableContainer.style.display = 'none';
                        if (emptyState) emptyState.style.display = 'block';
                        if (pagination) pagination.innerHTML = '';
                    }
                }).catch(error => {
                    showToast('error', error.response?.data?.message || 'An error occurred while searching.');
                });
            }

            searchInput.addEventListener('input', performSearch);
            searchForm.addEventListener('submit', function(event) {
                event.preventDefault();
                performSearch();
            });
        });
    </script>
@endpush