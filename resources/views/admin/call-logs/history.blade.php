@extends('layouts.app')

@section('title', 'Call History')

@section('layout_class', 'no-sidebar')

@section('breadcrumb')
    <a href="{{ route('admin.call-logs.index') }}" class="breadcrumb-link">Call Logs</a>
    <span class="breadcrumb-item active">Call History</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.call-logs.index') }}" class="btn btn-modern">
            <i class="fas fa-arrow-left me-2"></i>Back to Call Logs
        </a>
        <button type="button" class="btn btn-modern" data-bs-toggle="modal" data-bs-target="#createCallLogModal">
            <i class="fas fa-plus me-2"></i>Add New Call Log
        </button>
    </div>
@endsection

@push('styles')
    <style>
        /* Hide the Sidebar Container */
        #sidebar {
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

        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
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

        .full-screen-content {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
        }

        .content-body {
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #e5e7eb;
        }

        .content-body::-webkit-scrollbar {
            width: 6px;
        }

        .content-body::-webkit-scrollbar-track {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .content-body::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: center;
            padding: 1rem;
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
            margin-bottom: 1.5rem;
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

        .table-full {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            font-family: 'Inter', sans-serif;
            border: 1px solid #e5e7eb;
            background: var(--card-background);
            box-shadow: 0 4px 12px var(--shadow-color);
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .table-left thead th,
        .table-right thead th,
        .table-full thead th {
            background: #10b981;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            padding: 1rem;
            text-align: center;
            border: 1px solid #000000;
            white-space: nowrap;
            transition: background 0.3s ease;
        }

        .table-left thead th.section-header,
        .table-right thead th.section-header,
        .table-full thead th.section-header {
            background: #10b981;
            color: #ffffff;
            border: 1px solid #000000;
        }

        .table-left tbody td,
        .table-right tbody td,
        .table-full tbody td {
            padding: 1rem;
            font-size: 0.75rem;
            color: var(--text-color);
            border: 1px solid #000000;
            white-space: nowrap;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
        }

        .table-left tbody tr:last-child td,
        .table-right tbody tr:last-child td,
        .table-full tbody tr:last-child td {
            border-bottom: 1px solid #000000;
        }

        .table-left tbody tr:nth-child(odd),
        .table-right tbody tr:nth-child(odd),
        .table-full tbody tr:nth-child(odd) {
            background-color: #f8fafc;
        }

        .table-left tbody tr:hover,
        .table-right tbody tr:hover,
        .table-full tbody tr:hover {
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

        /* Modal Styling */
        .modal-content {
            background: #f1f5f9;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(2, 6, 23, 0.06);
        }

        .modal-header {
            border-bottom: 1px solid #eef2f6;
            background: #f1f5f9;
        }

        .modal-footer {
            border-top: 1px solid #eef2f6;
            background: #f1f5f9;
        }

        .modal-body {
            max-height: 80vh; /* Increased from 70vh for larger modal */
            overflow-y: auto;
            padding: 1.5rem;
        }

        /* Increase modal size */
        .modal-xl {
            max-width: 90vw; /* Wider than default modal-xl (1140px) */
            width: 100%;
        }

        /* Reduce font sizes in modal */
        .modal-body .form-label {
            font-size: 0.9rem; /* Smaller than default */
            color: #000000;
        }

        .modal-body .form-control,
        .modal-body .form-select,
        .modal-body textarea.form-control {
            font-size: 0.85rem; /* Smaller input text */
            border-radius: 10px;
            border: 0.5px solid #000000;
            background: #f1f5f9;
            color: #000000;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .modal-body .form-control:focus,
        .modal-body .form-select:focus,
        .modal-body textarea.form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 .2rem rgba(16, 185, 129, 0.15);
            background: #fff;
            color: #000000;
        }

        .modal-body .form-text {
            font-size: 0.8rem; /* Smaller form text */
            color: #6b7280;
        }

        .modal-body .section-title {
            font-size: 1rem; /* Slightly smaller section titles */
            font-weight: 800;
            color: #10b981;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .modal-body .section-subtext {
            font-size: 0.8rem; /* Smaller subtext */
            color: #6b7280;
        }

        .modal-body .input-group-text {
            font-size: 0.85rem; /* Smaller icons */
            background: #f1f5f9;
            border: 1px solid #eef2f6;
            color: #64748b;
            min-width: 38px; /* Slightly smaller for proportionality */
            justify-content: center;
        }

        .modal-body .form-check-label {
            font-size: 0.9rem; /* Smaller checkbox label */
            color: #10b981;
        }

        .modal-body .alert-danger {
            font-size: 0.85rem; /* Smaller error messages */
            border-radius: 10px;
            border: 2px solid #fee2e2;
            background: #fff1f2;
            color: #dc2626;
        }

        .modal-body .btn-primary {
            font-size: 0.9rem; /* Smaller button text */
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
        }

        .modal-body .btn-ghost-danger {
            font-size: 0.9rem; /* Smaller button text */
            border: 1px solid #fee2e2;
            color: #dc2626;
            background: #f1f5f9;
        }

        .section-title .icon {
            width: 32px; /* Slightly smaller icon */
            height: 32px;
            border-radius: 8px;
            background: #eafaf3;
            color: #10b981;
            display: grid;
            place-items: center;
        }

        .subcard {
            border: 1px dashed #1f2937;
            border-radius: 12px;
            padding: 1rem;
            background: #f1f5f9;
        }

        .btn-ghost-danger:hover {
            background: #ffeaea;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0ea5a0 0%, #047857 100%);
        }

        .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        .form-check-input {
            accent-color: #10b981;
        }

        @media (min-width: 992px) {
            .call-type-container {
                flex: 0 0 auto;
                width: 60%;
            }

            .call-date-container {
                flex: 0 0 auto;
                width: 40%;
                margin-left: 0;
            }

            .subject-container {
                flex: 0 0 auto;
                width: 50%;
            }

            .priority-container,
            .status-container {
                flex: 0 0 auto;
                width: 25%;
            }
        }

        @media (max-width: 992px) {
            .content-body {
                padding: 1rem;
            }

            .table-left thead th,
            .table-right thead th,
            .table-full thead th {
                font-size: 0.65rem;
                padding: 0.6rem;
                border: 1px solid #000000;
            }

            .table-left tbody td,
            .table-right tbody td,
            .table-full tbody td {
                font-size: 0.7rem;
                padding: 0.6rem;
                max-width: 140px;
                border: 1px solid #000000;
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

            /* Adjust modal font sizes for smaller screens */
            .modal-body .form-label {
                font-size: 0.85rem;
            }

            .modal-body .form-control,
            .modal-body .form-select,
            .modal-body textarea.form-control {
                font-size: 0.8rem;
            }

            .modal-body .form-text {
                font-size: 0.75rem;
            }

            .modal-body .section-title {
                font-size: 0.95rem;
            }

            .modal-body .section-subtext {
                font-size: 0.75rem;
            }

            .modal-body .input-group-text {
                font-size: 0.8rem;
                min-width: 36px;
            }

            .modal-body .form-check-label {
                font-size: 0.85rem;
            }

            .modal-body .alert-danger {
                font-size: 0.8rem;
            }

            .modal-body .btn-primary,
            .modal-body .btn-ghost-danger {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 768px) {
            .content-body {
                padding: 0.5rem;
            }

            .table-left thead th,
            .table-right thead th,
            .table-full thead th {
                font-size: 0.6rem;
                padding: 0.5rem;
                border: 1px solid #000000;
            }

            .table-left tbody td,
            .table-right tbody td,
            .table-full tbody td {
                font-size: 0.65rem;
                padding: 0.5rem;
                max-width: 100px;
                border: 1px solid #000000;
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

            /* Further reduce modal font sizes for small screens */
            .modal-body .form-label {
                font-size: 0.8rem;
            }

            .modal-body .form-control,
            .modal-body .form-select,
            .modal-body textarea.form-control {
                font-size: 0.75rem;
            }

            .modal-body .form-text {
                font-size: 0.7rem;
            }

            .modal-body .section-title {
                font-size: 0.9rem;
            }

            .modal-body .section-subtext {
                font-size: 0.7rem;
            }

            .modal-body .input-group-text {
                font-size: 0.75rem;
                min-width: 34px;
            }

            .modal-body .form-check-label {
                font-size: 0.8rem;
            }

            .modal-body .alert-danger {
                font-size: 0.75rem;
            }

            .modal-body .btn-primary,
            .modal-body .btn-ghost-danger {
                font-size: 0.8rem;
            }
        }

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
    <div class="full-screen-content">
        <div class="content-body">
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
                    <!-- Split Table for Today Call Appointment and Call Details -->
                    <div class="table-container">
                        <!-- Today Call Appointment -->
                        <table class="table-left">
                            <thead>
                                <tr>
                                    <th colspan="3" class="section-header"><i class="fas fa-bell me-2"></i>Today Call Appointment</th>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-phone me-1"></i>Phone</th>
                                    <th><i class="fas fa-user me-1"></i>Customer</th>
                                    <th><i class="fas fa-comment me-1"></i>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($callLogs->where('follow_up_date', now()->format('Y-m-d')) as $callLog)
                                    <tr>
                                        <td>
                                            @if($callLog->caller_phone)
                                                <a href="tel:{{ $callLog->caller_phone }}" class="text-decoration-none">{{ $callLog->caller_phone }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $callLog->caller_name ?? 'N/A' }}{{ $callLog->organization ? ' (' . $callLog->organization . ')' : '' }}</td>
                                        <td>{{ $callLog->description ?? 'N/A' }}</td>
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
                    <!-- Full-width Calls Table -->
                    <table class="table-full">
                        <thead>
                            <tr>
                                <th><i class="fas fa-phone me-1"></i>Phone</th>
                                <th><i class="fas fa-user me-1"></i>Customer</th>
                                <th><i class="fas fa-calendar me-1"></i>Next Call</th>
                                <th><i class="fas fa-exclamation-circle me-1"></i>Priority</th>
                                <th><i class="fas fa-comment me-1"></i>Description</th>
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
                                    <td>{{ $callLog->caller_name ?? 'N/A' }}{{ $callLog->organization ? ' (' . $callLog->organization . ')' : '' }}</td>
                                    <td>
                                        @if($callLog->follow_up_date)
                                            {{ $callLog->follow_up_date->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($callLog->priority)
                                            <span class="badge">{{ ucfirst($callLog->priority) }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $callLog->description ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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

    <!-- Create Call Log Modal -->
    <div class="modal fade" id="createCallLogModal" tabindex="-1" aria-labelledby="createCallLogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCallLogModalLabel">Record New Call</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.call-logs.store') }}" method="POST">
                        @csrf

                        {{-- Call Information --}}
                        <div class="mb-4">
                            <div class="section-title">
                                <div class="icon"><i class="fas fa-phone"></i></div>
                                <div>
                                    <div>Call Information</div>
                                    <div class="section-subtext">Details about the call and assignment.</div>
                                </div>
                            </div>
                            <div class="subcard">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="employee_id" class="form-label">Assign to Employee</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id">
                                                <option value="">Select employee...</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('employee_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">Leave empty to assign to yourself</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                                            <select class="form-select client-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                                <option value="">Select client...</option>
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}" data-contact-name="{{ $client->user ? $client->user->name ?? 'N/A' : 'N/A' }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->name }} ({{ $client->company_name ?? 'N/A' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('client_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-7 call-type-container">
                                        <label for="call_type" class="form-label">Call Type <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                            <select class="form-select @error('call_type') is-invalid @enderror" id="call_type" name="call_type" required>
                                                <option value="">Select type...</option>
                                                <option value="incoming" {{ old('call_type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                                <option value="outgoing" {{ old('call_type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                            </select>
                                            @error('call_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-5 call-date-container">
                                        <label for="call_date" class="form-label">Call Date/Time <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control @error('call_date') is-invalid @enderror" id="createCallLogModal_call_date" name="call_date" value="{{ old('call_date', now()->format('Y-m-d\TH:i')) }}" required>
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            @error('call_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Caller Info --}}
                        <div class="mb-4">
                            <div class="section-title">
                                <div class="icon"><i class="fas fa-user-circle"></i></div>
                                <div>Caller Information</div>
                            </div>
                            <div class="subcard">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="caller_name" class="form-label">Caller Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            <input type="text" class="form-control @error('caller_name') is-invalid @enderror" id="createCallLogModal_caller_name" name="caller_name" value="{{ old('caller_name') }}" maxlength="255" placeholder="Enter caller's name">
                                            @error('caller_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="caller_phone" class="form-label">Caller Phone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" class="form-control @error('caller_phone') is-invalid @enderror" id="createCallLogModal_caller_phone" name="caller_phone" value="{{ old('caller_phone') }}" maxlength="20" placeholder="Phone number">
                                            @error('caller_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                            <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" id="createCallLogModal_duration_minutes" name="duration_minutes" value="{{ old('duration_minutes') }}" min="0" placeholder="Call duration">
                                            @error('duration_minutes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Call Details --}}
                        <div class="mb-4">
                            <div class="section-title">
                                <div class="icon"><i class="fas fa-info-circle"></i></div>
                                <div>Call Details</div>
                            </div>
                            <div class="subcard">
                                <div class="row g-3">
                                    <div class="col-md-6 subject-container">
                                        <label for="subject" class="form-label">Subject <span class="text-danger" title="title for both call log and task">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="createCallLogModal_subject" name="subject" value="{{ old('subject') }}" required maxlength="255" placeholder="Brief subject of the call">
                                            @error('subject')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3 priority-container">
                                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-exclamation-circle"></i></span>
                                            <select class="form-select @error('priority') is-invalid @enderror" id="createCallLogModal_priority" name="priority" required>
                                                @foreach (\App\Models\CallLog::getPriorityOptions() as $value => $label)
                                                    <option value="{{ $value }}" {{ old('priority', 'medium') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3 status-container">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                            <select class="form-select @error('status') is-invalid @enderror" id="createCallLogModal_status" name="status" required>
                                                @foreach (\App\Models\CallLog::getStatusOptions() as $value => $label)
                                                    <option value="{{ $value }}" {{ old('status', 1) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Description & Notes --}}
                        <div class="mb-4">
                            <div class="section-title">
                                <div class="icon"><i class="fas fa-file-alt"></i></div>
                                <div>Description & Notes</div>
                            </div>
                            <div class="subcard">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="createCallLogModal_description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Additional Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="createCallLogModal_notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Follow-up --}}
                        <div class="mb-4">
                            <div class="section-title">
                                <div class="icon"><i class="fas fa-calendar-check"></i></div>
                                <div>Task Creation</div>
                            </div>
                            <div class="subcard">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label for="follow_up_required" class="form-label">Task Description</label>
                                        <textarea class="form-control @error('follow_up_required') is-invalid @enderror" id="createCallLogModal_follow_up_required" name="follow_up_required" rows="2">{{ old('follow_up_required') }}</textarea>
                                        @error('follow_up_required')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="follow_up_date" class="form-label">Next Call Date</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control @error('follow_up_date') is-invalid @enderror" id="createCallLogModal_follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            @error('follow_up_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Task checkbox and Assigned To field --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div style="flex: 0 0 60%;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="createCallLogModal_create_task" name="create_task" value="1" {{ old('create_task', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="createCallLogModal_create_task"><strong>Automatically create a task for this call</strong></label>
                                        <div class="form-text">This will create a task for follow-up. Not created for "Resolved" calls.</div>
                                    </div>
                                </div>
                                <div style="flex: 0 0 40%;" id="createCallLogModal_assigned_to_container" style="display: {{ old('create_task', true) ? 'block' : 'none' }};">
                                    <label for="createCallLogModal_assigned_to" class="form-label">Assign Task To</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="createCallLogModal_assigned_to" name="assigned_to">
                                            <option value="">Select employee...</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ old('assigned_to', old('employee_id')) == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Defaults to call log's assigned employee.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Record Call</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Tom Select for modal form fields
                new TomSelect('#createCallLogModal #client_id', {
                    placeholder: 'Search and select client...',
                    allowEmptyOption: true,
                    create: false,
                    render: {
                        option: function(data, escape) {
                            return `<div>${escape(data.text)}</div>`;
                        },
                        item: function(data, escape) {
                            return `<div>${escape(data.text)}</div>`;
                        }
                    }
                });

                const employeeSelect = new TomSelect('#createCallLogModal #employee_id', {
                    placeholder: 'Select employee...',
                    allowEmptyOption: true,
                    create: false
                });

                new TomSelect('#createCallLogModal #call_type', {
                    placeholder: 'Select type...',
                    allowEmptyOption: true,
                    create: false
                });

                new TomSelect('#createCallLogModal #priority', {
                    placeholder: 'Select priority...',
                    allowEmptyOption: true,
                    create: false
                });

                new TomSelect('#createCallLogModal #status', {
                    placeholder: 'Select status...',
                    allowEmptyOption: true,
                    create: false
                });

                const assignedToSelect = new TomSelect('#createCallLogModal #assigned_to', {
                    placeholder: 'Select employee...',
                    allowEmptyOption: true,
                    create: false
                });

                let assignedToManuallyChanged = false;
                assignedToSelect.on('change', function() {
                    assignedToManuallyChanged = true;
                });

                const employeeIdSelect = document.getElementById('createCallLogModal_employee_id');
                employeeIdSelect.addEventListener('change', function() {
                    if (createTaskCheckbox.checked && !assignedToManuallyChanged) {
                        assignedToSelect.setValue(this.value || '');
                    }
                });

                const createTaskCheckbox = document.getElementById('createCallLogModal_create_task');
                const assignedToContainer = document.getElementById('createCallLogModal_assigned_to_container');
                createTaskCheckbox.addEventListener('change', function() {
                    assignedToContainer.style.display = this.checked ? 'block' : 'none';
                    if (this.checked && !assignedToManuallyChanged) {
                        assignedToSelect.setValue(employeeIdSelect.value || '');
                    }
                });

                if (createTaskCheckbox.checked) {
                    assignedToContainer.style.display = 'block';
                    if (!assignedToManuallyChanged) {
                        assignedToSelect.setValue(employeeIdSelect.value || '');
                    }
                }

                const statusSelect = document.getElementById('createCallLogModal_status');
                const resolvedStatus = '{{ \App\Models\CallLog::STATUS_RESOLVED }}';

                function checkTaskStatus() {
                    if (statusSelect.value == resolvedStatus) {
                        createTaskCheckbox.checked = false;
                        createTaskCheckbox.disabled = true;
                        assignedToContainer.style.display = 'none';
                    } else {
                        createTaskCheckbox.disabled = false;
                        assignedToContainer.style.display = createTaskCheckbox.checked ? 'block' : 'none';
                    }
                }

                checkTaskStatus();
                statusSelect.addEventListener('change', checkTaskStatus);

                // Refresh page after modal submission
                const form = document.querySelector('#createCallLogModal form');
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Error saving call log.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while saving the call log.');
                    });
                });
            });
        </script>
    @endpush