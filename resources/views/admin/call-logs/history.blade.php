@extends('layouts.app')

@section('title', 'Call History')

@section('breadcrumb')
    <a href="{{ route('admin.call-logs.index') }}" class="breadcrumb-link">Call Logs</a>
    <span class="breadcrumb-item active">Call History</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.call-logs.index') }}" class="btn btn-modern">
            <i class="fas fa-arrow-left me-2"></i>Back to Call Logs
        </a>
    </div>
@endsection

@push('styles')
    <style>
        /* Import modern font */
      

        /* Hide the sidebar */
        #sidebar, .sidebar-wrapper, .sidebar-menu {
            display: none !important;
        }

        :root {
            --primary-color: #1a73e8;
            --secondary-color: #34c759;
            --background-color: #f8fafc;
            --card-background: rgba(255, 255, 255, 0.95);
            --text-color: #1f2937;
            --accent-color: #64748b;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --table-header-color: #10b981;
            --table-subheader-color: #e5e7eb;
        }

        /* Full-width content with subtle background */
        .container-fluid {
            padding: 0 !important;
            marginıyordu: 0 !important;
            width: 100%;
            background: linear-gradient(135deg, var(--background-color) 0%, #e2e8f0 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .container-fluid::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
            opacity: 0.3;
            z-index: -1;
        }

        .main-content {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .modern-card {
            border-radius: 16px;
            background: var(--card-background);
            backdrop-filter: blur(10px);
            box-shadow: 0 6px 20px var(--shadow-color);
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            margin: 1.5rem;
            animation: slideIn 0.8s ease-out;
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

        /* Filter form styling */
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: center;
        }

        .filter-form select {
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: var(--text-color);
            min-width: 200px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .filter-form select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.2);
        }

        .filter-form .btn-modern {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .filter-form .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .table-container {
            display: flex;
            width: 100%;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: var(--card-background);
            box-shadow: 0 4px 12px var(--shadow-color);
            animation: slideIn 1s ease-out 0.2s both;
        }

        .table-left, .table-right {
            flex: 1;
            width: 50%;
            border-collapse: collapse;
            table-layout: auto;
            font-family: 'Inter', sans-serif;
        }

        .table-left {
            border-right: 1px solid #4b5563;
        }

        .table-left thead th,
        .table-right thead th {
            background: var(--table-subheader-color);
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
            transition: background 0.3s ease;
        }

        .table-left thead th.section-header,
        .table-right thead th.section-header {
            background: var(--table-header-color);
            color: #ffffff;
        }

        .table-left tbody td,
        .table-right tbody td {
            padding: 1rem;
            font-size: 0.75rem;
            color: var(--text-color);
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
        }

        .table-left tbody tr:last-child td,
        .table-right tbody tr:last-child td {
            border-bottom: none;
        }

        .table-left tbody tr:nth-child(odd),
        .table-right tbody tr:nth-child(odd) {
            background-color: #f8fafc;
        }

        .table-left tbody tr:hover,
        .table-right tbody tr:hover {
            background-color: #e5e7eb;
            transform: scale(1.01);
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        a.text-decoration-none {
            color: var(--primary-color);
            transition: color 0.2s ease, transform 0.2s ease;
        }

        a.text-decoration-none:hover {
            color: #2563eb;
            transform: scale(1.05);
            text-decoration: none;
        }

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 500;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            color: #0369a1;
            transition: transform 0.2s ease;
            animation: pulse 2s infinite;
        }

        .badge:hover {
            transform: scale(1.1);
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.2rem;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            color: #ffffff;
            text-decoration: none;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            text-align: center;
            color: var(--accent-color);
            font-family: 'Inter', sans-serif;
            animation: fadeIn 1s ease-out;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            animation: bounce 2s infinite;
        }

        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .empty-state a {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #ffffff;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: pulse 2s infinite;
        }

        .empty-state a:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        /* Pagination styling */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            font-family: 'Inter', sans-serif;
        }

        .pagination .page-item .page-link {
            border: none;
            border-radius: 8px;
            color: var(--text-color);
            background: #f1f5f9;
            margin: 0 0.3rem;
            padding: 0.5rem 1rem;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            color: #ffffff;
        }

        .pagination .page-item .page-link:hover {
            background: var(--secondary-color);
            color: #ffffff;
            transform: translateY(-2px);
        }

        @media (max-width: 992px) {
            .modern-card {
                margin: 1rem;
            }

            .modern-card .card-body {
                padding: 1.2rem;
            }

            .table-left thead th,
            .table-right thead th {
                font-size: 0.65rem;
                padding: 0.6rem;
            }

            .table-left tbody td,
            .table-right tbody td {
                font-size: 0.7rem;
                padding: 0.6rem;
                max-width: 140px;
            }

            .table-container {
                flex-direction: column;
            }

            .table-left, .table-right {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #4b5563;
            }

            .table-right {
                border-bottom: none;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-form select {
                min-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .modern-card {
                margin: 0.5rem;
            }

            .modern-card .card-body {
                padding: 1rem;
            }

            .table-left thead th,
            .table-right thead th {
                font-size: 0.6rem;
                padding: 0.5rem;
            }

            .table-left tbody td,
            .table-right tbody td {
                font-size: 0.65rem;
                padding: 0.5rem;
                max-width: 100px;
            }

            .empty-state {
                padding: 2rem;
            }

            .empty-state i {
                font-size: 2rem;
            }

            .empty-state p {
                font-size: 0.9rem;
            }
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
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
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 main-content">
            <!-- Call History -->
            <div class="card modern-card">
                <div class="card-body">
                    <!-- Filter Form -->
                    <form class="filter-form" method="GET" action="{{ route('admin.call-logs.call-history') }}">
                        <select name="caller_name" id="caller_name">
                            <option value="">Select Name</option>
                            @foreach($callerNames as $name)
                                <option value="{{ $name }}" {{ old('caller_name', request('caller_name')) == $name ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <select name="caller_phone" id="caller_phone">
                            <option value="">Select Phone Number</option>
                            @foreach($callerPhones as $phone)
                                <option value="{{ $phone }}" {{ old('caller_phone', request('caller_phone')) == $phone ? 'selected' : '' }}>{{ $phone }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-modern"><i class="fas fa-filter me-2"></i>Load Data</button>
                    </form>

                    @if(request()->has('caller_name') || request()->has('caller_phone'))
                        @if($callLogs->count() > 0)
                            <div class="table-container">
                                <!-- Follow-up Details -->
                                <table class="table-left">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="section-header"><i class="fas fa-bell me-2"></i>Follow-up Details</th>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-user me-1"></i>Name</th>
                                            <th><i class="fas fa-phone me-1"></i>Phone</th>
                                            <th><i class="fas fa-calendar me-1"></i>Next Call Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($callLogs as $callLog)
                                            <tr>
                                                <td>{{ $callLog->caller_name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($callLog->caller_phone)
                                                        <a href="tel:{{ $callLog->caller_phone }}" class="text-decoration-none">{{ $callLog->caller_phone }}</a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($callLog->follow_up_date)
                                                        {{ $callLog->follow_up_date->format('M d, Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- Call Details -->
                                <table class="table-right">
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="section-header"><i class="fas fa-phone-alt me-2"></i>Call Details</th>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone me-1"></i>Phone</th>
                                            <th><i class="fas fa-tag me-1"></i>Call Type</th>
                                            <th><i class="fas fa-calendar-alt me-1"></i>Call Date</th>
                                            <th><i class="fas fa-clock me-1"></i>Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($callLogs as $callLog)
                                            <tr>
                                                <td>
                                                    @if($callLog->caller_phone)
                                                        <a href="tel:{{ $callLog->caller_phone }}" class="text-decoration-none">{{ $callLog->caller_phone }}</a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($callLog->call_type)
                                                        <span class="badge">{{ ucfirst($callLog->call_type) }}</span>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $callLog->call_date ? $callLog->call_date->format('M d, Y H:i') : 'N/A' }}</td>
                                                <td>{{ $callLog->duration_minutes ?? 0 }} minutes</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $callLogs->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-phone-slash"></i>
                                <p>No call logs found for the selected filters.</p>
                                <a href="{{ route('admin.call-logs.create') }}">Create a New Call Log</a>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-filter"></i>
                            <p>Please select a name or phone number to load call logs.</p>
                            <a href="{{ route('admin.call-logs.create') }}">Create a New Call Log</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection