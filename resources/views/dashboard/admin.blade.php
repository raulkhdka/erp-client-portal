@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
    @push('styles')
        <style>
            .table-professional {
                border: 2px solid #000 !important;
                border-collapse: separate !important;
                border-spacing: 0 !important;
                width: 100% !important;
            }

            .table-professional th,
            .table-professional td {
                border: 1px solid #000 !important;
                padding: 12px 15px !important;
                vertical-align: middle !important;
                background: white !important;
            }

            .table-professional th {
                background: #f8f9fc !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                font-size: 0.75rem !important;
                letter-spacing: 0.5px !important;
                color: #5a5c69 !important;
                border-bottom: 2px solid #000 !important;
            }

            .table-professional tbody tr:hover {
                background-color: #f8f9fc !important;
            }

            /* Badge styling */
            .badge-professional {
                padding: 0.375rem 0.75rem !important;
                font-size: 0.75rem !important;
                font-weight: 600 !important;
                border-radius: 0.35rem !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
            }

            /* Bootstrap badge colors */
            .bg-success {
                background-color: #28a745 !important;
                color: white !important;
            }

            .bg-warning {
                background-color: #ffc107 !important;
                color: #212529 !important;
            }

            .bg-danger {
                background-color: #dc3545 !important;
                color: white !important;
            }

            .bg-primary {
                background-color: #007bff !important;
                color: white !important;
            }

            .bg-secondary {
                background-color: #6c757d !important;
                color: white !important;
            }

            .bg-info {
                background-color: #17a2b8 !important;
                color: white !important;
            }

            .bg-dark {
                background-color: #343a40 !important;
                color: white !important;
            }
            /* Enhanced Card Styling */
        .shadow-enhanced {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
            transition: all 0.3s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.3) !important;
        }

        .data-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.3rem 1.5rem 0 rgba(58, 59, 69, 0.2) !important;
        }

        /* Border Colors */
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }

        .border-left-secondary {
            border-left: 0.25rem solid #858796 !important;
        }

        /* Gradient Headers */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }

        /* Professional Table Styling */
        .table-professional {
            border: 2px solid #000 !important;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-professional th,
        .table-professional td {
            border: 1px solid #000 !important;
            padding: 12px 15px;
            vertical-align: middle;
        }

        .table-header {
            background: linear-gradient(135deg, #f8f9fc 0%, #e3e6f0 100%);
        }

        .table-header th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #5a5c69;
            border-bottom: 2px solid #000 !important;
        }

        .table-row-hover {
            transition: all 0.2s ease-in-out;
        }

        .table-row-hover:hover {
            background-color: #f8f9fc;
            transform: scale(1.01);
        }

        /* Badge Styling */
        .badge-professional {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Button Enhancements */
        .btn-hover {
            transition: all 0.3s ease-in-out;
            border-radius: 0.35rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
        }

        .btn-action {
            transition: all 0.2s ease-in-out;
            border-radius: 0.25rem;
            font-weight: 500;
        }

        .btn-action:hover {
            transform: scale(1.05);
        }

        /* Stat Card Animations */
        .stat-icon {
            transition: all 0.3s ease-in-out;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
            color: #4e73df !important;
        }

        .stat-number {
            transition: all 0.3s ease-in-out;
        }

        .stat-card:hover .stat-number {
            color: #4e73df !important;
        }

        .stat-label {
            transition: all 0.3s ease-in-out;
        }

        /* Dashboard Title */
        .dashboard-title {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        /* Font Weight Helper */
        .font-weight-medium {
            font-weight: 500 !important;
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .stat-card:hover {
                transform: none;
            }

            .data-card:hover {
                transform: none;
            }

            .table-row-hover:hover {
                transform: none;
            }
        }

        /* Loading Animation for Icons */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .fa-spinner {
            animation: spin 2s linear infinite;
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Card Body Padding Override for Tables */
        .data-card .card-body.p-0 {
            padding: 0 !important;
        }

        /* Empty State Styling */
        .text-center i.fa-3x {
            opacity: 0.3;
        }
        </style>
    @endpush

    @section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 dashboard-title"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h1>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 stat-label">Total Clients
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $totalClients }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up"
                data-aos-delay="200">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1 stat-label">Active Clients
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $activeClients }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 stat-label">Total Employees
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $totalEmployees }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up"
                data-aos-delay="400">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 stat-label">Total Call
                                Logs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $totalCallLogs }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-phone fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row of Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up"
                data-aos-delay="500">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1 stat-label">Pending Call
                                Logs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $pendingCallLogs }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-phone-slash fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up"
                data-aos-delay="600">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1 stat-label">Total Tasks
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $totalTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up"
                data-aos-delay="700">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 stat-label">Pending Tasks
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $pendingTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up" data-aos-delay="800">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 stat-label">In Progress Tasks
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $inProgressTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data Tables -->
    <div class="row">
        <!-- Recent Clients -->
        <div class="col-lg-6">
            <div class="card shadow-enhanced mb-4 data-card" data-aos="fade-right">
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-users me-2"></i>Recent Clients</h6>
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-light btn-hover">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($recentClients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-professional mb-0">
                                <thead class="table-header">
                                    <tr>
                                        <th class="border-dark">Company</th>
                                        <th class="border-dark">Contact</th>
                                        <th class="border-dark">Status</th>
                                        <th class="border-dark">Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentClients as $client)
                                        <tr class="table-row-hover">
                                            <td class="border-dark">{{ $client->company_name }}</td>
                                            <td class="border-dark">
                                                {{ $client->phones->first() ? $client->phones->first()->phone : 'N/A' }}
                                            </td>
                                            <td class="border-dark">
                                                <span
                                                    class="badge badge-professional bg-{{ $client->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($client->status) }}
                                                </span>
                                            </td>
                                            <td class="border-dark">{{ $client->created_at->format('M d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No clients found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Call Logs -->
        <div class="col-lg-6">
            <div class="card shadow-enhanced mb-4 data-card" data-aos="fade-left">
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-success">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-phone me-2"></i>Recent Call Logs</h6>
                    <a href="{{ route('admin.call-logs.index') }}" class="btn btn-sm btn-light btn-hover">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($recentCallLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-professional mb-0">
                                <thead class="table-header">
                                    <tr>
                                        <th class="border-dark">Client</th>
                                        <th class="border-dark">Subject</th>
                                        <th class="border-dark">Status</th>
                                        <th class="border-dark">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentCallLogs as $callLog)
                                        <tr class="table-row-hover">
                                            <td class="border-dark">{{ $callLog->client->company_name ?? 'N/A' }}</td>
                                            <td class="border-dark">{{ Str::limit($callLog->subject, 20) }}</td>
                                            <td class="border-dark">
                                                <span class="badge badge-professional bg-{{ $callLog->status_color }}">
                                                    {{ $callLog->status_label }}
                                                </span>
                                            </td>
                                            <td class="border-dark">{{ $callLog->created_at->format('M d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <i class="fas fa-phone fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No call logs found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- DEBUG Tasks Section - Add this to your view temporarily -->
    <div class="row">
        <div class="col-lg-12">

            {{-- <!-- Debug Information -->
            <div class="alert alert-info mb-3">
                <h5><i class="fas fa-bug"></i> DEBUG INFO:</h5>
                <p><strong>Recent Tasks Count:</strong> {{ $recentTasks->count() ?? 'Variable not set' }}</p>
                <p><strong>Recent Tasks Type:</strong> {{ gettype($recentTasks) }}</p>
                <p><strong>Recent Tasks Empty Check:</strong>
                    {{ $recentTasks->isEmpty() ? 'TRUE (Empty)' : 'FALSE (Has Data)' }}</p>

                @if (isset($recentTasks) && $recentTasks->count() > 0)
                    <h6>First Task Data:</h6>
                    <pre style="font-size: 12px; max-height: 300px; overflow-y: auto;">
{{ print_r($recentTasks->first()->toArray(), true) }}
                </pre>
                @else
                    <p class="text-danger">No tasks found or variable not set</p>
                @endif
            </div> --}}

            <!-- Actual Tasks Card -->
            <div class="card shadow-enhanced mb-4 data-card" data-aos="fade-up">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-info">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-tasks me-2"></i>Recent Tasks
                        <span class="badge badge-light ms-2">{{ $recentTasks->count() }}</span>
                    </h6>
                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-sm btn-light btn-hover">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">

                    <!-- Check if recentTasks variable exists and has data -->
                    @if (isset($recentTasks))
                        @if ($recentTasks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-professional mb-0">
                                    <thead class="table-header">
                                        <tr>
                                            <th class="border-dark">ID</th>
                                            <th class="border-dark">Title</th>
                                            <th class="border-dark">Client</th>
                                            <th class="border-dark">Assigned To</th>
                                            <th class="border-dark">Priority</th>
                                            <th class="border-dark">Status</th>
                                            <th class="border-dark">Due Date</th>
                                            <th class="border-dark">Created</th>
                                            <th class="border-dark">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentTasks as $index => $task)
                                            <tr class="table-row-hover">
                                                <td class="border-dark">{{ $task->id }}</td>
                                                <td class="border-dark">{{ $task->title ?? 'No Title' }}</td>
                                                <td class="border-dark">{{ $task->client->company_name ?? 'No Client' }}
                                                </td>
                                                <td class="border-dark">{{ $task->assignedTo->name ?? 'Unassigned' }}</td>
                                                <td class="border-dark">{{ $task->priority ?? 'No Priority' }}</td>
                                                <td class="border-dark">{{ $task->status ?? 'No Status' }}</td>
                                                <td class="border-dark">
                                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                                </td>
                                                <td class="border-dark">{{ $task->created_at->format('M d') }}</td>
                                                <td class="border-dark">
                                                    <a href="{{ route('admin.tasks.show', $task) }}"
                                                        class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center">
                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No recent tasks found.</p>
                                <small class="text-info">recentTasks collection is empty but variable exists</small>
                            </div>
                        @endif
                    @else
                        <div class="p-4 text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <p class="text-danger">$recentTasks variable is not set in the controller.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-enhanced data-card" data-aos="fade-up">
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-file-alt me-2"></i>Document Management
                    </h6>
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn-light btn-hover">
                        <i class="fas fa-cog me-1"></i>Manage Documents
                    </a>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-folder-open fa-3x text-primary"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 lead">
                                Upload, approve, or manage all documents in the system.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <script>
        // Initialize AOS with custom settings for your layout
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 50, // Reduced offset
            disable: false,
            startEvent: 'DOMContentLoaded',
            initClassName: 'aos-init',
            animatedClassName: 'aos-animate',
            useClassNames: false,
            disableMutationObserver: false,
            debounceDelay: 50,
            throttleDelay: 99,
        });

        // Force AOS to detect elements immediately
        setTimeout(function() {
            AOS.refresh();
            // Force all elements to animate immediately for dashboard
            document.querySelectorAll('[data-aos]').forEach(function(el) {
                el.classList.add('aos-animate');
            });
        }, 100);

        // Add smooth number counting animation
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');

            statNumbers.forEach(function(element) {
                const finalNumber = parseInt(element.textContent);
                let currentNumber = 0;
                const increment = finalNumber / 50;

                const timer = setInterval(function() {
                    currentNumber += increment;
                    if (currentNumber >= finalNumber) {
                        element.textContent = finalNumber;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(currentNumber);
                    }
                }, 30);
            });
        });

        // Add table row click functionality
        document.querySelectorAll('.table-row-hover').forEach(function(row) {
            row.addEventListener('click', function(e) {
                if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON') {
                    const link = row.querySelector('a[href]');
                    if (link) {
                        window.location.href = link.href;
                    }
                }
            });
        });
    </script>
@endpush

