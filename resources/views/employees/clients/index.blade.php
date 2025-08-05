@extends('layouts.app')

@section('title', 'Assigned Clients')

@section('breadcrumb')
    <span class="breadcrumb-item active">Assigned Clients</span>
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
        table-layout: fixed !important;
        min-width: 0;
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
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.75rem 0.5rem;
        position: relative;
        overflow: hidden;
        white-space: nowrap;
        width: auto;
    }

    /* Adjusted column widths to fit within 100% for 7 columns */
    .enhanced-table thead th:nth-child(1) { width: 15%; min-width: 120px; } /* Client Name */
    .enhanced-table thead th:nth-child(2) { width: 25%; min-width: 160px; } /* Company Name */
    .enhanced-table thead th:nth-child(3) { width: 20%; min-width: 150px; } /* Email */
    .enhanced-table thead th:nth-child(4) { width: 15%; min-width: 120px; } /* Phone */
    .enhanced-table thead th:nth-child(5) { width: 10%; min-width: 80px; } /* Status */
    .enhanced-table thead th:nth-child(6) { width: 10%; min-width: 80px; } /* Created */
    .enhanced-table thead th:nth-child(7) { width: 10%; min-width: 100px; } /* Actions */

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
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
        text-align: center !important;
        transition: all 0.3s ease;
        background-color: white;
        width: auto;
        max-width: none;
        word-wrap: break-word;
    }

    .enhanced-table tbody td:nth-child(1) { width: 15%; } /* Client Name */
    .enhanced-table tbody td:nth-child(2) { width: 25%; } /* Company Name */
    .enhanced-table tbody td:nth-child(3) { width: 20%; } /* Email */
    .enhanced-table tbody td:nth-child(4) { width: 15%; } /* Phone */
    .enhanced-table tbody td:nth-child(5) { width: 10%; } /* Status */
    .enhanced-table tbody td:nth-child(6) { width: 10%; } /* Created */
    .enhanced-table tbody td:nth-child(7) { width: 10%; } /* Actions */

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
            padding: 0.5rem 0.25rem;
        }

        .enhanced-table thead th:nth-child(1) { width: 15%; min-width: 100px; }
        .enhanced-table thead th:nth-child(2) { width: 25%; min-width: 140px; }
        .enhanced-table thead th:nth-child(3) { width: 20%; min-width: 130px; }
        .enhanced-table thead th:nth-child(4) { width: 15%; min-width: 100px; }
        .enhanced-table thead th:nth-child(5) { width: 10%; min-width: 70px; }
        .enhanced-table thead th:nth-child(6) { width: 10%; min-width: 70px; }
        .enhanced-table thead th:nth-child(7) { width: 10%; min-width: 90px; }

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

        .enhanced-table thead th:nth-child(1) { width: 15%; min-width: 90px; }
        .enhanced-table thead th:nth-child(2) { width: 25%; min-width: 120px; }
        .enhanced-table thead th:nth-child(3) { width: 20%; min-width: 110px; }
        .enhanced-table thead th:nth-child(4) { width: 15%; min-width: 90px; }
        .enhanced-table thead th:nth-child(5) { width: 10%; min-width: 60px; }
        .enhanced-table thead th:nth-child(6) { width: 10%; min-width: 60px; }
        .enhanced-table thead th:nth-child(7) { width: 10%; min-width: 80px; }

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

        .enhanced-table thead th:nth-child(1) { width: 15%; min-width: 80px; }
        .enhanced-table thead th:nth-child(2) { width: 25%; min-width: 110px; }
        .enhanced-table thead th:nth-child(3) { width: 20%; min-width: 100px; }
        .enhanced-table thead th:nth-child(4) { width: 15%; min-width: 80px; }
        .enhanced-table thead th:nth-child(5) { width: 10%; min-width: 50px; }
        .enhanced-table thead th:nth-child(6) { width: 10%; min-width: 50px; }
        .enhanced-table thead th:nth-child(7) { width: 10%; min-width: 70px; }

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
                    <form method="GET" action="{{ route('employees.clients.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="client_id" class="form-select client-select">
                                    <option value="">All Clients</option>
                                    @foreach($accessibleClients as $client)
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
                                <a href="{{ route('employees.clients.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    @if ($clients->count() > 0)
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table enhanced-table">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-user me-2"></i>Client Name</th>
                                            <th><i class="fas fa-building me-2"></i>Company Name</th>
                                            <th><i class="fas fa-envelope me-2"></i>Email</th>
                                            <th><i class="fas fa-phone me-2"></i>Phone</th>
                                            <th><i class="fas fa-signal me-2"></i>Status</th>
                                            <th><i class="fas fa-calendar-alt me-2"></i>Created</th>
                                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($clients as $client)
                                            <tr>
                                                <td>
                                                    @php
                                                        $isLongName = strlen($client->name) > 30;
                                                    @endphp
                                                    <span class="{{ $isLongName ? 'truncated-content' : '' }}"
                                                          @if($isLongName) data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $client->name }}" @endif>
                                                        {{ $isLongName ? Str::limit($client->name, 30) : $client->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong>{{ $client->company_name ?? 'N/A' }}</strong>
                                                    @if (isset($client->services) && $client->services instanceof \Illuminate\Database\Eloquent\Collection && $client->services->count() > 0)
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
                                                    {{ $client->user->email ?? 'N/A' }}
                                                    @if ($client->emails->count() > 0)
                                                        <br><small class="text-muted">
                                                            +{{ $client->emails->count() }} additional email(s)
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($client->phones->count() > 0)
                                                        {{ $client->phones->first()->phone }}
                                                        @if ($client->phones->count() > 1)
                                                            <br><small class="text-muted">
                                                                +{{ $client->phones->count() - 1 }} more
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">No phone</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $client->status === 'active' ? 'success' : ($client->status === 'inactive' ? 'secondary' : 'warning') }} animated-badge">
                                                        {{ ucfirst($client->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $client->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('employees.clients.show', $client->id) }}"
                                                           class="btn btn-sm btn-outline-primary"
                                                           data-bs-toggle="tooltip" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
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
                            <h4 class="text-muted mb-3">No Clients Assigned</h4>
                            <p class="text-muted mb-4">You currently have no clients assigned to your projects.</p>
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

    // Add click animation to badges
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
            table.style.width = '100%';
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
</script>
@endpush