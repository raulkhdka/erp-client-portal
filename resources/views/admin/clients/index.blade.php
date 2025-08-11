@extends('layouts.app')

@section('title', 'Clients')
@section('breadcrumb')
    <span class="breadcrumb-item active">Clients</span>
@endsection
@section('actions')
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Client
    </a>
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
            transition: all 0.3s ease;
            background-color: white;
            word-wrap: break-word;
            font-size: 0.875rem;
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
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .modal-header {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .icon-wrapper {
            transition: transform 0.3s ease;
        }

        .icon-wrapper:hover {
            transform: scale(1.1);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card enhanced-card">
                    <div class="card-body">
                        @if ($clients->count() > 0)
                            <div class="table-container">
                                <div class="table-responsive">
                                    <table class="table enhanced-table">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-list-ol me-2"></i>SN</th>
                                                <th><i class="fas fa-user me-2"></i>Client Name</th>
                                                <th><i class="fas fa-building me-2"></i>Company Name</th>
                                                <th><i class="fas fa-user-tie me-2"></i>Employee Name</th>
                                                <th><i class="fas fa-envelope me-2"></i>Email</th>
                                                <th><i class="fas fa-phone me-2"></i>Phone</th>
                                                <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                                {{-- <th><i class="fas fa-calendar-alt me-2"></i>Created</th> --}}
                                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clients as $client)
                                                <tr>
                                                    <td>
                                                        <span class="truncated-content" data-bs-toggle="tooltip"
                                                            title="Client #{{ $loop->iteration }}">
                                                            {{ $loop->iteration }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $client->name }}</td>
                                                    <td>
                                                        <strong>{{ $client->company_name }}</strong>
                                                        @if (isset($client->services) &&
                                                                $client->services instanceof \Illuminate\Database\Eloquent\Collection &&
                                                                $client->services->count() > 0)
                                                            <br><small class="text-muted">
                                                                @foreach ($client->services->take(2) as $service)
                                                                    <span
                                                                        class="badge bg-light text-dark me-1 animated-badge">{{ $service->name }}</span>
                                                                @endforeach
                                                                @if ($client->services->count() > 2)
                                                                    <span
                                                                        class="text-muted">+{{ $client->services->count() - 2 }}
                                                                        more</span>
                                                                @endif
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($client->assignedEmployees->first())
                                                            {{ $client->assignedEmployees->first()->name }}
                                                        @else
                                                            <span class="text-muted">Unassigned</span>
                                                        @endif

                                                    </td>
                                                    <td>
                                                        {{ $client->user->email }}
                                                        @if ($client->emails->count() > 0)
                                                            <br><small class="text-muted">+{{ $client->emails->count() }}
                                                                additional email(s)</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($client->phones->count() > 0)
                                                            {{ $client->phones->first()->phone }}
                                                            @if ($client->phones->count() > 1)
                                                                <br><small
                                                                    class="text-muted">+{{ $client->phones->count() - 1 }}
                                                                    more</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">No phone</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $client->status === 'active' ? 'success' : ($client->status === 'inactive' ? 'secondary' : 'warning') }} animated-badge"
                                                            data-bs-toggle="tooltip"
                                                            title="Status: {{ ucfirst($client->status) }}">
                                                            {{ ucfirst($client->status) }}
                                                        </span>
                                                    </td>
                                                    {{-- <td>{{ $client->created_at->format('M d, Y') }}</td> --}}
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.clients.show', $client->id) }}"
                                                                class="btn btn-sm btn-outline-primary icon-wrapper"
                                                                data-bs-toggle="tooltip" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.clients.edit', $client->id) }}"
                                                                class="btn btn-sm btn-outline-secondary icon-wrapper"
                                                                data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger icon-wrapper"
                                                                data-bs-toggle="tooltip" title="Delete"
                                                                data-delete-url="{{ route('admin.clients.destroy', $client->id) }}"
                                                                onclick="deleteClient(this)">
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
                                    {{ $clients->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5 empty-state">
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
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this client? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
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

            // Add click animation to badges and buttons
            const animatedElements = document.querySelectorAll('.animated-badge, .btn, .icon-wrapper');
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
        });

        function deleteClient(button) {
            const form = document.getElementById('deleteForm');
            form.action = button.getAttribute('data-delete-url');
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endpush
