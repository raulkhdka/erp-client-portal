@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 dashboard-title"><i class="fas fa-tachometer-alt me-2"></i>My Dashboard</h1>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 stat-label">Total Assigned Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $totalAssignedServices }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-puzzle-piece fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1 stat-label">Active Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $activeServices }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 stat-label">Inactive Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $inactiveServices }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-minus-circle fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up" data-aos-delay="400">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 stat-label">Your Assigned Employee</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $assignedEmployees->user->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up" data-aos-delay="500">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1 stat-label">Suspended / Expired Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $suspendedServices + $expiredServices }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow-enhanced h-100 py-2 stat-card" data-aos="fade-up" data-aos-delay="600">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1 stat-label">Total Documents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">{{ $totalDocuments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-enhanced mb-4 data-card" data-aos="fade-right">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-puzzle-piece me-2"></i>My Services</h6>
                    <a href="{{ route('clients.services.index') }}" class="btn btn-sm btn-light btn-hover">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($clientServices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-professional mb-0">
                                <thead class="table-header">
                                    <tr>
                                        <th class="border-dark">Service Name</th>
                                        <th class="border-dark">Status</th>
                                        <th class="border-dark">Assigned Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clientServices as $service)
                                        <tr class="table-row-hover">
                                            <td class="border-dark">{{ $service->name }}</td>
                                            <td class="border-dark">
                                                @php
                                                    $statusColor = '';
                                                    switch($service->pivot->status) {
                                                        case 'active': $statusColor = 'success'; break;
                                                        case 'inactive': $statusColor = 'warning'; break;
                                                        case 'suspended': $statusColor = 'danger'; break;
                                                        case 'expired': $statusColor = 'secondary'; break;
                                                        default: $statusColor = 'info'; break;
                                                    }
                                                @endphp
                                                <span class="badge badge-professional bg-{{ $statusColor }}">
                                                    {{ ucfirst($service->pivot->status) }}
                                                </span>
                                            </td>
                                            <td class="border-dark">{{ $service->pivot->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <i class="fas fa-puzzle-piece fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No services found for your account.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-enhanced mb-4 data-card" data-aos="fade-left">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-info">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-user-tie me-2"></i>My Assigned Employees</h6>
                    <a href="{{ route('clients.employees.index') }}" class="btn btn-sm btn-light btn-hover">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($clientEmployees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-professional mb-0">
                                <thead class="table-header">
                                    <tr>
                                        <th class="border-dark">Name</th>
                                        <th class="border-dark">Email</th>
                                        <th class="border-dark">Access Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clientEmployees as $employee)
                                        <tr class="table-row-hover">
                                            <td class="border-dark">{{ $employee->user->name ?? 'N/A' }}</td>
                                            <td class="border-dark">{{ $employee->user->email ?? 'N/A' }}</td>
                                            <td class="border-dark">
                                                <span class="badge badge-professional bg-{{ $employee->pivot->is_active ? 'success' : 'secondary' }}">
                                                    {{ $employee->pivot->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No employees assigned to your account.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-enhanced mb-4 data-card" data-aos="fade-up">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-secondary">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-file-alt me-2"></i>Recent Documents</h6>
                    <a href="{{ route('clients.documents.index') }}" class="btn btn-sm btn-light btn-hover">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentDocuments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-professional mb-0">
                                <thead class="table-header">
                                    <tr>
                                        <th class="border-dark">Title</th>
                                        <th class="border-dark">Uploaded By</th>
                                        <th class="border-dark">Upload Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentDocuments as $document)
                                        <tr class="table-row-hover">
                                            <td class="border-dark">{{ $document->title }}</td>
                                            <td class="border-dark">{{ $document->uploadedByUser->name ?? 'N/A' }}</td>
                                            <td class="border-dark">{{ $document->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No documents found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')
    <style>
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
        .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
        .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
        .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
        .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
        .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
        .border-left-secondary { border-left: 0.25rem solid #858796 !important; }

        /* Gradient Headers */
        .bg-gradient-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
        .bg-gradient-success { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
        .bg-gradient-info { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); }
        .bg-gradient-secondary { background: linear-gradient(135deg, #858796 0%, #5a5c69 100%); }

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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fa-spinner {
            animation: spin 2s linear infinite;
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

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@push('scripts')
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

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