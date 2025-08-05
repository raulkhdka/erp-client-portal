@extends('layouts.app')

@section('title', 'My Tasks')

@section('breadcrumb')
    <span class="breadcrumb-item active">My Tasks</span>
@endsection

@push('styles')
    <style>
        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 80%;
        }

        .btn-group .btn {
            margin-right: 4px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
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
            opacity: 1; /* Gradient border appears on hover */
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
        }

        .task-btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .task-btn-primary {
            background: linear-gradient(135deg, #a78bfa, #f472b6);
            color: white;
        }

        .task-btn-primary:hover {
            background: linear-gradient(135deg, #9333ea, #ec4899);
            box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
        }

        .task-btn-success {
            background: linear-gradient(135deg, #10b981, #34d399);
            color: white;
        }

        .task-btn-success:hover {
            background: linear-gradient(135deg, #059669, #22c55e);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
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
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow animate-slide-up">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-tasks text-purple-600 me-2"></i>My Tasks
                        </h3>
                    </div>

                    <div class="card-body border-bottom bg-light py-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card bg-white h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Total Tasks</h6>
                                                <h4 class="mb-0">{{ $tasksCount['total'] }}</h4>
                                            </div>
                                            <div class="rounded-circle bg-purple-100 p-2">
                                                <i class="fas fa-tasks text-purple-600"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-white h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Pending</h6>
                                                <h4 class="mb-0">{{ $tasksCount['pending'] }}</h4>
                                            </div>
                                            <div class="rounded-circle bg-yellow-100 p-2">
                                                <i class="fas fa-clock text-yellow-600"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-white h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">In Progress</h6>
                                                <h4 class="mb-0">{{ $tasksCount['in_progress'] }}</h4>
                                            </div>
                                            <div class="rounded-circle bg-blue-100 p-2">
                                                <i class="fas fa-spinner text-blue-600"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-white h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Completed</h6>
                                                <h4 class="mb-0">{{ $tasksCount['completed'] }}</h4>
                                            </div>
                                            <div class="rounded-circle bg-green-100 p-2">
                                                <i class="fas fa-check text-green-600"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <form class="d-flex gap-2" method="GET" action="{{ route('employees.tasks.index') }}">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Pending</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>In Progress</option>
                                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>On Hold</option>
                                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Pending Review</option>
                                    <option value="7" {{ request('status') == '7' ? 'selected' : '' }}>Completed</option>
                                    <option value="8" {{ request('status') == '8' ? 'selected' : '' }}>Resolved</option>
                                </select>
                                <select name="priority" class="form-select form-select-sm">
                                    <option value="">All Priority</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-purple-600">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                @if (request()->hasAny(['status', 'priority']))
                                    <a href="{{ route('employees.tasks.index') }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($tasks->isEmpty())
                            <div class="alert alert-info animate-fade-in">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle me-3"></i>
                                    <div>
                                        <h5 class="mb-1">No tasks found</h5>
                                        <p class="mb-0">No tasks assigned to you at the moment.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="tasks-grid">
                                @foreach ($tasks as $index => $task)
                                    <div class="task-card animate-fade-in"
                                        style="animation-delay: {{ $index * 0.1 }}s">
                                        <div class="task-card-body">
                                            <h4 class="task-title">
                                                {{ $task->title }}
                                            </h4>

                                            @if ($task->description)
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
                                            </div>

                                            <div class="task-actions">
                                                <a href="{{ route('employees.tasks.show', $task) }}"
                                                    class="task-btn task-btn-primary">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                @if (!in_array($task->status, [7, 8]))
                                                    <button type="button" class="task-btn task-btn-success"
                                                        onclick="updateTaskStatus('{{ $task->id }}')">
                                                        <i class="fas fa-check me-1"></i> Update
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 d-flex justify-content-center">
                                {{ $tasks->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Status Update Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius: 12px; border: 1px solid rgba(139, 92, 246, 0.15); box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.1); background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(0,0,0,0.1);">
                    <h5 class="modal-title">
                        <i class="fas fa-tasks text-purple-600 me-2"></i>
                        Update Task Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateStatusForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status" class="form-label fw-semibold">New Status</label>
                            <select name="status" id="status" class="form-select" required
                                style="border-radius: 8px;">
                                <option value="2">In Progress</option>
                                <option value="3">On Hold</option>
                                <option value="4">Pending Review</option>
                                <option value="7">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid rgba(0,0,0,0.1);">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            style="border-radius: 8px;">
                            Cancel
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
            const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
            const form = document.getElementById('updateStatusForm');
            form.action = `{{ url('employee/tasks') }}/${taskId}/status`;
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

            // Add loading state when updating status
            document.getElementById('updateStatusForm').addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
                submitBtn.disabled = true;
            });

            // Add ripple effect to buttons
            document.querySelectorAll('.task-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
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