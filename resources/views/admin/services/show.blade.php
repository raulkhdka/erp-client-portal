@extends('layouts.app')

@section('title', $service->name . ' - Service Details')

@section('breadcrumb')
    <a href="{{ route('admin.services.index') }}" class="breadcrumb-item">Services</a>
    <span class="breadcrumb-item active">{{ $service->name }}</span>
@endsection

@section('actions')
    <div class="btn-group me-2">
        <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        @if($service->clients->count() == 0)
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash me-2"></i>Delete
            </button>
        @endif
    </div>
    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Services
    </a>
@endsection

@push('styles')
    <style>
        :root {
            --primary-color: #10b981;
        }

        .modern-card {
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: #f7f7f7;
            overflow: hidden;
            position: relative;
            max-height: 600px;
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem !important;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .modern-card .card-header {
            padding: 1.5rem;
            border-bottom: none;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 16px;
            padding: 2px;
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
            font-size: 1.125rem;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        .modern-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #000000;
            background: #f7f7f7;
        }

        .modern-table thead th {
            background: #e5e7eb;
            color: #1f2937;
            font-weight: 600;
            font-size: 0.75rem;
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
            font-size: 0.75rem;
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
            font-size: 0.625rem;
            font-weight: 500;
            transition: transform 0.2s ease;
        }

        .badge:hover {
            transform: scale(1.05);
        }

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

        .show-layout-flex {
            display: flex;
            align-items: flex-start;
            width: 100%;
            min-height: 100vh;
        }

        .show-layout-flex #sidebar {
            position: static;
            width: 250px;
            min-width: 250px;
            height: 100vh;
            z-index: 2;
        }

        .show-layout-flex .main-content-section {
            flex: 1 1 0;
            min-width: 0;
            padding: 0 2rem 0 2rem;
            margin-left: 0;
        }

        .show-layout-flex .side-panel {
            width: 340px;
            max-width: 100%;
            margin-left: 0;
            position: sticky;
            top: 1.5rem;
            align-self: flex-start;
            z-index: 0;
        }

        @media (max-width: 1200px) {
            .show-layout-flex .side-panel {
                width: 280px;
            }
        }

        @media (max-width: 992px) {
            .show-layout-flex {
                flex-direction: column;
            }
            .show-layout-flex #sidebar,
            .show-layout-flex .side-panel {
                width: 100%;
                position: static;
                min-width: 0;
                height: auto;
            }
            .show-layout-flex .main-content-section {
                padding: 0 1rem;
            }
        }

        .card.shadow {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
        }

        .card.shadow:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: #f8f9fa;
            color: #1f2937;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-body.d-flex {
            padding: 1rem 1.5rem;
        }

        .flex-fill {
            flex: 1;
        }

        .border-end {
            border-right: 1px solid #e5e7eb;
        }

        .border-bottom {
            border-bottom: 1px solid #e5e7eb;
        }

        .text-primary {
            color: #2563eb !important;
        }

        .text-success {
            color: #10b981 !important;
        }

        .text-info {
            color: #0ea5e9 !important;
        }

        .text-warning {
            color: #f59e0b !important;
        }

        .text-muted {
            color: #6b7280 !important;
        }

        .text-center {
            text-align: center !important;
        }

        .py-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .show-layout-flex h5 {
            font-size: 1.125rem;
        }

        .show-layout-flex h6 {
            font-size: 1rem;
        }

        .show-layout-flex p,
        .show-layout-flex .text-muted,
        .show-layout-flex a.text-decoration-none {
            font-size: 0.8125rem;
        }

        .show-layout-flex .modern-table thead th {
            font-size: 0.75rem;
        }

        .show-layout-flex .modern-table tbody td {
            font-size: 0.75rem;
        }

        .show-layout-flex .badge {
            font-size: 0.625rem;
        }

        .show-layout-flex .btn-sm {
            font-size: 0.75rem;
        }

        .show-layout-flex .small {
            font-size: 0.6875rem;
        }

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
                font-size: 0.6875rem;
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
                font-size: 0.625rem;
            }
        }

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
<div class="show-layout-flex">
    <div class="main-content-section">
        <!-- Service Information -->
        <div class="card modern-card mb-4 fade-in service-info-section">
            <div class="card-header">
                <h5><i class="fas fa-briefcase me-2"></i>Service Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p class="text-muted"><strong><i class="fas fa-tag me-2"></i>Name:</strong> {{ $service->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-muted"><strong><i class="fas fa-info-circle me-2"></i>Type:</strong> <span class="badge bg-info">Type {{ $service->type }}</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-muted"><strong><i class="fas fa-toggle-on me-2"></i>Status:</strong>
                            <span class="badge bg-{{ $service->is_active ? 'success' : 'warning' }}">
                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-muted"><strong><i class="fas fa-calendar-alt me-2"></i>Created:</strong> {!! $service->created_at_nepali_html !!}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-muted"><strong><i class="fas fa-calendar-check me-2"></i>Updated:</strong> {!! $service->updated_at_nepali_html !!}</p>
                    </div>
                    @if ($service->detail)
                        <div class="col-md-12 mb-3">
                            <p class="text-muted"><strong><i class="fas fa-clipboard me-2"></i>Detail:</strong> {{ $service->detail }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assigned Clients -->
        @if ($service->clients->count() > 0)
            <div class="card modern-card mb-4 fade-in assigned-clients-section">
                <div class="card-header">
                    <h5><i class="fas fa-users me-2"></i>Assigned Clients ({{ $service->clients->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-user me-2"></i>Name</th>
                                    <th><i class="fas fa-envelope me-2"></i>Email</th>
                                    <th><i class="fas fa-building me-2"></i>Company</th>
                                    <th><i class="fas fa-cog me-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($service->clients->take(6) as $client)
                                    <tr>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->emails->first()->email ?? ($client->user->email ?? 'N/A') }}</td>
                                        <td>{{ $client->company_name ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Client">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($service->clients->count() > 6)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.clients.index', ['service_id' => $service->id]) }}" class="btn btn-outline-primary">
                                <i class="fas fa-users me-2"></i>View All Clients ({{ $service->clients->count() }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Right Side Panel -->
    <div class="side-panel">
        <!-- Quick Stats -->
        <div class="card shadow mb-4 fade-in">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Quick Stats
            </div>
            <div class="card-body d-flex flex-column h-100">
                <div class="d-flex">
                    <div class="flex-fill d-flex flex-column justify-content-center text-center py-3">
                        <h4 class="text-primary mb-1">{{ $service->clients->count() }}</h4>
                        <small class="text-muted">Total Assignments</small>
                    </div>
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
                    <form action="{{ route('admin.services.update-status', $service) }}" method="POST" class="update-status-form">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-{{ $service->is_active ? 'secondary' : 'success' }}">
                            <i class="fas fa-{{ $service->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $service->is_active ? 'Deactivate' : 'Activate' }} Service
                        </button>
                    </form>
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Edit Service
                    </a>
                    <a href="{{ route('admin.clients.index', ['service_id' => $service->id]) }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-users me-2"></i>Assign Clients
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if ($service->clients->count() == 0)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $service->name }}</strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Service
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Handle status update with AJAX
    document.querySelectorAll('.update-status-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const form = event.target;
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                alert('Security token not found.');
                button.disabled = false;
                button.innerHTML = originalText;
                return;
            }

            axios({
                method: 'PATCH',
                url: form.action,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (response.data.status === 'success') {
                    const isActive = response.data.is_active;
                    const statusBadge = document.querySelector('.badge.bg-success, .badge.bg-warning');
                    statusBadge.classList.remove('bg-success', 'bg-warning');
                    statusBadge.classList.add(isActive ? 'bg-success' : 'bg-warning');
                    statusBadge.textContent = isActive ? 'Active' : 'Inactive';
                    button.classList.remove('btn-outline-success', 'btn-outline-secondary');
                    button.classList.add(isActive ? 'btn-outline-secondary' : 'btn-outline-success');
                    button.innerHTML = `<i class="fas fa-${isActive ? 'pause' : 'play'} me-2"></i>${isActive ? 'Deactivate' : 'Activate'} Service`;
                    alert('Service status updated successfully.');
                } else {
                    alert(response.data.message || 'Failed to update service status.');
                }
            }).catch(error => {
                let errorMessage = 'An error occurred while updating status.';
                if (error.response) {
                    errorMessage = error.response.data.message || errorMessage;
                }
                alert(errorMessage);
            }).finally(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    });
});
</script>
@endpush