@extends('layouts.app')

@section('title', 'All Tasks')

@section('breadcrumb')
    <span class="breadcrumb-item active">All Tasks</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Task
        </a>
    </div>
@endsection

@push('styles')
    <style>
        /* Hide the sidebar */
        #sidebar, .sidebar-wrapper, .sidebar-menu {
                display: none !important;
            }

       /* Adjust content wrapper to full width when sidebar is hidden */
       .content-wrapper {
            margin-left: 0 !important;
        }

        /* Card styles */
        .card {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
        }

        .card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        .bg-opacity-10 {
            opacity: 0.1;
        }

        .rounded-circle {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .rounded-circle i {
            font-size: 1.2rem;
        }

        /* Task Card Styles */
        .task-card {
            background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 14px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(139, 92, 246, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .task-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 14px;
            padding: 2px;
            background: linear-gradient(135deg, #a78bfa, #f472b6, #fb7185, #fdba74);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .task-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            background: linear-gradient(145deg, #f1f5f9 0%, #d1dae6 100%);
        }

        .task-card:hover::before {
            opacity: 1;
        }

        .task-card-body {
            padding: 16px;
            position: relative;
        }

        .task-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.3s ease;
        }

        .task-title:hover {
            color: #7c3aed;
        }

        .task-description {
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 12px;
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
            gap: 8px;
            margin-bottom: 12px;
            padding: 8px;
            background: rgba(139, 92, 246, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(139, 92, 246, 0.08);
        }

        .task-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #475569;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .task-meta-item:hover {
            color: #1e293b;
            transform: translateY(-1px);
        }

        .task-meta-item i {
            width: 14px;
            color: #94a3b8;
            font-size: 0.75rem;
            transition: color 0.2s ease;
        }

        .task-meta-item:hover i {
            color: #a78bfa;
        }

        .task-badges {
            display: flex;
            gap: 6px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .task-badge {
            padding: 4px 10px;
            border-radius: 16px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: linear-gradient(135deg, #a78bfa, #f472b6);
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .task-badge.bg-danger {
            background: linear-gradient(135deg, #f87171, #ef4444);
        }

        .task-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.4s ease;
        }

        .task-card:hover .task-badge::before {
            left: 100%;
        }

        .task-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
            padding-top: 12px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            flex-wrap: wrap;
        }

        .task-btn {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
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
            min-width: 80px;
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

        .task-btn-view:hover {
            background: linear-gradient(135deg, #26a69a, #0d9488);
            box-shadow: 0 4px 10px rgba(20, 184, 166, 0.3);
        }

        .task-btn-edit {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: white;
        }

        .task-btn-edit:hover {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }

        .task-btn-delete {
            background: linear-gradient(135deg, #f87171, #ef4444);
            color: white;
        }

        .task-btn-delete:hover {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }

        .task-btn-update-status {
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
            color: white;
        }

        .task-btn-update-status:hover {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
        }

        .task-due-date {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            background: rgba(0, 0, 0, 0.03);
        }

        .task-due-date.overdue {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .task-due-date.upcoming {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .task-due-date.no-date {
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, 0.2);
        }

        /* Animation classes */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slideUp 0.4s ease-out forwards;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Grid Layout for 4 Cards */
        .tasks-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 16px 0;
        }

        @media (max-width: 1200px) {
            .tasks-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 14px;
            }
        }

        @media (max-width: 992px) {
            .tasks-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
        }

        @media (max-width: 768px) {
            .tasks-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .task-meta {
                flex-direction: column;
                gap: 8px;
            }

            .task-actions {
                flex-direction: column;
            }

            .task-btn {
                width: 100%;
                text-align: center;
            }
        }

        /* Loading state */
        .task-card.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .task-card.loading::after {
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

        /* Pagination */
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

        /* Search Form */
        .search-form {
            max-width: 400px;
            margin-bottom: 1rem;
        }

        .search-form .input-group {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
        }

        .search-form .form-control {
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .search-form .btn {
            border: none;
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
            color: white;
            transition: all 0.3s ease;
        }

        .search-form .btn:hover {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
        }

        /* Modal Button */
        .btn-purple-600 {
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
            color: white;
            border: none;
        }

        .btn-purple-600:hover {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
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
                            <i class="fas fa-tasks text-purple-600 me-2"></i>All Tasks
                        </h3>
                    </div>

                    <div class="card-body border-bottom bg-light py-3">
                        <!-- Search Form -->
                        <form method="GET" action="{{ route('admin.tasks.all') }}" class="search-form">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        @if($tasks->count() > 0)
                            <div class="tasks-grid">
                                @foreach($tasks as $index => $task)
                                    <div class="task-card animate-fade-in" style="animation-delay: {{ $index * 0.1 }}s">
                                        <div class="task-card-body">
                                            <h4 class="task-title">
                                                {{ $task->title }}
                                            </h4>

                                            @if($task->description)
                                                <p class="task-description">
                                                    {{ $task->description }}
                                                </p>
                                            @endif

                                            <div class="task-meta">
                                                <div class="task-meta-item">
                                                    <i class="fas fa-building"></i>
                                                    <span>{{ $task->client->company_name ?? 'N/A' }}</span>
                                                </div>
                                                <div class="task-meta-item">
                                                    <i class="fas fa-user"></i>
                                                    <span>{{ $task->adminCreator->name ?? 'System' }}</span>
                                                </div>
                                                @if($task->assignedTo && $task->assignedTo->user)
                                                    <div class="task-meta-item">
                                                        <i class="fas fa-user-tie"></i>
                                                        <span>{{ $task->assignedTo->user->name }}</span>
                                                    </div>
                                                @endif
                                                @if($task->callLog)
                                                    <div class="task-meta-item">
                                                        <i class="fas fa-phone"></i>
                                                        <a href="{{ route('admin.call-logs.show', $task->callLog) }}"
                                                           class="text-decoration-none">
                                                            Call #{{ $task->callLog->id }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="task-badges">
                                                <span class="task-badge">
                                                    <i class="fas fa-signal me-1"></i>
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                                <span class="task-badge">
                                                    <i class="fas fa-spinner me-1"></i>
                                                    {{ $task->status_label }}
                                                </span>
                                                @if($task->is_overdue)
                                                    <span class="task-badge bg-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        Overdue
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="task-actions">
                                                <a href="{{ route('admin.tasks.show', $task) }}"
                                                   class="task-btn task-btn-view">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                @if(Auth::user()->isAdmin())
                                                    <a href="{{ route('admin.tasks.edit', $task) }}"
                                                       class="task-btn task-btn-edit">
                                                        <i class="fas fa-edit me-1"></i> Edit
                                                    </a>
                                                    <button type="button"
                                                            class="task-btn task-btn-delete delete-btn"
                                                            data-task-id="{{ $task->id }}"
                                                            data-task-title="{{ $task->title }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal">
                                                        <i class="fas fa-trash me-1"></i> Delete
                                                    </button>
                                                    @if(!in_array($task->status, [7, 8]))
                                                        <button type="button"
                                                                class="task-btn task-btn-update-status"
                                                                onclick="updateTaskStatus('{{ $task->id }}')">
                                                            <i class="fas fa-sync-alt me-1"></i> Update Status
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 d-flex justify-content-center">
                                {{ $tasks->links() }}
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
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                 style="border-radius: 12px; border: 1px solid rgba(139, 92, 246, 0.15); box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.1); background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(0,0,0,0.1);">
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
                <div class="modal-footer" style="border-top: 1px solid rgba(0,0,0,0.1);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            style="border-radius: 8px;">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="border-radius: 8px;">
                            <i class="fas fa-trash me-1"></i>Delete Task
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Status Update Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                 style="border-radius: 12px; border: 1px solid rgba(139, 92, 246, 0.15); box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.1); background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(0,0,0,0.1);">
                    <h5 class="modal-title" id="updateStatusModalLabel">
                        <i class="fas fa-tasks text-purple-600 me-2"></i>
                        Update Task Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status" class="form-label fw-semibold">New Status</label>
                            <select name="status" id="status" class="form-select" required
                                    style="border-radius: 8px;">
                                @foreach(\App\Models\Task::getStatusOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid rgba(0,0,0,0.1);">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                style="border-radius: 8px;">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-purple-600" style="border-radius: 8px;">
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
        function updateTaskStatus(taskId) {
            console.log('Task ID:', taskId); // Debug
            const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
            const form = document.getElementById('updateStatusForm');
            form.action = '{{ route('admin.tasks.update-all', ['task' => ':taskId']) }}'.replace(':taskId', taskId);
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.task-card').forEach(function(card) {
                observer.observe(card);
            });

            // Delete confirmation functionality
            let currentTaskId = null;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const deleteForm = document.getElementById('deleteForm');

            document.querySelectorAll('.delete-btn').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentTaskId = this.getAttribute('data-task-id');
                    const taskTitle = this.getAttribute('data-task-title');
                    document.getElementById('taskTitle').textContent = taskTitle;
                    deleteForm.action = '{{ route('admin.tasks.destroy', ['task' => ':taskId']) }}'.replace(':taskId', currentTaskId);
                    deleteModal.show();
                });
            });

            // Add loading state when updating status
            document.getElementById('updateStatusForm').addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
                submitBtn.disabled = true;
            });

            // Add ripple effect to buttons
            document.querySelectorAll('.task-btn').forEach(function(btn) {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });

        // Add ripple effect CSS
        const rippleCSS = `
.task-btn {
    position: relative;
    overflow: hidden;
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
`;

        const style = document.createElement('style');
        style.textContent = rippleCSS;
        document.head.appendChild(style);
    </script>
@endpush