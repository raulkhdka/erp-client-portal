@extends('layouts.app')

@section('title', 'Services')
@section('breadcrumb')
    <span class="breadcrumb-item active">Services</span>
@endsection
@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Service
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

            .enhanced-table thead th:nth-child(7),
            .enhanced-table tbody td:nth-child(7) {
                display: none;
            }

            .enhanced-table tbody td:nth-child(5) .status-dropdown .btn {
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
                @if (session('success') || session('status_update_success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') ?? session('status_update_success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error') || session('status_update_error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') ?? session('status_update_error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.services.export') }}" class="btn btn-pdf" data-bs-toggle="tooltip" title="Export to PDF">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </a>
                        <a href="{{ route('admin.services.export-excel') }}" class="btn btn-excel" data-bs-toggle="tooltip" title="Export to Excel">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-print" data-bs-toggle="tooltip" title="Print Table">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                    <div class="search-form-container">
                        <form class="search-form">
                            <input type="text" name="search" id="search-input" class="form-control" placeholder="Search by name or type" value="{{ request('search') }}" title="search by name or type">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-container">
                    @if ($services->count() > 0)
                        <div class="table-responsive">
                            <table class="table enhanced-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-list-ol me-2"></i>SN</th>
                                        <th><i class="fas fa-concierge-bell me-2"></i>Name</th>
                                        <th><i class="fas fa-tag me-2"></i>Service Type</th>
                                        <th><i class="fas fa-users me-2"></i>Clients</th>
                                        <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                        <th><i class="fas fa-calendar-alt me-2"></i>Created</th>
                                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="service-table-body">
                                    @foreach ($services as $service)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $service->name }}</td>
                                            <td><span class="badge bg-info">{{ $service->type }}</span></td>
                                            <td><span class="badge bg-secondary">{{ $service->clients_count }} clients</span></td>
                                            <td>
                                                <div class="status-dropdown">
                                                    <button class="btn btn-sm btn-outline-{{ $service->is_active ? 'success' : 'secondary' }} dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-toggle="tooltip" title="Change Status">
                                                        <span class="status-text">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach (['active', 'inactive'] as $status)
                                                            @if ($status !== ($service->is_active ? 'active' : 'inactive'))
                                                                <li>
                                                                    <button type="button" class="dropdown-item change-status-btn" data-id="{{ $service->id }}" data-status="{{ $status }}">
                                                                        {{ ucfirst($status) }}
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </td>
                                            <td><small class="text-muted">{!! $service->created_at_nepali_html !!}</small></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if ($service->clients_count == 0)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $service->id }}" data-bs-toggle="tooltip" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-danger disabled" data-bs-toggle="tooltip" title="Cannot delete - service has {{ $service->clients_count }} client(s)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
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
                            {{ $services->appends(request()->query())->links() }}
                        </div>

                        @foreach ($services as $service)
                            @if ($service->clients_count == 0)
                                <div class="modal fade" id="deleteModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete the service <strong>{{ $service->name }}</strong>?
                                                <br><br>
                                                <small class="text-muted">This action cannot be undone.</small>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete Service</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-5" id="empty-state">
                            <i class="fas fa-concierge-bell fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No services found</h4>
                            <p class="text-muted mb-4">Start by creating your first service.</p>
                            <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Create First Service
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
        function deleteService(serviceId) {
            if (!serviceId || isNaN(serviceId)) {
                showToast('error', 'Invalid service ID.');
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

                    axios.delete(`${window.location.origin}/admin/services/${serviceId}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(response => {
                        if (response.data.status === 'success') {
                            const row = document.querySelector(`tr td button[data-id="${serviceId}"]`)?.closest('tr');
                            if (row) row.remove();
                            showToast('success', 'Service deleted successfully.');
                            const modal = document.getElementById(`deleteModal${serviceId}`);
                            if (modal) bootstrap.Modal.getInstance(modal)?.hide();
                        } else {
                            showToast('error', response.data.message || 'Failed to delete service.');
                        }
                    }).catch(error => {
                        let errorMessage = 'An error occurred while deleting the service.';
                        if (error.response) {
                            if (error.response.status === 405) errorMessage = 'Method not allowed.';
                            else if (error.response.status === 404) errorMessage = 'Service not found.';
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
                const serviceId = button.dataset.id;
                const newStatus = button.dataset.status;

                if (!serviceId || !newStatus) {
                    showToast('error', 'Missing required information.');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    showToast('error', 'Security token not found.');
                    return;
                }

                axios.patch(`${window.location.origin}/admin/services/${serviceId}/status`, {
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
                        btn.classList.remove('btn-outline-success', 'btn-outline-secondary');
                        btn.classList.add(`btn-outline-${newStatus === 'active' ? 'success' : 'secondary'}`);

                        const menu = dropdown.querySelector('.dropdown-menu');
                        menu.innerHTML = ['active', 'inactive'].filter(status => status !== newStatus).map(status => `
                            <li>
                                <button type="button" class="dropdown-item change-status-btn" data-id="${serviceId}" data-status="${status}">
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
                        else if (error.response.status === 404) errorMessage = 'Service not found.';
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
                const button = event.target.closest('.btn-outline-danger:not(.disabled)');
                if (!button) return;
                event.preventDefault();
                const serviceId = button.dataset.id || button.getAttribute('data-bs-target')?.replace('#deleteModal', '');
                if (serviceId) deleteService(serviceId);
            });

            // Real-time search
            const searchInput = document.getElementById('search-input');
            const searchForm = document.querySelector('.search-form');
            const tableBody = document.getElementById('service-table-body');
            const tableContainer = document.querySelector('.table-container');
            const emptyState = document.getElementById('empty-state');
            const pagination = document.querySelector('.pagination');

            function performSearch() {
                const searchTerm = searchInput.value.trim();
                axios.get(`${window.location.origin}/admin/services?search=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (response.data.services?.data.length > 0) {
                        tableBody.innerHTML = response.data.services.data.map((service, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${service.name}</td>
                                <td><span class="badge bg-info">${service.type}</span></td>
                                <td><span class="badge bg-secondary">${service.clients_count} clients</span></td>
                                <td>
                                    <div class="status-dropdown">
                                        <button class="btn btn-sm btn-outline-${service.is_active ? 'success' : 'secondary'} dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-toggle="tooltip" title="Change Status">
                                            <span class="status-text">${service.is_active ? 'Active' : 'Inactive'}</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            ${['active', 'inactive'].filter(status => status !== (service.is_active ? 'active' : 'inactive')).map(status => `
                                                <li>
                                                    <button type="button" class="dropdown-item change-status-btn" data-id="${service.id}" data-status="${status}">
                                                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                                                    </button>
                                                </li>
                                            `).join('')}
                                        </ul>
                                    </div>
                                </td>
                                <td><small class="text-muted">${new Date(service.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="${window.location.origin}/admin/services/${service.id}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="${window.location.origin}/admin/services/${service.id}/edit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        ${service.clients_count === 0 ? `
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal${service.id}" data-id="${service.id}" data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        ` : `
                                            <button type="button" class="btn btn-sm btn-outline-danger disabled" data-bs-toggle="tooltip" title="Cannot delete - service has ${service.clients_count} client(s)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        `}
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