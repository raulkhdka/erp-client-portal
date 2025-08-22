@extends('layouts.app')

@section('title', 'Clients')
@section('breadcrumb')
    <span class="breadcrumb-item active">Clients</span>
@endsection
@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Client
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
                        <a href="{{ route('admin.clients.export') }}" class="btn btn-pdf" data-bs-toggle="tooltip" title="Export to PDF">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </a>
                        <a href="{{ route('admin.clients.export-excel') }}" class="btn btn-excel" data-bs-toggle="tooltip" title="Export to Excel">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-print" data-bs-toggle="tooltip" title="Print Table">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                    <div class="search-form-container">
                        <form class="search-form">
                            <input type="text" name="search" id="search-input" class="form-control" placeholder="Search by name, company, email, or phone" value="{{ request('search') }}" title="search by name, company, email, or phone">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-container">
                    @if ($clients->count() > 0)
                        <div class="table-responsive">
                            <table class="table enhanced-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-list-ol me-2"></i>SN</th>
                                        <th><i class="fas fa-user me-2"></i>Client Name</th>
                                        <th><i class="fas fa-building me-2"></i>Company Info</th>
                                        <th><i class="fas fa-user-tie me-2"></i>Employee Name</th>
                                        <th><i class="fas fa-envelope me-2"></i>Email</th>
                                        <th><i class="fas fa-phone me-2"></i>Phone</th>
                                        <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="client-table-body">
                                    @foreach ($clients as $client)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $client->name }}</td>
                                            <td>
                                                {{ $client->company_name }}
                                                @if ($client->services && $client->services->count() > 0)
                                                    <br><small class="text-muted">
                                                    @foreach ($client->services->take(2) as $service)
                                                        <span class="badge bg-light text-dark me-1">{{ $service->name }}</span>
                                                    @endforeach
                                                    @if ($client->services->count() > 2)
                                                        <span class="text-muted">+{{ $client->services->count() - 2 }} more</span>
                                                    @endif
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($client->assignedEmployees->first())
                                                    {{ $client->assignedEmployees->first()->name ?? 'Unassigned' }}
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $client->user->email }}
                                                @if ($client->emails->count() > 0)
                                                    <br><small class="text-muted">+{{ $client->emails->count() }} additional email(s)</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($client->phones->count() > 0)
                                                    {{ $client->phones->first()->phone }}
                                                    @if ($client->phones->count() > 1)
                                                        <br><small class="text-muted">+{{ $client->phones->count() - 1 }} more</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No phone</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="status-dropdown">
                                                    <button class="btn btn-sm btn-outline-{{ $client->status === 'active' ? 'success' : 'secondary' }} dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-toggle="tooltip" title="Change Status">
                                                        <span class="status-text">{{ ucfirst($client->status) }}</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach (['active', 'inactive'] as $status)
                                                            @if ($status !== $client->status)
                                                                <li>
                                                                    <button type="button" class="dropdown-item change-status-btn" data-id="{{ $client->id }}" data-status="{{ $status }}">
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
                                                    <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-client-btn" data-bs-toggle="tooltip" title="Delete" data-id="{{ $client->id }}">
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
                            {{ $clients->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5" id="empty-state">
                            <i class="fas fa-users fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No clients found</h4>
                            <p class="text-muted mb-4">Start by adding your first client to the system.</p>
                            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Add First Client
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
        function deleteClient(clientId) {
            if (!clientId || isNaN(clientId)) {
                showToast('error', 'Invalid client ID.');
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

                    axios.delete(`${window.location.origin}/admin/clients/${clientId}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(response => {
                        if (response.data.status === 'success') {
                            const row = document.querySelector(`tr td button[data-id="${clientId}"]`)?.closest('tr');
                            if (row) row.remove();
                            showToast('success', 'Client deleted successfully.');
                        } else {
                            showToast('error', response.data.message || 'Failed to delete client.');
                        }
                    }).catch(error => {
                        let errorMessage = 'An error occurred while deleting the client.';
                        if (error.response) {
                            if (error.response.status === 405) errorMessage = 'Method not allowed.';
                            else if (error.response.status === 404) errorMessage = 'Client not found.';
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
                const clientId = button.dataset.id;
                const newStatus = button.dataset.status;

                if (!clientId || !newStatus) {
                    showToast('error', 'Missing required information.');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    showToast('error', 'Security token not found.');
                    return;
                }

                axios.patch(`${window.location.origin}/admin/clients/${clientId}/status`, {
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
                                <button type="button" class="dropdown-item change-status-btn" data-id="${clientId}" data-status="${status}">
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
                        else if (error.response.status === 404) errorMessage = 'Client not found.';
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
                const button = event.target.closest('.delete-client-btn');
                if (!button) return;
                event.preventDefault();
                deleteClient(button.dataset.id);
            });

            // Real-time search
            const searchInput = document.getElementById('search-input');
            const searchForm = document.querySelector('.search-form');
            const tableBody = document.getElementById('client-table-body');
            const tableContainer = document.querySelector('.table-container');
            const emptyState = document.getElementById('empty-state');
            const pagination = document.querySelector('.pagination');

            function performSearch() {
                const searchTerm = searchInput.value.trim();
                axios.get(`${window.location.origin}/admin/clients?search=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (response.data.clients?.data.length > 0) {
                        tableBody.innerHTML = response.data.clients.data.map((client, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${client.name}</td>
                                <td>
                                    ${client.company_name}
                                    ${client.services && client.services.length > 0 ? `
                                        <br><small class="text-muted">
                                            ${client.services.slice(0, 2).map(service => `<span class="badge bg-light text-dark me-1">${service.name}</span>`).join('')}
                                            ${client.services.length > 2 ? `<span class="text-muted">+${client.services.length - 2} more</span>` : ''}
                                        </small>
                                    ` : ''}
                                </td>
                                <td>
                                    ${client.assignedEmployees && client.assignedEmployees.length > 0 ? client.assignedEmployees[0].name || 'Unassigned' : '<span class="text-muted">Unassigned</span>'}
                                </td>
                                <td>
                                    ${client.user ? client.user.email : 'N/A'}
                                    ${client.emails && client.emails.length > 0 ? `<br><small class="text-muted">+${client.emails.length} additional email(s)</small>` : ''}
                                </td>
                                <td>
                                    ${client.phones && client.phones.length > 0 ? `
                                        ${client.phones[0].phone}
                                        ${client.phones.length > 1 ? `<br><small class="text-muted">+${client.phones.length - 1} more</small>` : ''}
                                    ` : '<span class="text-muted">No phone</span>'}
                                </td>
                                <td>
                                    <div class="status-dropdown">
                                        <button class="btn btn-sm btn-outline-${client.status === 'active' ? 'success' : 'secondary'} dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-toggle="tooltip" title="Change Status">
                                            <span class="status-text">${client.status.charAt(0).toUpperCase() + client.status.slice(1)}</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            ${['active', 'inactive'].filter(status => status !== client.status).map(status => `
                                                <li>
                                                    <button type="button" class="dropdown-item change-status-btn" data-id="${client.id}" data-status="${status}">
                                                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                                                    </button>
                                                </li>
                                            `).join('')}
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="${window.location.origin}/admin/clients/${client.id}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="${window.location.origin}/admin/clients/${client.id}/edit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-client-btn" data-bs-toggle="tooltip" title="Delete" data-id="${client.id}">
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