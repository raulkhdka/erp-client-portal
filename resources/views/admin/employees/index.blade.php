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
        /* Enhanced Table Styling with Auto-Adjusting Sizes */
        .enhanced-table {
            border-collapse: separate !important;
            border-spacing: 0;
            border: 0.5px solid #000000 !important;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100% !important;
            table-layout: auto !important;
            border-radius: 0 !important;
        }

        .table-responsive {
            background: white;
            width: 100%;
            margin: 0 auto;
        }

        .btn-pdf {
            background-color: #DC2626 !important;
            color: white !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .btn-pdf:hover {
            background-color: #B91C1C !important;
            box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.06);
            transform: scale(1.05);
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
            background: #10b981 !important;
            color: white;
            text-align: center !important;
            vertical-align: middle;
            font-weight: 600;
            font-size: 0.75rem;
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
            background-color: white;
            word-wrap: break-word;
            font-size: 0.875rem;
        }

        .enhanced-table tbody td:nth-child(1) {
            white-space: nowrap;
        }

        .enhanced-table tbody tr {
            position: relative;
            height: auto;
        }

        .enhanced-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .enhanced-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
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

        .enhanced-table tbody td,
        .enhanced-table tbody tr {
            overflow: visible !important;
            position: relative;
        }

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

        .search-form {
            margin-bottom: 0.5rem;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            max-width: 500px;
        }

        /* Add to the existing styles */
        .search-form-container {
            display: flex;
            justify-content: flex-end;
            /* Aligns the content to the right */
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
            white-space: nowrap;
        }

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

        @media (max-width: 1200px) {
            .enhanced-table {
                font-size: 0.875rem;
                width: 100% !important;
            }

            .enhanced-table thead th,
            .enhanced-table tbody td {
                padding: 0.5rem 0.25rem;
            }

            .table-responsive {
                overflow: visible !important;
            }
        }

        @media (max-width: 992px) {
            .enhanced-table {
                font-size: 0.8rem;
            }

            .enhanced-table thead th {
                font-size: 0.75rem;
                padding: 0.4rem 0.25rem;
            }

            .enhanced-table tbody td {
                padding: 0.4rem 0.25rem;
                overflow: visible;
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
            }

            .enhanced-table thead th {
                padding: 0.3rem 0.2rem;
            }

            .enhanced-table tbody td {
                padding: 0.3rem 0.2rem;
            }

            .status-dropdown .btn {
                font-size: 0.65rem;
                padding: 0.15rem 0.3rem;
            }

            .status-dropdown .dropdown-menu {
                z-index: 1050;
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

        .table-container {
            animation: fadeInUp 0.6s ease-out;
            position: relative;
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
            border-radius: 0 !important;
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
            border-radius: 0 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .modal-header {
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0 !important;
        }

        .icon-wrapper {
            transition: transform 0.3s ease;
        }

        .icon-wrapper:hover {
            transform: scale(1.1);
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        .card.enhanced-card {
            border-radius: 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="enhanced-card no-radius">
            <div class="card-body">
                <!-- Search Form with PDF Button -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('admin.employees.export') }}" class="btn btn-pdf" id="export-pdf-btn"
                        data-bs-toggle="tooltip" title="Export to PDF">
                        <i class="fas fa-file-pdf me-2"></i>PDF
                    </a>
                    <form class="search-form flex-grow-1">
                        <input type="text" name="search" id="search-input" class="form-control"
                            placeholder="Search by name, email, department, or position" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i><span>Search</span>
                        </button>
                    </form>
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
                                            <td>
                                                <span class="truncated-content" data-bs-toggle="tooltip"
                                                    title="Employee #{{ $loop->iteration }}">
                                                    {{ $loop->iteration }}
                                                </span>
                                            </td>
                                            <td>{{ $employee->name }}</td>
                                            <td>{{ $employee->user->email }}</td>
                                            <td>{{ $employee->department ?? 'Not specified' }}</td>
                                            <td>{{ $employee->position }}</td>
                                            <td>
                                                {{ $employee->hire_date_formatted ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <div class="status-dropdown">
                                                    <button
                                                        class="btn btn-sm btn-outline-{{ $employee->status === 'active' ? 'success' : ($employee->status === 'inactive' ? 'secondary' : 'warning') }} dropdown-toggle animated-badge"
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

                        <div class="d-flex justify-content-center mt-4" id="pagination">
                            {{ $employees->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5 empty-state" id="empty-state">
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
        // Define deleteEmployee in global scope
        function deleteEmployee(employeeId) {
            // Validate employeeId
            if (!employeeId || isNaN(employeeId)) {
                console.error('Invalid or missing employeeId for delete:', employeeId);
                showToast('error', 'Invalid employee ID. Please ensure the employee record is correctly set up.');
                return;
            }

            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('CSRF token not found');
                        showToast('error', 'Security token not found. Please refresh the page.');
                        return;
                    }

                    // Construct the delete URL
                    const baseUrl = window.location.origin;
                    const url = `${baseUrl}/admin/employees/${employeeId}`;
                    console.log('Making DELETE request to:', url); // Debug log

                    // Send the delete request
                    axios.delete(url, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(response => {
                        console.log('Delete response:', response); // Debug log

                        if (response.data.status === 'success') {
                            // Remove the employee row from the table
                            const row = document.querySelector(`tr td button[data-id="${employeeId}"]`)
                                ?.closest('tr');
                            if (row) {
                                row.style.animation = 'fadeOut 0.5s ease-out forwards';
                                setTimeout(() => row.remove(), 500);
                            }

                            showToast('success', 'Employee deleted successfully.');
                        } else {
                            showToast('error', response.data.message || 'Failed to delete employee.');
                        }
                    }).catch(error => {
                        console.error('Delete error:', error);
                        console.error('Error response:', error.response); // Debug log

                        let errorMessage = 'An error occurred while deleting the employee.';
                        if (error.response) {
                            if (error.response.status === 405) {
                                errorMessage = 'Method not allowed. Please check the server configuration.';
                            } else if (error.response.status === 404) {
                                errorMessage = 'Employee not found.';
                            } else if (error.response.data && error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }
                        } else if (error.request) {
                            errorMessage = 'No response from server. Please check your connection.';
                        }

                        showToast('error', errorMessage);
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(tooltipTriggerEl => {
                new bootstrap.Tooltip(tooltipTriggerEl);
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

                // Retrieve employeeId and newStatus
                const employeeId = button.dataset.id;
                const newStatus = button.dataset.status;

                console.log('Employee ID:', employeeId); // Debug log
                console.log('New Status:', newStatus); // Debug log

                // Validate that we have the required data
                if (!employeeId || !newStatus) {
                    console.error('Missing employee ID or status');
                    showToast('error', 'Unable to update status. Missing required information.');
                    return;
                }

                // Construct the request URL
                const baseUrl = window.location.origin;
                const url = `${baseUrl}/admin/employees/${employeeId}/status`;
                console.log('Making request to:', url); // Debug log
                console.log('Base URL:', baseUrl); // Debug log

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    showToast('error', 'Security token not found. Please refresh the page.');
                    return;
                }

                // Send the status update request without confirmation
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
                    console.log('Response:', response); // Debug log

                    if (response.data.status === 'success') {
                        const dropdown = button.closest('.status-dropdown');
                        const statusText = dropdown.querySelector('.status-text');
                        const btn = dropdown.querySelector('button.dropdown-toggle');

                        // Update button text
                        statusText.textContent = newStatus.charAt(0).toUpperCase() + newStatus
                            .slice(1);

                        // Update button classes
                        btn.classList.remove('btn-outline-success', 'btn-outline-secondary',
                            'btn-outline-warning');
                        btn.classList.add(
                            `btn-outline-${newStatus === 'active' ? 'success' : (newStatus === 'inactive' ? 'secondary' : 'warning')}`
                        );

                        // Update dropdown menu items
                        const menu = dropdown.querySelector('.dropdown-menu');
                        menu.innerHTML = '';
                        ['active', 'inactive', 'terminated'].forEach(status => {
                            if (status !== newStatus) {
                                const li = document.createElement('li');
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'dropdown-item change-status-btn';
                                btn.dataset.id = employeeId;
                                btn.dataset.status = status;
                                btn.textContent = status.charAt(0).toUpperCase() + status
                                    .slice(1);
                                li.appendChild(btn);
                                menu.appendChild(li);
                            }
                        });

                        showToast('success',
                            `Status updated to ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`
                        );
                    } else {
                        showToast('error', response.data.message || 'Failed to update status.');
                    }
                }).catch(error => {
                    console.error('Status update error:', error);
                    console.error('Error response:', error.response); // Debug log

                    let errorMessage = 'An error occurred while updating status.';
                    if (error.response) {
                        if (error.response.status === 405) {
                            errorMessage =
                                'Method not allowed. Please check the server configuration.';
                        } else if (error.response.status === 404) {
                            errorMessage = 'Employee not found.';
                        } else if (error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }
                    } else if (error.request) {
                        errorMessage = 'No response from server. Please check your connection.';
                    }

                    showToast('error', errorMessage);
                });
            });

            // Handle delete button clicks with event delegation
            document.body.addEventListener('click', function(event) {
                const button = event.target.closest('.delete-employee-btn');
                if (!button) return;

                event.preventDefault();
                const employeeId = button.dataset.id;
                deleteEmployee(employeeId);
            });

            // Real-time search with Axios
            const searchInput = document.getElementById('search-input');
            const searchForm = document.querySelector('.search-form');
            const tableBody = document.getElementById('employee-table-body');
            const tableContainer = document.querySelector('.table-container');
            const emptyState = document.getElementById('empty-state');
            const pagination = document.getElementById('pagination');

            function performSearch() {
                const searchTerm = searchInput.value.trim();
                const baseUrl = window.location.origin;
                const url = `${baseUrl}/admin/employees?search=${encodeURIComponent(searchTerm)}`;

                axios.get(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (response.data.employees && response.data.employees.data.length > 0) {
                        // Update table body
                        tableBody.innerHTML = response.data.employees.data.map((employee, index) => `
                <tr>
                    <td>
                        <span class="truncated-content" data-bs-toggle="tooltip" title="Employee #${index + 1}">
                            ${index + 1}
                        </span>
                    </td>
                    <td>${employee.name}</td>
                    <td>${employee.user ? employee.user.email : 'N/A'}</td>
                    <td>${employee.department || 'Not specified'}</td>
                    <td>${employee.position}</td>
                    <td>${employee.hire_date_formatted || 'N/A'}</td>
                    <td>
                        <div class="status-dropdown">
                            <button class="btn btn-sm btn-outline-${employee.status === 'active' ? 'success' : (employee.status === 'inactive' ? 'secondary' : 'warning')} dropdown-toggle animated-badge"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Change Status">
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
                            <a href="${baseUrl}/admin/employees/${employee.id}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="${baseUrl}/admin/employees/${employee.id}/edit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
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

                        // Re-init tooltips
                        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                            new bootstrap.Tooltip(el);
                        });
                    } else {
                        tableBody.innerHTML = '';
                        tableContainer.style.display = 'none';
                        if (emptyState) emptyState.style.display = 'block';
                        if (pagination) pagination.innerHTML = '';
                    }
                }).catch(error => {
                    console.error('Search error:', error);
                    showToast('error', error.response?.data?.message ||
                        'An error occurred while searching.');
                });
            }

            // Trigger search immediately on typing
            searchInput.addEventListener('input', performSearch);

            // Prevent form submission
            searchForm.addEventListener('submit', function(event) {
                event.preventDefault();
                performSearch();
            });
        });
    </script>
@endpush
