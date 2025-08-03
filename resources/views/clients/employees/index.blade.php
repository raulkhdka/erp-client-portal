@extends('layouts.app')

@section('title', 'Assigned Employees')

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
            width: 100% !important; /* Ensure table takes full width of wrapper */
            table-layout: auto !important; /* Enable automatic column sizing */
            min-width: 0; /* Remove min-width to avoid extra space */
        }

        /* Ensure table wrapper adapts to content */
        .table-responsive {
            border: 0.5px solid #000000 !important;
            border-radius: 12px !important;
            overflow: auto;
            background: white;
            width: 90%;
            margin: 0 auto; /* Center the table */
            box-sizing: border-box; /* Include padding and border in width calculation */
        }

        /* Fix first and last column borders */
        .enhanced-table thead th:first-child,
        .enhanced-table tbody td:first-child {
            border-left: none !important;
        }

        .enhanced-table thead th:last-child,
        .enhanced-table tbody td:last-child {
            border-right: none !important;
        }

        /* Fix top and bottom row borders with rounded corners */
        .enhanced-table thead th {
            border-top: none !important;
            border-bottom: 0.5px solid #000000 !important;
        }

        .enhanced-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Rounded corners for header */
        .enhanced-table thead th:first-child {
            border-top-left-radius: 12px;
        }

        .enhanced-table thead th:last-child {
            border-top-right-radius: 12px;
        }

        /* Rounded corners for last row */
        .enhanced-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }

        .enhanced-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }

        /* Auto-sizing borders */
        .enhanced-table thead th,
        .enhanced-table tbody td {
            border-right: 0.5px solid #000000 !important;
            border-bottom: 0.5px solid #000000 !important;
        }

        /* Remove borders from last column and last row */
        .enhanced-table thead th:last-child,
        .enhanced-table tbody td:last-child {
            border-right: none !important;
        }

        .enhanced-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Override any Bootstrap table styles */
        .table-responsive .table {
            margin-bottom: 0 !important;
        }

        /* Table Headers - Auto-sizing with content-based padding */
        .enhanced-table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center !important;
            vertical-align: middle;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.75rem 1rem; /* Responsive padding */
            position: relative;
            overflow: hidden;
            white-space: nowrap; /* Prevent text wrapping in headers */
            width: auto; /* Auto width based on content */
        }

        /* Special sizing for specific columns */
        .enhanced-table thead th:nth-child(1) { /* Name column */
            min-width: 120px;
            width: auto;
        }

        .enhanced-table thead th:nth-child(2) { /* Email column */
            min-width: 150px;
            width: auto;
            max-width: 200px;
        }

        .enhanced-table thead th:nth-child(3) { /* Department column */
            min-width: 150px;
            width: auto;
            max-width: 200px;
        }

        .enhanced-table thead th:nth-child(4) { /* Position column */
            min-width: 120px;
            width: auto;
        }

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

        /* Table Body - Auto-sizing cells */
        .enhanced-table tbody td {
            padding: 0.75rem 1rem; /* Responsive padding */
            vertical-align: middle;
            text-align: center !important;
            transition: all 0.3s ease;
            background-color: white;
            width: auto; /* Auto width based on content */
            max-width: none; /* Remove max-width restrictions */
            word-wrap: break-word; /* Handle long content gracefully */
        }

        /* Specific cell sizing */
        .enhanced-table tbody td:nth-child(1) { /* Name column */
            text-align: center !important;
            font-weight: 600;
        }

        .enhanced-table tbody td:nth-child(2) { /* Email column */
            text-align: center !important;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .enhanced-table tbody td:nth-child(3) { /* Department column */
            text-align: center !important;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .enhanced-table tbody td:nth-child(4) { /* Position column */
            text-align: center !important;
        }

        /* Row Hover Effects */
        .enhanced-table tbody tr {
            transition: all 0.3s ease;
            position: relative;
            height: auto; /* Auto height based on content */
        }

        .enhanced-table tbody tr:hover {
            background-color: #f8fafc !important;
            transform: scale(1.01);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .enhanced-table tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        /* Alternating Row Colors */
        .enhanced-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .enhanced-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        .enhanced-table tbody tr:nth-child(even):hover td {
            background-color: #f1f5f9 !important;
        }

        /* Auto-sizing text content */
        .enhanced-table .text-primary,
        .enhanced-table .text-muted,
        .enhanced-table .text-dark,
        .enhanced-table small,
        .enhanced-table strong,
        .enhanced-table span {
            display: inline-block;
            text-align: center;
        }

        /* Responsive Enhancements - Maintain auto-sizing */
        @media (max-width: 768px) {
            .enhanced-table {
                font-size: 0.875rem;
                border-radius: 8px !important;
                width: auto !important;
                min-width: 100%;
            }

            .enhanced-table thead th,
            .enhanced-table tbody td {
                padding: 0.5rem 0.75rem; /* Smaller padding on mobile */
            }

            /* Adjust minimum widths for mobile */
            .enhanced-table thead th:nth-child(1) { min-width: 100px; }
            .enhanced-table thead th:nth-child(2) { min-width: 120px; max-width: 150px; }
            .enhanced-table thead th:nth-child(3) { min-width: 120px; max-width: 150px; }
            .enhanced-table thead th:nth-child(4) { min-width: 80px; }

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

        @media (max-width: 576px) {
            .enhanced-table {
                font-size: 0.8rem;
                border-radius: 6px !important;
            }

            .enhanced-table thead th {
                font-size: 0.75rem;
                padding: 0.4rem 0.5rem; /* Even smaller padding */
            }

            .enhanced-table tbody td {
                padding: 0.4rem 0.5rem;
            }

            /* Further adjust minimum widths for small mobile */
            .enhanced-table thead th:nth-child(1) { min-width: 80px; }
            .enhanced-table thead th:nth-child(2) { min-width: 100px; max-width: 120px; }
            .enhanced-table thead th:nth-child(3) { min-width: 100px; max-width: 120px; }
            .enhanced-table thead th:nth-child(4) { min-width: 70px; }

            .enhanced-table thead th:first-child {
                border-top-left-radius: 6px;
            }

            .enhanced-table thead th:last-child {
                border-top-right-radius: 6px;
            }

            .enhanced-table tbody tr:last-child td:first-child {
                border-bottom-left-radius: 6px;
            }

            .enhanced-table tbody tr:last-child td:last-child {
                border-bottom-right-radius: 6px;
            }

            .table-responsive {
                border-radius: 6px !important;
            }
        }

        /* Loading Animation */
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

        /* Enhanced Card */
        .enhanced-card {
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .enhanced-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Empty State Enhancement */
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

        /* Pagination Enhancement */
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

                        @if ($assignedEmployees->isEmpty())
                            <div class="text-center py-5 empty-state">
                                <i class="fas fa-users-slash fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">No Employees Assigned</h4>
                                <p class="text-muted mb-4">You currently have no employees assigned to your client profile.</p>
                            </div>
                        @else
                            <div class="table-container">
                                <div class="table-responsive">
                                    <table class="table enhanced-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <i class="fas fa-user me-2"></i>Name
                                                </th>
                                                <th>
                                                    <i class="fas fa-envelope me-2"></i>Email
                                                </th>
                                                <th>
                                                    <i class="fas fa-building me-2"></i>Department
                                                </th>
                                                <th>
                                                    <i class="fas fa-briefcase me-2"></i>Position
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($assignedEmployees as $employee)
                                                <tr>
                                                    <td>
                                                        <strong class="text-primary">{{ $employee->name ?? '-' }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="truncated-content" @if(strlen($employee->user->email ?? '') > 20) data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $employee->user->email ?? 'N/A' }}" @endif>
                                                            {{ strlen($employee->user->email ?? '') > 20 ? Str::limit($employee->user->email ?? 'N/A', 20) : $employee->user->email ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="truncated-content" @if(strlen($employee->department ?? '') > 20) data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $employee->department ?? '-' }}" @endif>
                                                            {{ strlen($employee->department ?? '') > 20 ? Str::limit($employee->department ?? '-', 20) : $employee->department ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $employee->position ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    {{ $assignedEmployees->links() }}
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
                    // Let the table naturally size itself
                    table.style.width = 'auto';

                    // Ensure minimum width
                    const containerWidth = table.parentElement.offsetWidth;
                    if (table.offsetWidth < containerWidth) {
                        table.style.width = '100%';
                    }
                }
            }

            // Call auto-resize on load and window resize
            autoResizeTable();
            window.addEventListener('resize', autoResizeTable);
        });
    </script>
@endpush