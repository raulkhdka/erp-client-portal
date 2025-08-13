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
        /* Adjusted font sizes for smaller overall text */
        .main-sidebar {
            display: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
        }

        .card {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
        }

        .card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        /* Kanban Board Styles */
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

        /* Enhanced Task Cards for Kanban */
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

        /* Grid View */
        .tasks-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            padding: 14px 0;
        }

        .task-card {
            background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(139, 92, 246, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .task-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            background: linear-gradient(145deg, #f1f5f9 0%, #d1dae6 100%);
        }

        /* View Toggle Styles */
        .view-toggle {
            margin-bottom: 18px;
        }

        .kanban-view {
            display: none;
        }

        .grid-view {
            display: block;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .kanban-column {
                flex: 0 0 260px;
            }

            .tasks-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .kanban-column {
                flex: 0 0 240px;
            }

            .tasks-grid {
                grid-template-columns: repeat(2, 1fr);
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

            .tasks-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Loading animation */
        .kanban-task-card.updating {
            pointer-events: none;
            opacity: 0.7;
        }

        .kanban-task-card.updating::after {
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

        /* Grid View Task Card Styles */
        .task-card-body {
            padding: 14px;
            position: relative;
        }

        .task-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.3s ease;
        }

        .task-description {
            font-size: 0.75rem;
            color: #64748b;
            line-height: 1.4;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 400;
        }

        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-bottom: 10px;
            padding: 7px;
            background: rgba(139, 92, 246, 0.05);
            border-radius: 7px;
            border: 1px solid rgba(139, 92, 246, 0.08);
        }

        .task-badges {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .task-badge {
            padding: 3px 8px;
            border-radius: 14px;
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: linear-gradient(135deg, #a78bfa, #f472b6);
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .task-actions {
            display: flex;
            flex-direction: column;
            gap: 7px;
            justify-content: flex-start;
            padding-top: 10px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .task-action-row {
            display: flex;
            gap: 7px;
            flex-wrap: nowrap;
        }

        .task-btn {
            padding: 10px 15px;
            border-radius: 7px;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            white-space: nowrap;
            min-width: 100px;
        }

        .task-btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .task-btn-view {
            background: linear-gradient(135deg, #2dd4bf, #14b8a6);
            color: white;
        }

        .task-btn-update-status {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }

        .task-btn-edit {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: white;
        }

        .task-btn-delete {
            background: linear-gradient(135deg, #f87171, #ef4444);
            color: white;
        }

        /* Modal styles from create view */
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

        .form-shell {
            --stack-offset: 160px;
        }

        @media (min-width: 992px) {
            .form-shell {
                height: calc(100dvh - var(--stack-offset));
                display: flex;
                min-height: 0;
            }

            .form-scroll {
                flex: 1 1 auto;
                height: 100%;
                overflow: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 1rem;
                min-height: 0;
            }
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.75rem;
        }

        .section-title .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #eafaf3;
            color: #10b981;
            display: grid;
            place-items: center;
        }

        .section-subtext {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .input-group-text {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            color: #64748b;
            min-width: 42px;
            justify-content: center;
        }

        .form-control,
        .form-select,
        textarea.form-control {
            border-radius: 12px;
            border: 0.5px solid #000000;
            background: #f8fafc;
            color: #000000;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 .2rem rgba(16, 185, 129, 0.15);
            background: #fff;
            color: #000000;
        }

        .subcard {
            border: 1px dashed #1f2937;
            border-radius: 14px;
            padding: 1rem;
            background: #f8fafc;
        }

        .btn-ghost-danger {
            border: 1px solid #fee2e2;
            color: #dc2626;
            background: #f8fafc;
        }

        .btn-ghost-danger:hover {
            background: #ffeaea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0ea5a0 0%, #047857 100%);
        }

        .ts-wrapper.form-select .ts-control,
        .ts-wrapper .ts-control {
            border-radius: 12px;
            border: 0.5px solid #000000;
            background: #f8fafc;
            color: #000000;
            min-height: calc(1.5em + .75rem + 2px);
            padding-block: .25rem;
            padding-inline: .5rem;
        }

        .ts-wrapper.single.input-active .ts-control,
        .ts-wrapper.multi.input-active .ts-control,
        .ts-wrapper .ts-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 .2rem rgba(16, 185, 129, .15);
            color: #000000;
        }

        .ts-dropdown {
            border: 0.5px solid #000000;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
            overflow: hidden;
            background: #f8fafc;
        }

        .ts-dropdown .active {
            background: #f0fdf4;
            color: #065f46;
        }

        .ts-control .item {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            border-radius: 10px;
            padding: .25rem .5rem;
            margin: .125rem .125rem;
        }

        .ts-control .remove {
            color: #047857;
            opacity: .8;
        }

        @media (prefers-reduced-motion: reduce) {

            .card-modern,
            .form-control,
            .form-select,
            textarea.form-control {
                transition: none;
            }
        }

        .form-select.client-select {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            appearance: none;
            margin-top: -7px;
            padding: 0.25rem 0.25rem;
            font-size: 0.875rem;
            height: auto;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .form-select.status-select {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            appearance: none;
            margin-top: -7px;
            padding: 0.25rem 0.25rem;
            font-size: 0.875rem;
            height: auto;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .form-select.priority-select {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            appearance: none;
            margin-top: -7px;
            padding: 0.25rem 0.25rem;
            font-size: 0.875rem;
            height: auto;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .form-select.employee-select {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            appearance: none;
            margin-top: -7px;
            padding: 0.25rem 0.25rem;
            font-size: 0.875rem;
            height: auto;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow animate-slide-up">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-tasks text-purple-600 me-2"></i>Tasks
                        </h3>
                    </div>

                    <!-- Filter Section -->
                    <div class="card-body border-bottom bg-light py-3">
                        <div class="d-flex justify-content-between align-items-center mb-3 filter-header">
                            <h5 class="mb-0">Filter Tasks</h5>
                            <div>
                                <span class="filter-buttons">
                                    <button type="submit" class="btn btn-outline-primary btn-sm me-2" form="filterForm">
                                        <i class="fas fa-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                </span>
                                <button class="btn btn-outline-secondary btn-sm filter-toggle-btn" id="filterToggleBtn">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                        <form method="GET" action="{{ route('admin.tasks.index') }}" id="filterForm"
                            class="filter-container mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <select name="status" class="form-select status-select">
                                        <option value="">All Statuses</option>
                                        @foreach (\App\Models\Task::getStatusOptions() as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ request('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="priority" class="form-select priority-select">
                                        <option value="">All Priorities</option>
                                        @foreach (\App\Models\Task::getPriorityOptions() as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ request('priority') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="client_id" class="form-select client-select">
                                        <option value="">All Clients</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (Auth::user()->isAdmin())
                                    <div class="col-md-3">
                                        <select name="assigned_to" class="form-select employee-select">
                                            <option value="">All Employees</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        @if ($tasks->count() > 0)
                            <!-- Grid View -->
                            <div id="gridView" class="grid-view">
                                <div class="tasks-grid">
                                    @foreach ($tasks as $index => $task)
                                        <div class="task-card animate-fade-in" data-task-id="{{ $task->id }}"
                                            style="animation-delay: {{ $index * 0.1 }}s">
                                            <div class="task-card-body">
                                                <h4 class="task-title">{{ $task->title }}</h4>
                                                @if ($task->description)
                                                    <p class="task-description">{{ $task->description }}</p>
                                                @endif
                                                @if ($task->call_log_id)
                                                    <p style="font-size: 12px;padding:0; margin-bottom:0;">
                                                        <a href="{{ route('admin.call-logs.show', $task->call_log_id) }}"
                                                            style="text-decoration: none;">
                                                            #call{{ $task->call_log_id }}
                                                        </a>
                                                    </p>
                                                @endif
                                                <div class="task-meta">
                                                    <div class="task-meta-item">
                                                        <i class="fas fa-building"></i>
                                                        <span
                                                            style="font-size: 12px;">{{ $task->client->company_name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="task-meta-item">
                                                        <i class="fas fa-user"></i>
                                                        <span
                                                            style="font-size: 12px;">{{ $task->adminCreator->name ?? 'System' }}</span>
                                                    </div>
                                                    @if ($task->assignedTo && $task->assignedTo->user)
                                                        <div class="task-meta-item">
                                                            <i class="fas fa-user-tie"></i>
                                                            <span
                                                                style="font-size: 12px;">{{ $task->assignedTo->name }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="task-badges">
                                                    <span class="task-badge">{{ ucfirst($task->priority) }}</span>
                                                    <span class="task-badge">{{ $task->status_label }}</span>
                                                    @if ($task->is_overdue)
                                                        <span class="task-badge bg-danger">Overdue</span>
                                                    @endif
                                                </div>
                                                <div class="task-actions">
                                                    <div class="task-action-row">
                                                        <button type="button" class="task-btn task-btn-view"
                                                            onclick="showTaskModal('{{ $task->id }}')">
                                                            <i class="fas fa-eye me-1"></i> View
                                                        </button>
                                                        @if (Auth::user()->isAdmin())
                                                            <button type="button" class="task-btn task-btn-update-status"
                                                                onclick="updateStatusModal('{{ $task->id }}')">
                                                                <i class="fas fa-sync-alt me-1"></i> Update Status
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @if (Auth::user()->isAdmin())
                                                        <div class="task-action-row">
                                                            <button type="button" class="task-btn task-btn-edit"
                                                                onclick="editTaskModal('{{ $task->id }}')">
                                                                <i class="fas fa-edit me-1"></i> Edit
                                                            </button>
                                                            <button type="button" class="task-btn task-btn-delete"
                                                                onclick="deleteTaskModal('{{ $task->id }}', '{{ $task->title }}')">
                                                                <i class="fas fa-trash me-1"></i> Delete
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 d-flex justify-content-center">
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
                                        <div class="kanban-column status-{{ $statusClass }}"
                                            data-status="{{ $statusValue }}">
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
                                                            <span
                                                                class="kanban-task-badge priority-{{ $task->priority }}">
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
                                                                    {{ $task->due_date->format('M d') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="kanban-task-actions">
                                                            <button class="kanban-task-btn kanban-task-btn-view"
                                                                onclick="showTaskModal('{{ $task->id }}')">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if (Auth::user()->isAdmin())
                                                                <button class="kanban-task-btn kanban-task-btn-edit"
                                                                    onclick="editTaskModal('{{ $task->id }}')">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
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
                                            <a href="{{ route('admin.call-logs.create') }}"
                                                class="btn btn-outline-primary">
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
        <div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel"
            aria-hidden="true">
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

        <!-- Edit Task Modal -->
        <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTaskModalLabel">
                            <div class="icon"><i class="fas fa-edit"></i></div>
                            <div>Edit Task</div>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body edit-modal-body form-shell">
                        <div class="form-scroll">
                            <div id="editTaskContent">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                                    <p>Loading edit form...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="saveEditBtn">
                            <i class="fas fa-save me-2"></i>Save Changes
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
                            <button type="button" class="btn btn-purple-600" id="updateStatusBtn">
                                <i class="fas fa-check me-1"></i>Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Wrap all code in an IIFE to prevent global namespace conflicts
        (function() {
        // In-memory storage for view preferences (since localStorage is not available)
        let viewPreference = 'grid';

        // View toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
                const gridViewBtn = document.getElementById('gridViewBtn');
                const kanbanViewBtn = document.getElementById('kanbanViewBtn');
                const gridView = document.getElementById('gridView');
                const kanbanView = document.getElementById('kanbanView');

                // Load saved view preference (using in-memory storage)
                if (viewPreference === 'kanban') {
                    showKanbanView();
                } else {
                    showGridView();
                }

                gridViewBtn.addEventListener('click', function() {
                    showGridView();
                    viewPreference = 'grid';
                });

                kanbanViewBtn.addEventListener('click', function() {
                    showKanbanView();
                    viewPreference = 'kanban';
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

                    // Initialize drag and drop after showing kanban view
                    setTimeout(initializeDragAndDrop, 100);
                }

                // Initialize drag and drop for Kanban
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

                                // Only update if moved to different column
                                if (evt.from !== evt.to) {
                                    const taskId = evt.item.dataset.taskId;
                                    const newStatus = evt.to.dataset.status;
                                    const oldStatus = evt.from.dataset.status;

                                    updateTaskStatusViaDrag(taskId, newStatus, oldStatus,
                                        evt.item, evt.from);
                                }
                            },

                            onAdd: function(evt) {
                                // Update column counts
                                updateColumnCounts();
                            },

                            onRemove: function(evt) {
                                // Update column counts
                                updateColumnCounts();
                            }
                        });
                    });
                }

                // Update task status via drag and drop
                function updateTaskStatusViaDrag(taskId, newStatus, oldStatus, taskElement, oldColumn) {
                    // Add loading state
                    taskElement.classList.add('updating');

                    // Update task counts immediately for better UX
                    updateColumnCounts();

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('CSRF token not found');
                        taskElement.classList.remove('updating');
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
                            showToast('success', 'Task moved to ' + (response.data.status_label ||
                                'new status'));
                            // Update both grid and kanban task cards
                            updateGridTaskCard(taskId, response.data.task);
                            updateKanbanTaskCard(taskId, response.data.task);
                        })
                        .catch(error => {
                            taskElement.classList.remove('updating');
                            console.error('Update Task Status Error:', error);

                            // Revert the move on error
                            oldColumn.appendChild(taskElement);
                            updateColumnCounts();

                            const message = error.response && error.response.data && error.response.data
                                .message ?
                                error.response.data.message : 'Failed to update task status.';
                            showToast('error', message);
                        });
                }

                // Update column counts
                function updateColumnCounts() {
                    document.querySelectorAll('.kanban-column').forEach(column => {
                        const count = column.querySelectorAll('.kanban-task-card').length;
                        const countElement = column.querySelector('.kanban-count');
                        if (countElement) {
                            countElement.textContent = count;
                        }
                    });
                }

                // Enhanced escapeHtml function with defensive checks
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

                // Update Grid Task Card
                function updateGridTaskCard(taskId, task) {
                    const gridTaskCard = document.querySelector('.task-card[data-task-id="' + taskId + '"]');
                    if (gridTaskCard && task) {
                        const title = gridTaskCard.querySelector('.task-title');
                        const description = gridTaskCard.querySelector('.task-description');
                        const client = gridTaskCard.querySelector('.task-meta-item:nth-child(1) span');
                        const creator = gridTaskCard.querySelector('.task-meta-item:nth-child(2) span');
                        const assignedTo = gridTaskCard.querySelector('.task-meta-item:nth-child(3) span');
                        const statusBadge = gridTaskCard.querySelector('.task-badges .task-badge:nth-child(2)');
                        const priorityBadge = gridTaskCard.querySelector(
                            '.task-badges .task-badge:nth-child(1)');
                        const overdueBadge = gridTaskCard.querySelector('.task-badge.bg-danger');

                        if (title) title.textContent = task.title || 'No Title';
                        if (description) description.textContent = task.description || '';
                        if (client) client.textContent = (task.client && task.client.company_name) ? task.client
                            .company_name : 'N/A';
                        if (creator) creator.textContent = (task.created_by && task.created_by.name) ? task
                            .created_by.name : 'System';
                        if (assignedTo) assignedTo.textContent = (task.assigned_to && task.assigned_to.name) ?
                            task.assigned_to.name : 'Unassigned';
                        if (statusBadge) statusBadge.textContent = task.status_label || 'Unknown';
                        if (priorityBadge) priorityBadge.textContent = task.priority || 'Unknown';

                        if (task.is_overdue && !overdueBadge) {
                            const badgesContainer = gridTaskCard.querySelector('.task-badges');
                            if (badgesContainer) {
                                const overdueSpan = document.createElement('span');
                                overdueSpan.className = 'task-badge bg-danger';
                                overdueSpan.textContent = 'Overdue';
                                badgesContainer.appendChild(overdueSpan);
                            }
                        } else if (!task.is_overdue && overdueBadge) {
                            overdueBadge.remove();
                        }
                    }
                }

                // Update Kanban Task Card
                function updateKanbanTaskCard(taskId, task) {
                    const kanbanTaskCard = document.querySelector('.kanban-task-card[data-task-id="' + taskId +
                        '"]');
                    if (kanbanTaskCard && task) {
                        const title = kanbanTaskCard.querySelector('.kanban-task-title');
                        const description = kanbanTaskCard.querySelector('.kanban-task-description');
                        const client = kanbanTaskCard.querySelector('.kanban-task-client');
                        const priorityBadge = kanbanTaskCard.querySelector('.kanban-task-badge.priority-' + (
                            task.priority || ''));
                        const assignedToBadge = kanbanTaskCard.querySelector(
                            '.kanban-task-badge[style*="background: #6366f1"]');
                        const dueDateBadge = kanbanTaskCard.querySelector(
                            '.kanban-task-badge[style*="background: #8b5cf6"]');

                        if (title) title.textContent = task.title || 'No Title';
                        if (description) description.textContent = task.description || '';
                        if (client) {
                            const clientName = (task.client && task.client.company_name) ? task.client
                                .company_name : 'N/A';
                            client.innerHTML = '<i class="fas fa-building"></i> ' + escapeHtml(clientName);
                        }
                        if (priorityBadge) priorityBadge.textContent = task.priority || 'Unknown';
                        if (assignedToBadge) {
                            const assignedName = (task.assigned_to && task.assigned_to.name) ? task.assigned_to
                                .name : 'Unassigned';
                            assignedToBadge.textContent = assignedName;
                        }

                        if (dueDateBadge && task.due_date) {
                            const dueDate = new Date(task.due_date);
                            if (!isNaN(dueDate.getTime())) {
                                dueDateBadge.textContent = dueDate.toLocaleDateString('en-US', {
                                    month: 'short',
                                    day: 'numeric'
                                });
                            }
                        } else if (!dueDateBadge && task.due_date) {
                            const metaContainer = kanbanTaskCard.querySelector('.kanban-task-meta');
                            if (metaContainer) {
                                const dueDate = new Date(task.due_date);
                                if (!isNaN(dueDate.getTime())) {
                                    const dueDateSpan = document.createElement('span');
                                    dueDateSpan.className = 'kanban-task-badge';
                                    dueDateSpan.style.background = '#8b5cf6';
                                    dueDateSpan.style.color = 'white';
                                    dueDateSpan.textContent = dueDate.toLocaleDateString('en-US', {
                                        month: 'short',
                                        day: 'numeric'
                                    });
                                    metaContainer.appendChild(dueDateSpan);
                                }
                            }
                        }
                    }
                }

                // Enhanced Update Status Modal Function
                window.updateStatusModal = function(taskId) {
                    try {
                        if (!taskId || isNaN(taskId)) {
                            console.error('Invalid Task ID:', taskId);
                            showToast('error', 'Invalid task ID.');
                            return;
                        }
                        console.log('Opening update status modal for task ID:', taskId);
                        const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                        const statusSelect = document.getElementById('status');
                        const updateStatusBtn = document.getElementById('updateStatusBtn');
                        updateStatusBtn.dataset.taskId = taskId;
                        modal.show();

                        // Get the base URL from current location
                        const baseUrl = window.location.origin + '/admin/tasks';

                        axios.get(baseUrl + '/' + taskId, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                console.log('Task status data received:', response.data);
                                const task = response.data.task || {};
                                statusSelect.value = task.status || '';
                            })
                            .catch(error => {
                                console.error('Fetch Task Status Error:', error);
                                const message = error.response && error.response.status === 404 ?
                                    'Task with ID ' + taskId + ' not found. It may have been deleted.' :
                                    (error.response && error.response.data && error.response.data
                                        .message) ?
                                    error.response.data.message : 'Failed to load task status.';
                                showToast('error', message);
                            });

                        updateStatusBtn.onclick = function() {
                            const taskId = this.dataset.taskId;
                            const form = document.getElementById('updateStatusForm');
                            const status = form.querySelector('#status').value;

                            if (!form || !status) {
                                console.error('Form or status not found:', {
                                    form: form,
                                    status: status
                                });
                                showToast('error', 'Please select a status.');
                                return;
                            }

                            const csrfToken = document.querySelector('meta[name="csrf-token"]');
                            if (!csrfToken) {
                                console.error('CSRF token not found');
                                showToast('error',
                                    'Security token not found. Please refresh the page.');
                                return;
                            }

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
                                    console.log('Task status updated successfully:', response.data);
                                    const message = (response.data && response.data.message) ?
                                        response.data.message : 'Task status updated successfully!';
                                    showToast('success', message);
                                    const modal = bootstrap.Modal.getInstance(document
                                        .getElementById('updateStatusModal'));
                                    modal.hide();

                                    // Update both grid and kanban views
                                    updateGridTaskCard(taskId, response.data.task);
                                    if (kanbanView.style.display === 'block') {
                                        const taskCard = document.querySelector(
                                            '.kanban-task-card[data-task-id="' + taskId + '"]');
                                        if (taskCard) {
                                            const newStatusColumn = document.querySelector(
                                                '.kanban-cards[data-status="' + response.data
                                                .task.status + '"]');
                                            if (newStatusColumn && taskCard.parentElement !==
                                                newStatusColumn) {
                                                newStatusColumn.appendChild(taskCard);
                                                updateKanbanTaskCard(taskId, response.data.task);
                                                updateColumnCounts();
                                            }
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Update Task Status Error:', error);
                                    const message = (error.response && error.response.data && error
                                            .response.data.message) ?
                                        error.response.data.message :
                                        'Failed to update task status.';
                                    showToast('error', message);
                                });
                        };
                    } catch (error) {
                        console.error('Error in updateStatusModal:', error);
                        showToast('error', 'Failed to open update status modal.');
                    }
                }

                // Enhanced Edit Task Modal Function
                window.editTaskModal = function(taskId) {
                        try {
                            if (!taskId || isNaN(taskId)) {
                                console.error('Invalid Task ID:', taskId);
                                showToast('error', 'Invalid task ID.');
                                return;
                            }

                            console.log('Fetching edit form for task ID:', taskId);
                            const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                            const content = document.getElementById('editTaskContent');
                            const saveBtn = document.getElementById('saveEditBtn');

                            content.innerHTML =
                                '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading edit form...</p></div>';

                            saveBtn.dataset.taskId = taskId;
                            modal.show();

                            // Clear TomSelect instances on modal close
                            document.getElementById('editTaskModal').addEventListener('hidden.bs.modal',
                                function() {
                                    content.innerHTML = '';
                                    const selectors = ['#edit_client_id', '#edit_assigned_to',
                                        '#edit_call_log_id', '#edit_priority', '#edit_status'
                                    ];
                                    selectors.forEach(selector => {
                                        const element = document.querySelector(selector);
                                        if (element && element.tomselect) {
                                            element.tomselect.destroy();
                                        }
                                    });
                                }, {
                                    once: true
                                });

                            const baseUrl = window.location.origin + '/admin/tasks';
                            axios.get(baseUrl + '/' + taskId + '/edit', {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => {
                                    console.log('Edit form data received for task ID:', taskId, response
                                        .data);
                                    const task = response.data.task || {};
                                    const clients = response.data.clients || [];
                                    const employees = response.data.employees || [];
                                    const callLogs = response.data.callLogs || [];
                                    const statusOptions = response.data.statusOptions || {};

                                    // Helper functions for date formatting
                                    const formatDateTimeLocal = (dateString) => {
                                        if (!dateString || typeof dateString !== 'string') return '';
                                        const normalizedDateString = dateString.length === 16 &&
                                            dateString.includes('T') ?
                                            dateString + ':00.000Z' : dateString;
                                        const date = new Date(normalizedDateString);
                                        if (isNaN(date.getTime())) {
                                            console.warn('Invalid date parsed:', dateString);
                                            return '';
                                        }
                                        return date.toISOString().slice(0, 16);
                                    };

                                    const formatDate = (dateString) => {
                                        if (!dateString || typeof dateString !== 'string') return '';
                                        const date = new Date(dateString);
                                        if (isNaN(date.getTime())) {
                                            console.warn('Invalid date parsed:', dateString);
                                            return '';
                                        }
                                        return date.toISOString().split('T')[0];
                                    };

                                    // Build options HTML
                                    let clientOptions = '<option value="">Select a client</option>';
                                    clients.forEach(client => {
                                        const selected = client.id == task.client_id ? 'selected' :
                                            '';
                                        const clientText = escapeHtml((client.name || '') + ' (' + (
                                            client.company_name || '') + ')');
                                        clientOptions += '<option value="' + client.id + '" ' +
                                            selected + '>' + clientText + '</option>';
                                    });

                                    let employeeOptions = '<option value="">Select an employee</option>';
                                    employees.forEach(employee => {
                                        const selected = employee.id === (task.assigned_to || '') ?
                                            'selected' : '';
                                        const employeeName = escapeHtml(employee.name || 'N/A');
                                        employeeOptions += '<option value="' + employee.id + '" ' +
                                            selected + '>' + employeeName + '</option>';
                                    });

                                    let callLogOptions = '<option value="">-- No Call Log --</option>';
                                    callLogs.forEach(log => {
                                        const selected = log.id == task.call_log_id ? 'selected' :
                                            '';
                                        const logText = escapeHtml(log.id + ' - ' + (log.subject ||
                                                'No Subject') + ' (' +
                                            (log.call_date ? new Date(log.call_date)
                                                .toISOString().split('T')[0] : 'N/A') + ')');
                                        callLogOptions += '<option value="' + log.id + '" ' +
                                            selected + '>' + logText + '</option>';
                                    });

                                    let statusOptionsHtml = '';
                                    Object.entries(statusOptions).forEach(([value, label]) => {
                                        const selected = task.status == value ? 'selected' : '';
                                        const colorMap = {
                                            1: '#f3f4f6',
                                            2: '#3b82f6',
                                            3: '#6b7280',
                                            4: '#dc2626',
                                            5: '#eab308',
                                            6: '#8b5cf6',
                                            7: '#22c55e',
                                            8: '#10b981'
                                        };
                                        const color = colorMap[value] || '#d1d5db';
                                        statusOptionsHtml += '<option value="' + value + '" ' +
                                            selected + ' data-color="' + color + '">' +
                                            escapeHtml(label) + '</option>';
                                    });

                                    content.innerHTML = '<form id="editTaskForm" data-task-id="' + taskId +
                                        '">' +
                                        '<div class="mb-4">' +
                                        '<div class="section-title">' +
                                        '<div class="icon"><i class="fas fa-tasks"></i></div>' +
                                        '<div>' +
                                        '<div>Task Information</div>' +
                                        '<div class="section-subtext">Provide details about the task.</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="subcard">' +
                                        '<div class="row g-3">' +
                                        '<div class="col-md-6">' +
                                        '<label for="edit_title" class="form-label">Task Title <span class="text-danger">*</span></label>' +
                                        '<div class="input-group">' +
                                        '<span class="input-group-text"><i class="fas fa-heading"></i></span>' +
                                        '<input type="text" name="title" id="edit_title" class="form-control" value="' +
                                        escapeHtml(task.title || '') +
                                        '" required placeholder="e.g. Website Redesign">' +
                                        '<div class="error-messages" id="edit_title-error"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="col-md-6">' +
                                        '<label for="edit_client_id" class="form-label">Client <span class="text-danger">*</span></label>' +
                                        '<div class="input-group">' +
                                        '<span class="input-group-text"><i class="fas fa-user-tie"></i></span>' +
                                        '<select name="client_id" id="edit_client_id" class="form-select client-select" required>' +
                                        clientOptions +
                                        '</select>' +
                                        '<div class="error-messages" id="edit_client_id-error"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="col-md-6">' +
                                        '<label for="edit_assigned_to" class="form-label">Assign To</label>' +
                                        '<div class="input-group">' +
                                        '<span class="input-group-text"><i class="fas fa-user"></i></span>' +
                                        '<select name="assigned_to" id="edit_assigned_to" class="form-select">' +
                                        employeeOptions +
                                        '</select>' +
                                        '<div class="error-messages" id="edit_assigned_to-error"></div>' +
                                        '</div>' +
                                        '<small class="form-text text-muted">Leave empty to keep unassigned</small>' +
                                        '</div>' +
                                        '<div class="col-md-6">' +
                                        '<label for="edit_priority" class="form-label">Priority <span class="text-danger">*</span></label>' +
                                        '<div class="input-group">' +
                                        '<span class="input-group-text"><i class="fas fa-exclamation-circle"></i></span>' +
                                        '<select name="priority" id="edit_priority" class="form-control" required>' +
                                        '<option value="low" ' + (task.priority === 'low' ? 'selected' :
                                            '') + ' data-color="#22c55e">Low</option>' +
                                        '<option value="medium" ' + (task.priority === 'medium' ?
                                            'selected' : '') + ' data-color="#eab308">Medium</option>' +
                                        '<option value="high" ' + (task.priority === 'high' ? 'selected' :
                                            '') + ' data-color="#f97316">High</option>' +
                                        '<option value="urgent" ' + (task.priority === 'urgent' ?
                                            'selected' : '') + ' data-color="#ef4444">Urgent</option>' +
                                        '</select>' +
                                        '<div class="error-messages" id="edit_priority-error"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="col-md-6">' +
                                        '<label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>' +
                                        '<div class="input-group">' +
                                        '<span class="input-group-text"><i class="fas fa-info-circle"></i></span>' +
                                        '<select name="status" id="edit_status" class="form-select" required>' +
                                        statusOptionsHtml +
                                        '</select>' +
                                        '<div class="error-messages" id="edit_status-error"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="col-md-6">' +
                                        '<label for="edit_call_log_id" class="form-label">Related Call Log (optional)</label>' +
                                        '<div class="input-group">' +
                                        '<span class="input-group-text"><i class="fas fa-phone"></i></span>' +
                                        '<select name="call_log_id" id="edit_call_log_id" class="form-select">' +
                                        callLogOptions +
                                        '</select>' +
                                        '<div class="error-messages" id="edit_call_log_id-error"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="mb-4">' +
                                        '<div class="section-title">' +
                                        '<div class="icon"><i class="fas fa-calendar-alt"></i></div>' +
                                        '<div>' +
                                        '<div>Scheduling & Planning</div>' +
                                        '<div class="section-subtext">Set dates for task tracking.</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="subcard">' +
                                        '<div class="row g-3">' +
                                        '<div class="col-md-6">' +
                                        '<label for="edit_due_date" class="form-label">Due Date</label>' +
                                        '<div class="input-group">' +
                                        '<input type="text" name="due_date" id="edit_due_date" class="form-control nepali-date" value="' +
                                        (formatDate(task.due_date) || '') + '">' +
                                        '<span class="input-group-text"><i class="fas fa-calendar"></i></span>' +
                                        '<div class="error-messages" id="edit_due_date-error"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="col-md-6" id="edit_started_at_group">' +
                                        '<label for="edit_started_at" class="form-label">Started At</label>' +
                                        '<div class="input-group">' +
                                        '<input type="datetime-local" name="started_at" id="edit_started_at" class="form-control" value="' +
                                        (formatDateTimeLocal(task.started_at) || '') + '">' +
                                        '<span class="input-group-text"><i class="fas fa-clock"></i></span>' +
                                        '<div class="error-messages" id="edit_started_at-error"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="col-md-6" id="edit_completed_at_group">' +
                                        '<label for="edit_completed_at" class="form-label">Completed At</label>' +
                                        '<div class="input-group">' +
                                        '<input type="datetime-local" name="completed_at" id="edit_completed_at" class="form-control" value="' +
                                        (formatDateTimeLocal(task.completed_at) || '') + '">' +
                                        '<span class="input-group-text"><i class="fas fa-clock"></i></span>' +
                                        '<div class="error-messages" id="edit_completed_at-error"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="mb-4">' +
                                        '<div class="section-title">' +
                                        '<div class="icon"><i class="fas fa-file-alt"></i></div>' +
                                        '<div>' +
                                        '<div>Description & Notes</div>' +
                                        '<div class="section-subtext">Provide detailed task information and additional notes.</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="subcard">' +
                                        '<div class="row g-3">' +
                                        '<div class="col-12">' +
                                        '<label for="edit_description" class="form-label">Description <span class="text-danger">*</span></label>' +
                                        '<textarea name="description" id="edit_description" class="form-control" rows="5" required ' +
                                        'placeholder="Detailed description of what needs to be done...">' +
                                        escapeHtml(task.description || '') + '</textarea>' +
                                        '<div class="error-messages" id="edit_description-error"></div>' +
                                        '</div>' +
                                        '<div class="col-12">' +
                                        '<label for="edit_notes" class="form-label">Notes</label>' +
                                        '<textarea name="notes" id="edit_notes" class="form-control" rows="3" ' +
                                        'placeholder="Any additional notes or comments...">' + escapeHtml(
                                            task.notes || '') + '</textarea>' +
                                        '<div class="error-messages" id="edit_notes-error"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</form>';

                                    // Initialize TomSelect after a short delay
                                    setTimeout(() => {
                                        const selectors = ['#edit_client_id', '#edit_assigned_to',
                                            '#edit_call_log_id', '#edit_priority',
                                            '#edit_status'
                                        ];
                                        selectors.forEach(selector => {
                                            const element = document.querySelector(
                                                selector);
                                            if (element && element.tomselect) {
                                                element.tomselect.destroy();
                                            }
                                        });

                                        // Initialize all TomSelect instances
                                        new TomSelect('#edit_client_id', {
                                            create: false,
                                            placeholder: 'Select a client',
                                            allowEmptyOption: true
                                        });

                                        new TomSelect('#edit_assigned_to', {
                                            create: false,
                                            placeholder: 'Select an employee',
                                            allowEmptyOption: true
                                        });

                                        new TomSelect('#edit_call_log_id', {
                                            create: false,
                                            placeholder: '-- No Call Log --',
                                            allowEmptyOption: true
                                        });

                                        new TomSelect('#edit_priority', {
                                            create: false,
                                            placeholder: 'Select priority',
                                            allowEmptyOption: false
                                        });

                                        new TomSelect('#edit_status', {
                                            create: false,
                                            placeholder: 'Select status',
                                            allowEmptyOption: false
                                        });
                                    }, 100);

                                    // // Initialize Nepali Date Picker for inputs with nepali-date class, we don't need it js uses MutationObserver should handle most
                                    // window.NepaliDateHelper.initByClass('nepali-date');
                                    // window.NepaliDateHelper.initByAttribute();

                            // Save Edit button handler
                            saveBtn.onclick = function() {
                                const taskId = this.dataset.taskId;
                                const form = document.getElementById('editTaskForm');

                                if (!form || !taskId) {
                                    console.error('Form or task ID not found:', {
                                        form: form,
                                        taskId: taskId
                                    });
                                    showToast('error', 'Form or task ID not found.');
                                    return;
                                }

                                // Validate required fields
                                const requiredFields = ['title', 'description', 'priority',
                                    'status', 'client_id'
                                ];
                                let hasErrors = false;
                                requiredFields.forEach(field => {
                                    const element = form.querySelector('[name="' +
                                        field + '"]');
                                    const errorElement = document.getElementById(
                                        'edit_' + field + '-error');
                                    if (errorElement) errorElement.innerHTML = '';

                                    if (!element || !element.value) {
                                        console.error('Missing required field: ' +
                                            field);
                                        if (errorElement) {
                                            const fieldName = field.replace('_', ' ')
                                                .replace(/\b\w/g, c => c.toUpperCase());
                                            errorElement.innerHTML = fieldName +
                                                ' is required.';
                                        }
                                        hasErrors = true;
                                    }
                                });

                                if (hasErrors) {
                                    showToast('error', 'Please fill in all required fields.');
                                    return;
                                }

                                // Prepare form data
                                const data = {
                                    title: document.getElementById('edit_title').value ||
                                        '',
                                    description: document.getElementById('edit_description')
                                        .value || '',
                                    priority: document.getElementById('edit_priority')
                                        .value || '',
                                    status: parseInt(document.getElementById('edit_status')
                                        .value) || null,
                                    client_id: document.getElementById('edit_client_id')
                                        .value || '',
                                    assigned_to: document.getElementById('edit_assigned_to')
                                        .value || null,
                                    due_date: document.getElementById('edit_due_date')
                                        .value || null,
                                    started_at: document.getElementById('edit_started_at')
                                        .value || null,
                                    completed_at: document.getElementById(
                                        'edit_completed_at').value || null,
                                    call_log_id: document.getElementById('edit_call_log_id')
                                        .value || null,
                                    notes: document.getElementById('edit_notes').value ||
                                        null
                                };

                                console.log('Sending update request with data:', data);

                                const csrfToken = document.querySelector(
                                    'meta[name="csrf-token"]');
                                if (!csrfToken) {
                                    console.error('CSRF token not found');
                                    showToast('error',
                                        'Security token not found. Please refresh the page.'
                                    );
                                    return;
                                }

                                axios.put('/admin/tasks/' + taskId, data, {
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken.getAttribute(
                                                'content')
                                        }
                                    })
                                    .then(response => {
                                        console.log('Task updated successfully:', response
                                            .data);
                                        const message = (response.data && response.data
                                                .message) ? response.data.message :
                                            'Task updated successfully!';
                                        showToast('success', message);
                                        const modal = bootstrap.Modal.getInstance(document
                                            .getElementById('editTaskModal'));
                                        modal.hide();

                                        // Update both grid and kanban views
                                        updateGridTaskCard(taskId, response.data.task);
                                        if (kanbanView.style.display === 'block') {
                                            const taskCard = document.querySelector(
                                                '.kanban-task-card[data-task-id="' +
                                                taskId + '"]');
                                            if (taskCard) {
                                                const newStatusColumn = document
                                                    .querySelector(
                                                        '.kanban-cards[data-status="' +
                                                        response.data.task.status + '"]');
                                                if (newStatusColumn && taskCard
                                                    .parentElement !== newStatusColumn) {
                                                    newStatusColumn.appendChild(taskCard);
                                                    updateKanbanTaskCard(taskId, response
                                                        .data.task);
                                                    updateColumnCounts();
                                                } else {
                                                    updateKanbanTaskCard(taskId, response
                                                        .data.task);
                                                }
                                            }
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Update Task Error:', error);
                                        if (error.response && error.response.data && error
                                            .response.data.errors) {
                                            const errors = error.response.data.errors;
                                            Object.keys(errors).forEach(field => {
                                                const errorElement = document
                                                    .getElementById('edit_' +
                                                        field + '-error');
                                                if (errorElement) {
                                                    errorElement.innerHTML = errors[
                                                        field].map(error =>
                                                        escapeHtml(error)).join(
                                                        '<br>');
                                                }
                                            });
                                            showToast('error',
                                                'Please correct the errors in the form.'
                                            );
                                        } else {
                                            const message = (error.response && error
                                                    .response.data && error.response.data
                                                    .message) ?
                                                error.response.data.message :
                                                'Failed to update task.';
                                            showToast('error', message);
                                        }
                                    });
                            };
                        })
                    .catch(error => {
                        console.error('Edit Task Error for task ID:', taskId, error.response ||
                            error);
                        const message = error.response && error.response.status === 404 ?
                            'Task with ID ' + taskId + ' not found. It may have been deleted.' :
                            (error.response && error.response.data && error.response.data
                                .message) ?
                            error.response.data.message : 'Failed to load edit form.';
                        content.innerHTML = '<div class="alert alert-danger">' + message +
                            '</div>';
                        showToast('error', message);
                    });
            } catch (error) {
                console.error('Error in editTaskModal:', error);
                showToast('error', 'Failed to load edit task modal.');
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
                                .data.statusOptions[task.status]) ?
                            response.data.statusOptions[task.status] : 'Unknown');

                        let assignedTo = 'Unassigned';
                        if (task.assigned_to) {
                            if (task.assigned_to.user && task.assigned_to.user.name) {
                                assignedTo = escapeHtml(task.assigned_to.user.name);
                            } else if (task.assigned_to.name) {
                                assignedTo = escapeHtml(task.assigned_to.name);
                            }
                        }

                        const createdBy = task.created_by && task.created_by.name ? escapeHtml(
                            task.created_by.name) : 'System';
                        const createdAt = task.created_at ? new Date(task.created_at)
                            .toLocaleString('en-US', {
                                dateStyle: 'medium',
                                timeStyle: 'short'
                            }) : 'N/A';
                        const startedAt = task.started_at ? new Date(task.started_at)
                            .toLocaleString('en-US', {
                                dateStyle: 'medium',
                                timeStyle: 'short'
                            }) : 'N/A';
                        const completedAt = task.completed_at ? new Date(task.completed_at)
                            .toLocaleString('en-US', {
                                dateStyle: 'medium',
                                timeStyle: 'short'
                            }) : 'N/A';
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
                            '<tr><th>Created:</th><td>' + createdAt + '</td></tr>' +
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
                                .message) ?
                            error.response.data.message : 'Failed to load task details.';
                        content.innerHTML = '<div class="alert alert-danger">' + message +
                            '</div>';
                        showToast('error', message);
                    });
            } catch (error) {
                console.error('Error in showTaskModal:', error);
                showToast('error', 'Failed to open task modal.');
            }
        }

        // Delete Task Modal Function
        window.deleteTaskModal = function(taskId, taskTitle) {
            try {
                if (!taskId || isNaN(taskId)) {
                    console.error('Invalid Task ID:', taskId);
                    showToast('error', 'Invalid task ID.');
                    return;
                }
                console.log('Opening delete modal for task ID:', taskId);
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                document.getElementById('taskTitle').textContent = escapeHtml(taskTitle || 'N/A');
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                confirmBtn.dataset.taskId = taskId;
                modal.show();

                confirmBtn.onclick = function() {
                    if (!taskId || isNaN(taskId)) {
                        console.error('Invalid Task ID:', taskId);
                        showToast('error', 'Invalid task ID.');
                        return;
                    }

                    console.log('Sending delete request for task ID:', taskId);

                    // Add loading state to button
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Deleting...';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('CSRF token not found');
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-trash me-1"></i>Delete Task';
                        showToast('error',
                            'Security token not found. Please refresh the page.');
                        return;
                    }

                    axios.delete('/admin/tasks/' + taskId, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                            }
                        })
                        .then(response => {
                            console.log('Task deleted successfully:', response.data);
                            const message = (response.data && response.data.message) ?
                                response.data.message : 'Task deleted successfully!';
                            showToast('success', message);

                            // Remove the task card from both views
                            const gridTaskCard = document.querySelector(
                                '.task-card[data-task-id="' + taskId + '"]');
                            const kanbanTaskCard = document.querySelector(
                                '.kanban-task-card[data-task-id="' + taskId + '"]');

                            if (gridTaskCard) {
                                gridTaskCard.classList.add('animate-fade-out');
                                setTimeout(() => gridTaskCard.remove(), 300);
                            }

                            if (kanbanTaskCard) {
                                kanbanTaskCard.classList.add('animate-fade-out');
                                setTimeout(() => {
                                    kanbanTaskCard.remove();
                                    updateColumnCounts();
                                }, 300);
                            }

                            // Close the modal
                            const modal = bootstrap.Modal.getInstance(document
                                .getElementById('deleteModal'));
                            modal.hide();

                            // Reset button state
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-trash me-1"></i>Delete Task';
                        })
                        .catch(error => {
                            console.error('Delete Task Error:', error);
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-trash me-1"></i>Delete Task';
                            const message = (error.response && error.response.data && error
                                    .response.data.message) ?
                                error.response.data.message : 'Failed to delete task.';
                            showToast('error', message);
                        });
                };
            } catch (error) {
                console.error('Error in deleteTaskModal:', error);
                showToast('error', 'Failed to open delete modal.');
            }
        }

        // Filter toggle functionality
        const filterToggleBtn = document.getElementById('filterToggleBtn');
        const filterContainer = document.querySelector('.filter-container');
        if (filterToggleBtn && filterContainer) {
            filterToggleBtn.addEventListener('click', function() {
                filterContainer.classList.toggle('d-none');
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
                }
            });
        }

        // Initialize TomSelect for filter form dropdowns
        document.querySelectorAll('.client-select').forEach(select => {
            if (!select.tomselect) {
                new TomSelect(select, {
                    create: false,
                    placeholder: 'Select a client',
                    allowEmptyOption: true,
                    render: {
                        option: function(item, escape) {
                            return '<div>' + escape(item.text) + '</div>';
                        },
                        item: function(item, escape) {
                            return '<div>' + escape(item.text) + '</div>';
                        }
                    }
                });
            }
        });

        //for status
        document.querySelectorAll('.status-select').forEach(select => {
            if (!select.tomselect) {
                new TomSelect(select, {
                    create: false,
                    placeholder: 'Select Status',
                    allowEmptyOption: true,
                    render: {
                        option: function(item, escape) {
                            return '<div>' + escape(item.text) + '</div>';
                        },
                        item: function(item, escape) {
                            return '<div>' + escape(item.text) + '</div>';
                        }
                    }
                });
            }
        });

        //TomSelect for Priority filter form dropdowns
        document.querySelectorAll('.priority-select').forEach(select => {
            if (!select.tomselect) {
                new TomSelect(select, {
                    create: false,
                    placeholder: 'Select a client',
                    allowEmptyOption: true,
                    render: {
                        option: function(item, escape) {
                            return '<div>' + escape(item.text) + '</div>';
                        },
                        item: function(item, escape) {
                            return '<div>' + escape(item.text) + '</div>';
                        }
                    }
                });
            }
        });

        //TomSelect for Employee filter form dropdowns
        document.querySelectorAll('.employee-select').forEach(select => {
            if (!select.tomselect) {
                new TomSelect(select, {
                    create: false,
                    placeholder: 'Select a Employee',
                    allowEmptyOption: true,
                    render: {
                        option: function(item, escape) {
                            return '<div>' + escape(item.text) + '</div>';
                        },
                        item: function(item, escape) {
                            return '<div>' + escape(item.text) + '</div>';
                        }
                    }
                });
            }
        });

        // Initialize drag and drop on page load if kanban view is active
        if (kanbanView && kanbanView.style.display === 'block') {
            initializeDragAndDrop();
        }
        });
        })();
    </script>
@endpush
