@extends('layouts.app')

@section('title', 'Tasks')

@section('breadcrumb')
    <span class="breadcrumb-item active">Tasks</span>
@endsection

@section('actions')
    <div class="btn-group">
        @if (Auth::user()->isEmployee())
            <a href="{{ route('admin.tasks.my-tasks') }}" class="btn btn-outline-primary">
                <i class="fas fa-user me-2"></i>My Tasks
            </a>
        @endif
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Task
        </a>
        <a href="{{ route('admin.tasks.all') }}" class="btn btn-outline-secondary ms-2" target="_blank">
            <i class="fas fa-list me-2"></i>Show All
        </a>
        <!-- View Toggle Buttons -->
        <div class="btn-group ms-2" role="group">
            <button type="button" class="btn btn-outline-info" id="gridViewBtn">
                <i class="fas fa-th-large me-2"></i>Grid
            </button>
            <button type="button" class="btn btn-info" id="kanbanViewBtn">
                <i class="fas fa-columns me-2"></i>Kanban
            </button>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Status Dropdown */
        .status-dropdown .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .status-dropdown .dropdown-menu {
            position: absolute;
            z-index: 2000;
            background-color: #ffffff;
            min-width: 120px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            top: calc(100% + 0.5rem);
            left: 0;
            right: auto;
            transform: none;
        }

        .status-dropdown .dropdown-item {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
        }

        .status-dropdown .dropdown-item:hover {
            background-color: #f0fdf4;
            color: #065f46;
        }

        /* Table Styling */
        .enhanced-table {
            border-spacing: 0;
            border: 0.5px solid #000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 100%;
            table-layout: auto;
            border-radius: 0;
        }

        .table-responsive {
            background: white;
            width: 100%;
            margin: 0 auto;
            overflow-x: auto;
        }

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
            background: #10b981;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .enhanced-table tbody td {
            font-size: 0.875rem;
            background-color: white;
            border-right: 0.5px solid #000;
            border-bottom: 0.5px solid #000;
            padding: 0.5rem;
            text-align: center;
            vertical-align: middle;
            position: relative;
            overflow: visible;
        }

        .enhanced-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        /* Card Styling */
        .card {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
        }

        .card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        /* Kanban Board Styling */
        .kanban-board {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 20px 0;
            min-height: 70vh;
        }

        .kanban-column {
            flex: 0 0 300px;
            background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .kanban-column:hover {
            border-color: rgba(139, 92, 246, 0.3);
            transform: translateY(-2px);
        }

        .kanban-column.drag-over {
            border-color: #a78bfa;
            background: linear-gradient(145deg, #f1f5f9 0%, #ddd6fe 100%);
        }

        .kanban-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }

        .kanban-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .kanban-count {
            background: #a78bfa;
            color: white;
            border-radius: 14px;
            padding: 2px 7px;
            font-size: 0.65rem;
            font-weight: 600;
            min-width: 18px;
            text-align: center;
        }

        .kanban-cards {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 180px;
        }

        .kanban-task-card {
            background: white;
            border-radius: 8px;
            padding: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.15);
            cursor: grab;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .kanban-task-card:active {
            cursor: grabbing;
        }

        .kanban-task-card:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 16px rgba(139, 92, 246, 0.2);
        }

        .kanban-task-card.dragging {
            opacity: 0.5;
            transform: rotate(5deg);
            z-index: 1000;
        }

        .kanban-task-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .kanban-task-description {
            font-size: 0.75rem;
            color: #64748b;
            line-height: 1.4;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 400;
        }

        .kanban-task-client {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .kanban-task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
        }

        .kanban-task-badge {
            padding: 3px 7px;
            border-radius: 10px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kanban-task-actions {
            display: flex;
            gap: 6px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .kanban-task-btn {
            flex: 1;
            padding: 5px;
            border-radius: 5px;
            font-size: 0.65rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kanban-task-btn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .kanban-task-btn-view {
            background: #10b981;
            color: white;
        }

        .kanban-task-btn-edit {
            background: #3b82f6;
            color: white;
        }

        .kanban-task-btn-delete {
            background: #ef4444;
            color: white;
        }

        /* Priority Colors */
        .priority-low {
            background-color: #10b981;
        }

        .priority-medium {
            background-color: #f59e0b;
        }

        .priority-high {
            background-color: #ef4444;
        }

        .priority-urgent {
            background-color: red;
        }

        /* Status Column Colors */
        .status-pending .kanban-header {
            border-bottom-color: #f59e0b;
        }

        .status-pending .kanban-count {
            background: #f59e0b;
        }

        .status-in-progress .kanban-header {
            border-bottom-color: #3b82f6;
        }

        .status-in-progress .kanban-count {
            background: #3b82f6;
        }

        .status-on-hold .kanban-header {
            border-bottom-color: #6b7280;
        }

        .status-on-hold .kanban-count {
            background: #6b7280;
        }

        .status-completed .kanban-header {
            border-bottom-color: #10b981;
        }

        .status-completed .kanban-count {
            background: #10b981;
        }

        .status-resolved .kanban-header {
            border-bottom-color: #059669;
        }

        .status-resolved .kanban-count {
            background: #059669;
        }

        /* View Toggle */
        .view-toggle {
            margin-bottom: 18px;
        }

        .kanban-view {
            display: none;
        }

        .grid-view {
            display: block;
        }

        /* Loading Animation */
        .kanban-task-card.updating,
        .enhanced-table tr.updating {
            pointer-events: none;
            opacity: 0.7;
            position: relative;
        }

        .kanban-task-card.updating::after,
        .enhanced-table tr.updating::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        /* Modal Styles */
        .modal-content {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(2, 6, 23, 0.06);
        }

        .modal-header {
            border-bottom: 1px solid #e3eaf2;
            padding: 1.25rem;
        }

        .modal-title {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 800;
            color: #0f172a;
        }

        .modal-body {
            padding: 1.25rem;
        }

        @media (min-width: 992px) {
            .modal-body {
                padding: 1.5rem;
            }
        }

        /* Form Styles */
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0ea5a0 0%, #047857 100%);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .kanban-column {
                flex: 0 0 260px;
            }

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

        @media (max-width: 992px) {
            .kanban-column {
                flex: 0 0 240px;
            }

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
        }

        @media (max-width: 768px) {
            .kanban-board {
                flex-direction: column;
                gap: 14px;
            }

            .kanban-column {
                flex: 1 1 auto;
            }

            .enhanced-table {
                font-size: 0.7rem;
            }

            .enhanced-table thead th,
            .enhanced-table tbody td {
                padding: 0.2rem 0.15rem;
            }

            .status-dropdown .btn {
                font-size: 0.6rem;
                padding: 0.1rem 0.2rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .card {
                transition: none;
            }
        }

        /* Print Styles */
        @media print {
            .btn,
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
        <div class="card shadow animate-slide-up">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h3 class="card-title mb-0">
                    <i class="fas fa-tasks text-purple-600 me-2"></i>Tasks
                </h3>
            </div>

            <div class="card-body">
                @if ($tasks->count() > 0)
                    <!-- Grid View (Table) -->
                    <div id="gridView" class="grid-view">
                        <div class="table-responsive">
                            <table class="table enhanced-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-list-ol me-2"></i>SN</th>
                                        <th><i class="fas fa-tasks me-2"></i>Title</th>
                                        <th><i class="fas fa-building me-2"></i>Client</th>
                                        <th><i class="fas fa-exclamation-circle me-2"></i>Priority</th>
                                        <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                        <th><i class="fas fa-user-tie me-2"></i>Assigned To</th>
                                        <th><i class="fas fa-calendar-alt me-2"></i>Due Date</th>
                                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="task-table-body">
                                    @foreach ($tasks as $index => $task)
                                        <tr class="animate-fade-in" data-task-id="{{ $task->id }}"
                                            style="animation-delay: {{ $index * 0.1 }}s">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $task->title }}
                                                @if ($task->call_log_id)
                                                    <br><small>
                                                        <a href="{{ route('admin.call-logs.show', $task->call_log_id) }}"
                                                            style="text-decoration: none;">
                                                            #call{{ $task->call_log_id }}
                                                        </a>
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $task->client->company_name ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                                            </td>
                                            <td>
                                                <div class="status-dropdown">
                                                    <button class="btn btn-sm btn-outline-{{ $task->status_color }}"
                                                        type="button" data-bs-toggle="dropdown" data-bs-toggle="tooltip"
                                                        title="Change Status">
                                                        <span class="status-text">{{ $task->status_label }}</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach (\App\Models\Task::getStatusOptions() as $value => $label)
                                                            @if ($value != $task->status)
                                                                <li>
                                                                    <button type="button"
                                                                        class="dropdown-item change-status-btn"
                                                                        data-id="{{ $task->id }}"
                                                                        data-status="{{ $value }}">
                                                                        {{ $label }}
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @if ($task->is_overdue)
                                                    <br><small class="badge bg-danger">Overdue</small>
                                                @endif
                                            </td>
                                            <td>{{ $task->assignedTo->name ?? 'Unassigned' }}</td>
                                            <td>{{ $task->due_date_formatted ?? 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="showTaskModal('{{ $task->id }}')"
                                                        data-bs-toggle="tooltip" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if (Auth::user()->isAdmin())
                                                        <a href="{{ route('admin.tasks.edit', $task->id) }}"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger delete-task-btn"
                                                            data-bs-toggle="tooltip" title="Delete"
                                                            data-id="{{ $task->id }}"
                                                            data-title="{{ $task->title }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="pagination mt-4">
                            {{ $tasks->links() }}
                        </div>
                    </div>

                    <!-- Kanban View -->
                    <div id="kanbanView" class="kanban-view">
                        <div class="kanban-board">
                            @php
                                $statusOptions = \App\Models\Task::getStatusOptions();
                                $groupedTasks = $tasks->groupBy('status');
                            @endphp

                            @foreach ($statusOptions as $statusValue => $statusLabel)
                                @php
                                    $statusTasks = $groupedTasks->get($statusValue, collect());
                                    $statusClass = strtolower(str_replace(' ', '-', $statusLabel));
                                @endphp
                                <div class="kanban-column status-{{ $statusClass }}" data-status="{{ $statusValue }}">
                                    <div class="kanban-header">
                                        <h5 class="kanban-title">
                                            <i class="fas fa-circle"></i>
                                            {{ $statusLabel }}
                                            <span class="kanban-count">{{ $statusTasks->count() }}</span>
                                        </h5>
                                    </div>
                                    <div class="kanban-cards" data-status="{{ $statusValue }}">
                                        @foreach ($statusTasks as $task)
                                            <div class="kanban-task-card" data-task-id="{{ $task->id }}"
                                                draggable="true">
                                                <div class="kanban-task-title">{{ $task->title }}</div>
                                                @if ($task->description)
                                                    <p class="kanban-task-description">{{ $task->description }}
                                                    </p>
                                                @endif
                                                <div class="kanban-task-client">
                                                    <i class="fas fa-building"></i>
                                                    {{ $task->client->company_name ?? 'N/A' }}
                                                </div>
                                                <div class="kanban-task-meta">
                                                    <span class="kanban-task-badge priority-{{ $task->priority }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if ($task->assignedTo && $task->assignedTo->user)
                                                        <span class="kanban-task-badge"
                                                            style="background: #6366f1; color: white;">
                                                            {{ $task->assignedTo->name }}
                                                        </span>
                                                    @endif
                                                    @if ($task->due_date)
                                                        <span class="kanban-task-badge"
                                                            style="background: #8b5cf6; color: white;">
                                                            {{ $task->due_date_formatted }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="kanban-task-actions">
                                                    <button class="kanban-task-btn kanban-task-btn-view"
                                                        onclick="showTaskModal('{{ $task->id }}')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if (Auth::user()->isAdmin())
                                                        <a href="{{ route('admin.tasks.edit', $task->id) }}"
                                                            class="kanban-task-btn kanban-task-btn-edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button class="kanban-task-btn kanban-task-btn-delete"
                                                            onclick="deleteTaskModal('{{ $task->id }}', '{{ $task->title }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="alert alert-info animate-fade-in">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3"></i>
                            <div>
                                <h5 class="mb-1">No tasks found</h5>
                                <p class="mb-0">Create tasks directly or automatically from call logs.</p>
                                <div class="mt-3 d-flex gap-2">
                                    <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Create Task
                                    </a>
                                    <a href="{{ route('admin.call-logs.create') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-phone me-1"></i>Record a Call
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the task:</p>
                    <p><strong id="taskTitle"></strong></p>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i>Delete Task
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Task Modal -->
    <div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewTaskModalLabel">
                        <i class="fas fa-tasks text-purple-600 me-2"></i>
                        Task Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body view-modal-body" id="viewTaskContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Loading task details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 290px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">
                        <i class="fas fa-tasks text-purple-600 me-2"></i>
                        Update Task Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status" class="form-label fw-semibold">New Status</label>
                            <select name="status" id="status" class="form-select" required>
                                @foreach (\App\Models\Task::getStatusOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="error-messages" id="status-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="updateStatusBtn">
                            <i class="fas fa-check me-1"></i>Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Wrap all code in an IIFE to prevent global namespace conflicts
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                const gridViewBtn = document.getElementById('gridViewBtn');
                const kanbanViewBtn = document.getElementById('kanbanViewBtn');
                const gridView = document.getElementById('gridView');
                const kanbanView = document.getElementById('kanbanView');

                // Load saved view preference from localStorage
                const savedView = localStorage.getItem('taskViewPreference') || 'grid';
                if (savedView === 'kanban') {
                    showKanbanView();
                } else {
                    showGridView();
                }

                gridViewBtn.addEventListener('click', function() {
                    showGridView();
                    localStorage.setItem('taskViewPreference', 'grid');
                });

                kanbanViewBtn.addEventListener('click', function() {
                    showKanbanView();
                    localStorage.setItem('taskViewPreference', 'kanban');
                });

                function showGridView() {
                    gridView.style.display = 'block';
                    kanbanView.style.display = 'none';
                    gridViewBtn.classList.remove('btn-outline-info');
                    gridViewBtn.classList.add('btn-info');
                    kanbanViewBtn.classList.remove('btn-info');
                    kanbanViewBtn.classList.add('btn-outline-info');
                }

                function showKanbanView() {
                    gridView.style.display = 'none';
                    kanbanView.style.display = 'block';
                    kanbanViewBtn.classList.remove('btn-outline-info');
                    kanbanViewBtn.classList.add('btn-info');
                    gridViewBtn.classList.remove('btn-info');
                    gridViewBtn.classList.add('btn-outline-info');
                    setTimeout(initializeDragAndDrop, 100);
                }

                function initializeDragAndDrop() {
                    const kanbanColumns = document.querySelectorAll('.kanban-cards');
                    kanbanColumns.forEach(column => {
                        new Sortable(column, {
                            group: 'kanban',
                            animation: 150,
                            ghostClass: 'kanban-task-card-ghost',
                            chosenClass: 'kanban-task-card-chosen',
                            dragClass: 'dragging',
                            onStart: function(evt) {
                                evt.item.classList.add('dragging');
                            },
                            onEnd: function(evt) {
                                evt.item.classList.remove('dragging');
                                if (evt.from !== evt.to) {
                                    const taskId = evt.item.dataset.taskId;
                                    const newStatus = evt.to.dataset.status;
                                    const oldStatus = evt.from.dataset.status;
                                    updateTaskStatusViaDrag(taskId, newStatus, oldStatus,
                                        evt.item, evt.from);
                                }
                            },
                            onAdd: function(evt) {
                                updateColumnCounts();
                            },
                            onRemove: function(evt) {
                                updateColumnCounts();
                            }
                        });
                    });
                }

                function updateTaskStatusViaDrag(taskId, newStatus, oldStatus, taskElement, oldColumn) {
                    taskElement.classList.add('updating');
                    const tableRow = document.querySelector(`tr[data-task-id="${taskId}"]`);
                    if (tableRow) tableRow.classList.add('updating');

                    updateColumnCounts();

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('CSRF token not found');
                        taskElement.classList.remove('updating');
                        if (tableRow) tableRow.classList.remove('updating');
                        oldColumn.appendChild(taskElement);
                        updateColumnCounts();
                        showToast('error', 'Security token not found. Please refresh the page.');
                        return;
                    }

                    axios.patch('/admin/tasks/' + taskId + '/status', {
                            status: parseInt(newStatus)
                        }, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                            }
                        })
                        .then(response => {
                            taskElement.classList.remove('updating');
                            if (tableRow) tableRow.classList.remove('updating');
                            showToast('success', 'Task status updated to ' + (response.data.status_label ||
                                'new status'));

                            // Update the table row
                            updateTableRow(taskId, response.data.task);

                            // Update Kanban view
                            updateKanbanTaskCard(taskId, response.data.task);
                            updateColumnCounts();

                            // Dispatch event to reinitialize dropdowns
                            document.dispatchEvent(new Event('tasksUpdated'));
                        })
                        .catch(error => {
                            taskElement.classList.remove('updating');
                            if (tableRow) tableRow.classList.remove('updating');
                            oldColumn.appendChild(taskElement);
                            updateColumnCounts();
                            console.error('Update Task Status Error:', error);
                            const message = error.response?.data?.message ||
                                'Failed to update task status.';
                            showToast('error', message);
                        });
                }

                function updateColumnCounts() {
                    document.querySelectorAll('.kanban-column').forEach(column => {
                        const count = column.querySelectorAll('.kanban-task-card').length;
                        const countElement = column.querySelector('.kanban-count');
                        if (countElement) {
                            countElement.textContent = count;
                        }
                    });
                }

                function escapeHtml(unsafe) {
                    if (!unsafe || typeof unsafe !== 'string') return '';
                    return unsafe
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;")
                        .replace(/`/g, "&#96;")
                        .replace(/\n/g, " ")
                        .replace(/\r/g, "")
                        .replace(/\t/g, " ")
                        .replace(/[\x00-\x1F\x7F-\x9F]/g, "");
                }

                function updateTableRow(taskId, task) {
                    const tableRow = document.querySelector(`tr[data-task-id="${taskId}"]`);
                    if (!tableRow) {
                        console.warn(
                            `Table row for task ID ${taskId} not found. Possibly on another page.`
                        );
                        return;
                    }

                    try {
                        const cells = tableRow.querySelectorAll('td');
                        const titleCell = cells[1];
                        const clientCell = cells[2];
                        const priorityCell = cells[3];
                        const statusCell = cells[4];
                        const assignedToCell = cells[5];
                        const dueDateCell = cells[6];

                        // Update Title
                        if (titleCell) {
                            titleCell.innerHTML = escapeHtml(task.title || 'No Title') +
                                (task.call_log_id ?
                                    `<br><small><a href="${window.location.origin}/admin/call-logs/${task.call_log_id}" style="text-decoration: none;">#call${task.call_log_id}</a></small>` :
                                    '');
                        }

                        // Update Client
                        if (clientCell) {
                            clientCell.textContent = task.client?.company_name || 'N/A';
                        }

                        // Update Priority
                        if (priorityCell) {
                            priorityCell.innerHTML =
                                `<span class="badge priority-${escapeHtml(task.priority || 'unknown')}">${escapeHtml(task.priority ? task.priority.charAt(0).toUpperCase() + task.priority.slice(1) : 'Unknown')}</span>`;
                        }

                        // Update Status
                        if (statusCell) {
                            const statusLabel = escapeHtml(task.status_label || 'Unknown');
                            const statusColor = escapeHtml(task.status_color || 'secondary');

                            // Get status options from the response or use fallback
                            const statusOptions = task.statusOptions || {
                                1: 'Pending',
                                2: 'In Progress',
                                3: 'On Hold',
                                4: 'Escalated',
                                5: 'Waiting Client',
                                6: 'Testing',
                                7: 'Completed',
                                8: 'Resolved',
                                9: 'Backlog',
                            };

                            // Remove the old dropdown
                            const existingDropdown = statusCell.querySelector('.status-dropdown');
                            if (existingDropdown) {
                                const existingButton = existingDropdown.querySelector(
                                    'button[data-bs-toggle="dropdown"]');
                                if (existingButton) {
                                    const dropdownInstance = bootstrap.Dropdown.getInstance(existingButton);
                                    if (dropdownInstance) {
                                        dropdownInstance.dispose();
                                    }
                                }
                                existingDropdown.remove();
                            }

                            // Create new dropdown structure
                            const dropdownContainer = document.createElement('div');
                            dropdownContainer.className = 'status-dropdown';

                            // Create button
                            const button = document.createElement('button');
                            button.className = `btn btn-sm btn-outline-${statusColor}`;
                            button.type = 'button';
                            button.setAttribute('data-bs-toggle', 'dropdown');
                            button.setAttribute('aria-expanded', 'false');
                            button.setAttribute('data-bs-auto-close', 'true');

                            const statusSpan = document.createElement('span');
                            statusSpan.className = 'status-text';
                            statusSpan.textContent = statusLabel;
                            button.appendChild(statusSpan);

                            // Create dropdown menu
                            const dropdownMenu = document.createElement('ul');
                            dropdownMenu.className = 'dropdown-menu';

                            // Add dropdown items
                            Object.entries(statusOptions).forEach(([value, label]) => {
                                if (parseInt(value) !== parseInt(task.status)) {
                                    const li = document.createElement('li');
                                    const dropdownItem = document.createElement('button');
                                    dropdownItem.type = 'button';
                                    dropdownItem.className = 'dropdown-item change-status-btn';
                                    dropdownItem.setAttribute('data-id', taskId);
                                    dropdownItem.setAttribute('data-status', value);
                                    dropdownItem.textContent = label;
                                    li.appendChild(dropdownItem);
                                    dropdownMenu.appendChild(li);
                                }
                            });

                            // Assemble the dropdown
                            dropdownContainer.appendChild(button);
                            dropdownContainer.appendChild(dropdownMenu);

                            // Insert the new dropdown into the status cell
                            statusCell.appendChild(dropdownContainer);

                            // Add overdue badge if needed
                            if (task.is_overdue) {
                                const overdueBr = document.createElement('br');
                                const overdueBadge = document.createElement('small');
                                overdueBadge.className = 'badge bg-danger';
                                overdueBadge.textContent = 'Overdue';
                                statusCell.appendChild(overdueBr);
                                statusCell.appendChild(overdueBadge);
                            }

                            // Initialize Bootstrap dropdown with proper delay
                            setTimeout(() => {
                                try {
                                    const newDropdownInstance = new bootstrap.Dropdown(button);
                                    console.log('Successfully reinitialized dropdown for task:',
                                        taskId);
                                } catch (error) {
                                    console.error('Error initializing new dropdown for task:', taskId,
                                        error);
                                    setTimeout(() => {
                                        document.dispatchEvent(new Event('tasksUpdated'));
                                    }, 100);
                                }
                            }, 150);
                        }

                        // Update Assigned To
                        if (assignedToCell) {
                            const assignedName = task.assignedTo?.name || 'Unassigned';
                            assignedToCell.textContent = assignedName;
                        }

                        // Update Due Date
                        if (dueDateCell) {
                            dueDateCell.textContent = task.due_date_formatted || 'N/A';
                        }
                    } catch (error) {
                        console.error('Error updating table row for task ID:', taskId, error);
                        showToast('error', 'Failed to update table row.');
                    }
                }

                function updateKanbanTaskCard(taskId, task) {
                    const kanbanTaskCard = document.querySelector(
                        `.kanban-task-card[data-task-id="${taskId}"]`);
                    if (kanbanTaskCard && task) {
                        const title = kanbanTaskCard.querySelector('.kanban-task-title');
                        const description = kanbanTaskCard.querySelector('.kanban-task-description');
                        const client = kanbanTaskCard.querySelector('.kanban-task-client');
                        const priorityBadge = kanbanTaskCard.querySelector(
                            `.kanban-task-badge.priority-${task.priority || ''}`);
                        const assignedToBadge = kanbanTaskCard.querySelector(
                            '.kanban-task-badge[style*="background: #6366f1"]');
                        const dueDateBadge = kanbanTaskCard.querySelector(
                            '.kanban-task-badge[style*="background: #8b5cf6"]');
                        const metaContainer = kanbanTaskCard.querySelector('.kanban-task-meta');

                        if (title) title.textContent = task.title || 'No Title';
                        if (description) description.textContent = task.description || '';
                        if (client) {
                            const clientName = task.client?.company_name || 'N/A';
                            client.innerHTML = `<i class="fas fa-building"></i> ${escapeHtml(clientName)}`;
                        }
                        if (priorityBadge) priorityBadge.textContent = task.priority ? task.priority.charAt(0)
                            .toUpperCase() + task.priority.slice(1) : 'Unknown';

                        // Update or create Assigned To badge
                        const assignedName = task.assignedTo?.name || 'Unassigned';
                        if (assignedToBadge) {
                            assignedToBadge.textContent = assignedName;
                        } else if (assignedName !== 'Unassigned' && metaContainer) {
                            const assignedSpan = document.createElement('span');
                            assignedSpan.className = 'kanban-task-badge';
                            assignedSpan.style.background = '#6366f1';
                            assignedSpan.style.color = 'white';
                            assignedSpan.textContent = assignedName;
                            metaContainer.appendChild(assignedSpan);
                        } else if (assignedName === 'Unassigned' && assignedToBadge) {
                            assignedToBadge.remove();
                        }

                        // Update or create Due Date badge
                        const dueDateFormatted = task.due_date_formatted || null;
                        if (dueDateBadge && dueDateFormatted) {
                            dueDateBadge.textContent = dueDateFormatted;
                        } else if (!dueDateBadge && dueDateFormatted && metaContainer) {
                            const dueDateSpan = document.createElement('span');
                            dueDateSpan.className = 'kanban-task-badge';
                            dueDateSpan.style.background = '#8b5cf6';
                            dueDateSpan.style.color = 'white';
                            dueDateSpan.textContent = dueDateFormatted;
                            metaContainer.appendChild(dueDateSpan);
                        } else if (dueDateBadge && !dueDateFormatted) {
                            dueDateBadge.remove();
                        }
                    }
                }

                // Enhanced Show Task Modal Function
                window.showTaskModal = function(taskId) {
                    try {
                        if (!taskId || isNaN(taskId)) {
                            console.error('Invalid Task ID:', taskId);
                            showToast('error', 'Invalid task ID.');
                            return;
                        }
                        console.log('Fetching task details for task ID:', taskId);
                        const modal = new bootstrap.Modal(document.getElementById('viewTaskModal'));
                        const content = document.getElementById('viewTaskContent');
                        content.innerHTML =
                            '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading task details...</p></div>';
                        modal.show();

                        const baseUrl = window.location.origin + '/admin/tasks';
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');

                        axios.get(baseUrl + '/' + taskId, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
                                }
                            })
                            .then(response => {
                                console.log('Task details received:', response.data);
                                if (!response.data.success || !response.data.task) {
                                    throw new Error(response.data.message ||
                                        'Invalid task data received.');
                                }
                                const task = response.data.task || {};
                                const title = escapeHtml(task.title || 'No Title');
                                const clientName = task.client ? escapeHtml(task.client.company_name ||
                                    'N/A') : 'N/A';
                                const priorityLabel = escapeHtml(task.priority || 'Unknown');
                                const statusLabel = escapeHtml((response.data.statusOptions && response
                                        .data.statusOptions[task.status]) ? response.data
                                    .statusOptions[task.status] : 'Unknown');
                                const assignedTo = task.assignedTo ? escapeHtml(task.assignedTo.name) : 'Unassigned';
                                const createdBy = task.created_by && task.created_by.name ? escapeHtml(
                                    task.created_by.name) : 'System';
                                const createdAt = task.created_at_nepali_html || 'N/A';
                                const updatedAt = task.updated_at_nepali_html || 'N/A';
                                const dueDate = task.due_date_formatted || 'N/A';
                                const dueDateNepali = task.due_date_nepali_html || 'N/A';
                                const startedAt = task.started_at_nepali_html || 'N/A';
                                const completedAt = task.completed_at_nepali_html || 'N/A';
                                const description = escapeHtml(task.description || 'No Description');
                                const notes = task.notes ? escapeHtml(task.notes) : '';

                                content.innerHTML = '<div class="row">' +
                                    '<div class="col-md-8">' +
                                    '<table class="table table-borderless">' +
                                    '<tr><th width="25%">Title:</th><td><strong>' + title +
                                    '</strong></td></tr>' +
                                    '<tr><th>Client:</th><td>' + clientName + '</td></tr>' +
                                    '<tr><th>Priority:</th><td><span class="badge bg-info">' +
                                    priorityLabel + '</span></td></tr>' +
                                    '<tr><th>Status:</th><td><span class="badge bg-info">' +
                                    statusLabel + '</span></td></tr>' +
                                    '<tr><th>Assigned To:</th><td>' + assignedTo + '</td></tr>' +
                                    '<tr><th>Created By:</th><td>' + createdBy + '</td></tr>' +
                                    '<tr><th>Due Date:</th><td>' + dueDate + '</td></tr>' +
                                    '<tr><th>Created:</th><td>' + createdAt + '</td></tr>' +
                                    '<tr><th>Updated:</th><td>' + updatedAt + '</td></tr>' +
                                    '<tr><th>Started:</th><td>' + startedAt + '</td></tr>' +
                                    '<tr><th>Completed:</th><td>' + completedAt + '</td></tr>' +
                                    '</table>' +
                                    '</div>' +
                                    '</div>' +
                                    '<hr>' +
                                    '<div class="row">' +
                                    '<div class="col-12">' +
                                    '<h6>Description</h6>' +
                                    '<p class="text-muted">' + description + '</p>' +
                                    '</div>' +
                                    '</div>' +
                                    (notes ?
                                        '<div class="row"><div class="col-12"><h6>Notes</h6><p class="text-muted">' +
                                        notes + '</p></div></div>' : '');
                            })
                            .catch(error => {
                                console.error('View Task Error for task ID:', taskId, error.response ||
                                    error);
                                const message = error.response && error.response.status === 404 ?
                                    'Task with ID ' + taskId + ' not found. It may have been deleted.' :
                                    (error.response && error.response.data && error.response.data
                                        .message) ? error.response.data.message :
                                    'Failed to load task details.';
                                content.innerHTML = '<div class="alert alert-danger">' + message +
                                    '</div>';
                                showToast('error', message);
                            });
                    } catch (error) {
                        console.error('Error in showTaskModal:', error);
                        showToast('error', 'Failed to open task modal.');
                    }
                }

                window.updateStatusModal = function(taskId) {
                    try {
                        if (!taskId || isNaN(taskId)) {
                            console.error('Invalid Task ID:', taskId);
                            showToast('error', 'Invalid task ID.');
                            return;
                        }
                        const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                        const statusSelect = document.getElementById('status');
                        const updateStatusBtn = document.getElementById('updateStatusBtn');
                        updateStatusBtn.dataset.taskId = taskId;
                        modal.show();

                        const baseUrl = window.location.origin + '/admin/tasks';
                        axios.get(baseUrl + '/' + taskId, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                const task = response.data.task || {};
                                statusSelect.value = task.status || '';
                            })
                            .catch(error => {
                                console.error('Fetch Task Status Error:', error);
                                const message = error.response?.status === 404 ?
                                    'Task not found. It may have been deleted.' : error.response?.data
                                    ?.message || 'Failed to load task status.';
                                showToast('error', message);
                            });

                        updateStatusBtn.onclick = function() {
                            const taskId = this.dataset.taskId;
                            const form = document.getElementById('updateStatusForm');
                            const status = form.querySelector('#status').value;

                            if (!form || !status) {
                                showToast('error', 'Please select a status.');
                                return;
                            }

                            const csrfToken = document.querySelector('meta[name="csrf-token"]');
                            if (!csrfToken) {
                                showToast('error',
                                    'Security token not found. Please refresh the page.');
                                return;
                            }

                            this.disabled = true;
                            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';

                            axios.patch('/admin/tasks/' + taskId + '/status', {
                                    status: parseInt(status)
                                }, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                                    }
                                })
                                .then(response => {
                                    this.disabled = false;
                                    this.innerHTML =
                                        '<i class="fas fa-check me-1"></i>Update Status';
                                    showToast('success', 'Task status updated to ' + (response.data
                                        .status_label || 'new status'));

                                    const taskCard = document.querySelector(
                                        `.kanban-task-card[data-task-id="${taskId}"]`);
                                    const tableRow = document.querySelector(
                                        `tr[data-task-id="${taskId}"]`);

                                    updateTableRow(taskId, response.data.task);
                                    if (taskCard) {
                                        const newStatusColumn = document.querySelector(
                                            `.kanban-cards[data-status="${response.data.task.status}"]`
                                        );
                                        if (newStatusColumn && taskCard.parentElement !==
                                            newStatusColumn) {
                                            newStatusColumn.appendChild(taskCard);
                                            updateKanbanTaskCard(taskId, response.data.task);
                                            updateColumnCounts();
                                        } else {
                                            updateKanbanTaskCard(taskId, response.data.task);
                                        }
                                    }

                                    bootstrap.Modal.getInstance(document.getElementById(
                                        'updateStatusModal')).hide();

                                    // Dispatch event to reinitialize dropdowns
                                    document.dispatchEvent(new Event('tasksUpdated'));
                                })
                                .catch(error => {
                                    this.disabled = false;
                                    this.innerHTML =
                                        '<i class="fas fa-check me-1"></i>Update Status';
                                    console.error('Update Task Status Error:', error);
                                    const message = error.response?.data?.message ||
                                        'Failed to update task status.';
                                    showToast('error', message);
                                });
                        };
                    } catch (error) {
                        console.error('Error in updateStatusModal:', error);
                        showToast('error', 'Failed to open update status modal.');
                    }
                };

                window.deleteTaskModal = function(taskId, taskTitle) {
                    try {
                        if (!taskId || isNaN(taskId)) {
                            console.error('Invalid Task ID:', taskId);
                            showToast('error', 'Invalid task ID.');
                            return;
                        }
                        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                        const taskTitleElement = document.getElementById('taskTitle');
                        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
                        taskTitleElement.textContent = escapeHtml(taskTitle || 'Untitled Task');
                        confirmDeleteBtn.dataset.taskId = taskId;
                        modal.show();

                        confirmDeleteBtn.onclick = function() {
                            const taskId = this.dataset.taskId;
                            const csrfToken = document.querySelector('meta[name="csrf-token"]');
                            if (!csrfToken) {
                                showToast('error',
                                    'Security token not found. Please refresh the page.');
                                return;
                            }

                            this.disabled = true;
                            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Deleting...';

                            axios.delete('/admin/tasks/' + taskId, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                                    }
                                })
                                .then(response => {
                                    this.disabled = false;
                                    this.innerHTML = '<i class="fas fa-trash me-1"></i>Delete Task';
                                    showToast('success', response.data.message ||
                                        'Task deleted successfully!');

                                    const taskCard = document.querySelector(
                                        `.kanban-task-card[data-task-id="${taskId}"]`);
                                    const tableRow = document.querySelector(
                                        `tr[data-task-id="${taskId}"]`);
                                    if (taskCard) taskCard.remove();
                                    if (tableRow) tableRow.remove();
                                    updateColumnCounts();

                                    bootstrap.Modal.getInstance(document.getElementById(
                                        'deleteModal')).hide();
                                })
                                .catch(error => {
                                    this.disabled = false;
                                    this.innerHTML = '<i class="fas fa-trash me-1"></i>Delete Task';
                                    console.error('Delete Task Error:', error);
                                    const message = error.response?.data?.message ||
                                        'Failed to delete task.';
                                    showToast('error', message);
                                });
                        };
                    } catch (error) {
                        console.error('Error in deleteTaskModal:', error);
                        showToast('error', 'Failed to open delete modal.');
                    }
                };

                // Enhanced event delegation for change status buttons
                document.body.addEventListener('click', function(event) {
                    const button = event.target.closest('.change-status-btn');
                    if (!button) return;

                    event.preventDefault();
                    event.stopPropagation();

                    const taskId = button.dataset.id;
                    const newStatus = button.dataset.status;

                    console.log('Status change requested:', {
                        taskId,
                        newStatus
                    });

                    if (!taskId || !newStatus) {
                        showToast('error', 'Missing required information.');
                        return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        showToast('error', 'Security token not found.');
                        return;
                    }

                    const tableRow = document.querySelector(`tr[data-task-id="${taskId}"]`);
                    const taskCard = document.querySelector(`.kanban-task-card[data-task-id="${taskId}"]`);
                    if (tableRow) tableRow.classList.add('updating');
                    if (taskCard) taskCard.classList.add('updating');

                    // Close the dropdown manually
                    const dropdown = button.closest('.dropdown-menu');
                    if (dropdown) {
                        const dropdownButton = dropdown.previousElementSibling;
                        if (dropdownButton) {
                            const dropdownInstance = bootstrap.Dropdown.getInstance(dropdownButton);
                            if (dropdownInstance) {
                                dropdownInstance.hide();
                            }
                        }
                    }

                    axios.patch(`/admin/tasks/${taskId}/status`, {
                        status: parseInt(newStatus)
                    }, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(response => {
                        if (tableRow) tableRow.classList.remove('updating');
                        if (taskCard) taskCard.classList.remove('updating');
                        showToast('success', `Status updated to ${response.data.status_label || 'new status'}`);

                        // Update the table row
                        updateTableRow(taskId, response.data.task);

                        // Update Kanban view regardless of visibility
                        if (taskCard) {
                            const newStatusColumn = document.querySelector(`.kanban-cards[data-status="${response.data.task.status}"]`);
                            if (!newStatusColumn) {
                                console.error(`New status column not found for status: ${response.data.task.status}`);
                                showToast('error', 'Failed to find the target column for the new status.');
                                return;
                            }

                            if (taskCard.parentElement !== newStatusColumn) {
                                console.log(`Moving task ${taskId} to new status column: ${response.data.task.status}`);
                                newStatusColumn.appendChild(taskCard);
                                updateKanbanTaskCard(taskId, response.data.task);
                                updateColumnCounts();
                            } else {
                                console.log(`Task ${taskId} is already in the correct column: ${response.data.task.status}`);
                                updateKanbanTaskCard(taskId, response.data.task);
                            }
                        } else {
                            console.warn(`Task card for ID ${taskId} not found in Kanban view.`);
                            updateColumnCounts();
                        }

                        // Dispatch event to reinitialize dropdowns
                        document.dispatchEvent(new Event('tasksUpdated'));
                    }).catch(error => {
                        if (tableRow) tableRow.classList.remove('updating');
                        if (taskCard) taskCard.classList.remove('updating');
                        const message = error.response?.data?.message || 'Failed to update status.';
                        showToast('error', message);
                        console.error('Status update error:', error);
                    });
                });

                // Handle delete button clicks
                document.body.addEventListener('click', function(event) {
                    const button = event.target.closest('.delete-task-btn');
                    if (!button) return;
                    event.preventDefault();
                    deleteTaskModal(button.dataset.id, button.dataset.title);
                });

                // Initialize all dropdowns on page load
                document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                        new bootstrap.Dropdown(el);
                    }
                });

                // Reinitialize dropdowns after any dynamic update
                document.addEventListener('tasksUpdated', function() {
                    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                            const existingDropdown = bootstrap.Dropdown.getInstance(el);
                            if (existingDropdown) {
                                existingDropdown.dispose();
                            }
                            new bootstrap.Dropdown(el);
                        }
                    });
                });

                // Initialize tooltips
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                    new bootstrap.Tooltip(el);
                });
            });
        })();
    </script>
@endpush