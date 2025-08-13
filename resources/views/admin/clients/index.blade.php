@extends('layouts.app')

@section('title', 'Clients')
@section('breadcrumb')
    <span class="breadcrumb-item active">Clients</span>
@endsection
@section('actions')
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Client
    </a>
@endsection

@push('styles')
    <style>
        /* Professional DataTables Theme */
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #3730a3;
            --secondary-color: #64748b;
            --success-color: #059669;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --info-color: #0284c7;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-muted: #6b7280;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 0.5rem;
            --transition: all 0.15s ease-in-out;
        }

        /* Card Styling */
        .enhanced-card {
            background: #ffffff;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            overflow: hidden;

            padding: 2rem;
            /* more inner spacing */
            margin: 1.5rem auto;
            /* more breathing room outside */
            width: 100%;
            max-width: 1400px;
            /* stretch to be fuller on large screens */
        }

        /* DataTables Wrapper */
        .dataTables_wrapper {
            padding: 0;
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* Table Styling */
        #clients-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100% !important;
            background: white;
            table-layout: auto !important;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        #clients-table thead th {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            font-weight: 600;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            padding: 1rem 0.75rem;
            text-align: center;
            border: none;
            vertical-align: middle;
            white-space: nowrap;
            position: relative;
        }

        #clients-table thead th:first-child {
            border-top-left-radius: var(--border-radius);
        }

        #clients-table thead th:last-child {
            border-top-right-radius: var(--border-radius);
        }

        #clients-table tbody td {
            padding: 0.875rem 0.75rem;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: #374151;
            background-color: white;
            transition: var(--transition);
        }

        #clients-table tbody tr {
            transition: var(--transition);
        }

        #clients-table tbody tr:hover {
            background-color: var(--light-bg);
        }

        #clients-table tbody tr:hover td {
            background-color: var(--light-bg);
        }

        #clients-table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        #clients-table tbody tr:nth-child(even) td {
            background-color: #fafbfc;
        }

        /* Badge Styling */
        .animated-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
            transition: var(--transition);
        }

        .bg-success {
            background-color: var(--success-color) !important;
        }

        .bg-secondary {
            background-color: var(--secondary-color) !important;
        }

        .bg-warning {
            background-color: var(--warning-color) !important;
        }

        .bg-light {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
        }

        .animated-badge:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-md);
        }

        /* Button Styling */
        .icon-wrapper {
            transition: var(--transition);
            border-radius: 0.375rem;
        }

        .icon-wrapper:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* DataTables Controls */
        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 1.5rem;
        }

        .dataTables_length label,
        .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0;
        }

        .dataTables_length select {
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            background-color: white;
            transition: var(--transition);
        }

        .dataTables_length select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .dataTables_filter input {
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            background-color: white;
            transition: var(--transition);
            min-width: 250px;
        }

        .dataTables_filter input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .dataTables_info {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        /* Pagination */
        .dataTables_paginate {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
        }

        .dataTables_paginate .paginate_button {
            border: 1px solid var(--border-color) !important;
            border-radius: 0.375rem !important;
            margin: 0 0.125rem;
            padding: 0.5rem 0.75rem !important;
            color: #374151 !important;
            background: white !important;
            transition: var(--transition) !important;
            font-weight: 500;
        }

        .dataTables_paginate .paginate_button:hover {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
            transform: translateY(-1px);
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
            font-weight: 600;
        }

        .dataTables_paginate .paginate_button.disabled {
            color: var(--text-muted) !important;
            background: #f9fafb !important;
            border-color: var(--border-color) !important;
        }

        /* Export Buttons */
        .dt-buttons {
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .dt-button {
            border: none !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            transition: var(--transition) !important;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dt-button:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .buttons-excel {
            background: var(--success-color) !important;
            color: white !important;
        }

        .buttons-pdf {
            background: var(--danger-color) !important;
            color: white !important;
        }

        .buttons-print {
            background: var(--info-color) !important;
            color: white !important;
        }

        .buttons-copy {
            background: var(--secondary-color) !important;
            color: white !important;
        }

        .dt-button.refresh-btn {
            background: var(--warning-color) !important;
            color: white !important;
        }

        /* Loading States */
        .dataTables_processing {
            background: rgba(255, 255, 255, 0.95) !important;
            border-radius: var(--border-radius) !important;
            box-shadow: var(--shadow-lg) !important;
            padding: 2rem !important;
            font-weight: 500;
            color: var(--primary-color);
        }

        /* Responsive Design */
        #clients-table {
            width: 100% !important;
            table-layout: fixed;
        }

        #clients-table thead th,
        #clients-table tbody td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Column widths for better responsive behavior */
        #clients-table thead th:nth-child(1) {
            width: 5%;
        }

        /* SN */
        #clients-table thead th:nth-child(2) {
            width: 15%;
        }

        /* Client Name */
        #clients-table thead th:nth-child(3) {
            width: 20%;
        }

        /* Company Info */
        #clients-table thead th:nth-child(4) {
            width: 15%;
        }

        /* Employee */
        #clients-table thead th:nth-child(5) {
            width: 20%;
        }

        /* Email */
        #clients-table thead th:nth-child(6) {
            width: 15%;
        }

        /* Phone */
        #clients-table thead th:nth-child(7) {
            width: 8%;
        }

        /* Status */
        #clients-table thead th:nth-child(8) {
            width: 10%;
        }

        /* Actions */

        /* Custom DataTables Layout */
        .dataTables_top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .dataTables_buttons_left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dataTables_search_right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .dataTables_filter {
            margin-bottom: 0 !important;
        }

        .dataTables_length {
            margin-bottom: 0 !important;
        }

        @media (max-width: 1200px) {
            #clients-table thead th:nth-child(3) {
                width: 18%;
            }

            #clients-table thead th:nth-child(5) {
                width: 18%;
            }

            #clients-table thead th:nth-child(6) {
                width: 10%;
            }
        }

        @media (max-width: 992px) {
            .dataTables_top {
                flex-direction: column;
                align-items: stretch;
            }

            .dataTables_buttons_left,
            .dataTables_search_right {
                justify-content: center;
            }

            #clients-table thead th,
            #clients-table tbody td {
                font-size: 0.75rem;
                padding: 0.5rem 0.25rem;
            }

            .dt-button {
                font-size: 0.75rem !important;
                padding: 0.375rem 0.75rem !important;
            }
        }

        @media (max-width: 768px) {
            .dataTables_filter input {
                min-width: 180px;
                width: 100%;
            }

            .dt-buttons {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.25rem;
            }

            .dt-button {
                font-size: 0.7rem !important;
                padding: 0.25rem 0.5rem !important;
                margin: 0.125rem;
            }

            #clients-table {
                font-size: 0.7rem;
            }

            #clients-table thead th,
            #clients-table tbody td {
                padding: 0.375rem 0.125rem;
                white-space: nowrap;
            }

            /* Hide less important columns on mobile */
            #clients-table thead th:nth-child(4),
            #clients-table tbody td:nth-child(4),
            #clients-table thead th:nth-child(6),
            #clients-table tbody td:nth-child(6) {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .dataTables_top {
                gap: 0.5rem;
            }

            .dataTables_filter input {
                min-width: 150px;
                font-size: 0.75rem;
            }

            .dt-button {
                font-size: 0.65rem !important;
                padding: 0.25rem 0.375rem !important;
            }

            /* Show only essential columns on very small screens */
            #clients-table thead th:nth-child(3),
            #clients-table tbody td:nth-child(3),
            #clients-table thead th:nth-child(5),
            #clients-table tbody td:nth-child(5) {
                display: none;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-container {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Empty State */
        .empty-state {
            padding: 3rem;
            text-align: center;
        }

        .empty-state i {
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .empty-state h4 {
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            border-top-left-radius: var(--border-radius);
            border-top-right-radius: var(--border-radius);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        /* Performance Optimizations */
        * {
            box-sizing: border-box;
        }

        .dataTables_wrapper * {
            will-change: auto;
        }

        /* Improved hover states */
        #clients-table tbody tr:hover {
            background-color: var(--light-bg);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Override Bootstrap container-fluid padding */
        .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin: 0 !important;
        }

        /* Remove main content padding */
        main.main-content {
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Fix the content wrapper */
        .px-4 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Enhanced card - remove margins and make it full width */
        .enhanced-card {
            background: #ffffff;
            border-radius: 0;
            /* Remove border radius for edge-to-edge */
            box-shadow: none;
            /* Remove shadow for cleaner edge-to-edge look */
            border: none;
            /* Remove border */
            overflow: hidden;
            padding: 2rem;
            margin: 0 !important;
            /* Remove all margins */
            width: 100%;
            max-width: none;
            /* Remove max-width constraint */
        }

        /* Alternative: If you want to keep some styling but still go edge-to-edge */
        .enhanced-card.keep-styling {
            background: #ffffff;
            border-radius: 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border-left: none;
            border-right: none;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            overflow: hidden;
            padding: 2rem;
            margin: 0 !important;
            width: 100%;
            max-width: none;
        }

        /* Ensure the row takes full width */
        .container-fluid>.row {
            margin: 0 !important;
            width: 100%;
        }

        /* Fix the column classes */
        .col-12 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Breadcrumb and actions section - add some padding back */
        .d-flex.align-items-center.justify-content-between {
            padding: 1rem 2rem !important;
            /* Add padding to match card content */
            margin: 0 !important;
        }

        /* DataTable wrapper adjustments */
        .dataTables_wrapper {
            padding: 0;
            margin: 0;
        }

        /* Table container adjustments */
        .table-container {
            margin: 0;
            padding: 0;
        }

        /* Responsive table wrapper */
        .table-responsive {
            margin: 0;
            border-radius: 0;
        }

        /* DataTables controls - add some spacing */
        .dataTables_top {
            padding: 0 1rem;
        }

        .dataTables_info {
            padding-left: 1rem;
        }

        .dataTables_paginate {
            padding-right: 1rem;
        }

        /* Fix sidebar to ensure no gaps */
        .sidebar {
            padding-right: 0 !important;
            border-right: 1px solid #e5e7eb;
        }

        /* Ensure main content starts right after sidebar */
        main.main-content {
            border-left: none;
            margin-left: 0 !important;
        }

        /* For Bootstrap column that contains the main content */
        .col-md-9.ms-sm-auto.col-lg-10 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card enhanced-card">
                    <div class="card-body">
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table" id="clients-table">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-list-ol me-2"></i>SN</th>
                                            <th><i class="fas fa-user me-2"></i>Client Name</th>
                                            <th><i class="fas fa-building me-2"></i>Company Info</th>
                                            <th><i class="fas fa-user-tie me-2"></i>Employee Name</th>
                                            <th><i class="fas fa-envelope me-2"></i>Email</th>
                                            <th><i class="fas fa-phone me-2"></i>Phone</th>
                                            <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this client? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#clients-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('admin.clients.index') }}",
                    type: 'GET',
                    data: function(d) {
                        // Add any additional parameters here if needed
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'client_name',
                        name: 'name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'company_info',
                        name: 'company_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'email_info',
                        name: 'user.email',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'phone_info',
                        name: 'phone_info',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: '_INPUT_',
                    searchPlaceholder: 'Search clients...',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>'
                    },
                    emptyTable: `
                        <div class="text-center py-5 empty-state">
                            <i class="fas fa-users fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No clients found</h4>
                            <p class="text-muted mb-4">Start by adding your first client to the system.</p>
                            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Add First Client
                            </a>
                        </div>
                    `
                },
                dom: '<"dataTables_top"<"dataTables_buttons_left"B><"dataTables_search_right"lf>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel me-1"></i>Excel',
                        className: 'btn btn-success btn-sm buttons-excel',
                        title: 'Clients_List_' + new Date().toISOString().split('T')[0],
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6] // Exclude actions column
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf me-1"></i>PDF',
                        className: 'btn btn-danger btn-sm buttons-pdf',
                        title: 'Clients List',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6] // Exclude actions column
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = ['8%', '15%', '20%', '15%', '20%', '12%',
                                '10%'
                            ];
                            doc.styles.tableHeader.fontSize = 10;
                            doc.defaultStyle.fontSize = 8;
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1"></i>Print',
                        className: 'btn btn-info btn-sm buttons-print',
                        title: 'Clients List',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6] // Exclude actions column
                        }
                    },
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy me-1"></i>Copy',
                        className: 'btn btn-secondary btn-sm buttons-copy',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6] // Exclude actions column
                        }
                    },
                    {
                        text: '<i class="fas fa-sync-alt me-1"></i>Refresh',
                        className: 'btn btn-warning btn-sm refresh-btn',
                        action: function(e, dt, node, config) {
                            dt.ajax.reload();
                        }
                    }
                ],
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                },
                columnDefs: [{
                        targets: [0, 7], // SN and Actions columns
                        responsivePriority: 1
                    },
                    {
                        targets: [1, 2], // Client Name and Company Info
                        responsivePriority: 2
                    },
                    {
                        targets: [6], // Status
                        responsivePriority: 3
                    },
                    {
                        targets: [3, 4, 5], // Employee, Email, Phone
                        responsivePriority: 4
                    }
                ],
                initComplete: function() {
                    // Initialize tooltips after table is loaded
                    $('[data-bs-toggle="tooltip"]').tooltip();

                    // Add animation to rows
                    $('#clients-table tbody tr').each(function(index) {
                        $(this).css({
                            'animation-delay': (index * 0.1) + 's',
                            'animation': 'fadeInUp 0.6s ease-out forwards'
                        });
                    });
                },
                drawCallback: function() {
                    // Re-initialize tooltips after each draw
                    $('[data-bs-toggle="tooltip"]').tooltip();

                    // Add click animation to badges and buttons
                    $('.animated-badge, .btn, .icon-wrapper').off('click.animation').on(
                        'click.animation',
                        function() {
                            const element = $(this);
                            element.css('transform', 'scale(0.95)');
                            setTimeout(() => element.css('transform', 'scale(1.05)'), 100);
                            setTimeout(() => element.css('transform', 'scale(1)'), 200);
                        });
                },
                createdRow: function(row, data, dataIndex) {
                    // Add hover effects to rows
                    $(row).hover(
                        function() {
                            $(this).addClass('table-row-hover');
                        },
                        function() {
                            $(this).removeClass('table-row-hover');
                        }
                    );
                }
            });

            // Custom search functionality
            $('.dataTables_filter input').removeClass('form-control-sm');
            $('.dataTables_length select').removeClass('form-select-sm');

            // Add search icon to search input
            $('.dataTables_filter').addClass('position-relative');
            $('.dataTables_filter input').addClass('ps-5');
            $('.dataTables_filter').prepend(
                '<i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #6b7280; z-index: 10;"></i>'
            );

            // Enhance the length select
            $('.dataTables_length select').addClass('form-select');

            // Add loading overlay
            let loadingOverlay = $(`
                <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 12px; text-align: center;">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0">Processing your request...</p>
                    </div>
                </div>
            `);

            $('body').append(loadingOverlay);

            // Show loading overlay during AJAX requests
            table.on('preXhr.dt', function() {
                $('#loading-overlay').fadeIn(300);
            });

            table.on('xhr.dt', function() {
                $('#loading-overlay').fadeOut(300);
            });
        });

        // Delete client function
        function deleteClient(button) {
            const form = document.getElementById('deleteForm');
            form.action = button.getAttribute('data-delete-url');
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Handle delete form submission
        $(document).on('submit', '#deleteForm', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = new FormData(this);

            // Show loading
            $('#loading-overlay').fadeIn(300);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#loading-overlay').fadeOut(300);
                    $('#deleteModal').modal('hide');

                    // Show success message
                    showAlert('success', 'Client deleted successfully!');

                    // Refresh the table
                    $('#clients-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    $('#loading-overlay').fadeOut(300);
                    $('#deleteModal').modal('hide');

                    let message = 'An error occurred while deleting the client.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    showAlert('danger', message);
                }
            });
        });

        // Alert function
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show position-fixed"
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            $('body').append(alertHtml);

            // Auto remove after 5 seconds
            setTimeout(() => {
                $('.alert').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Handle window resize for responsive table
        $(window).on('resize', function() {
            $('#clients-table').DataTable().columns.adjust().responsive.recalc();
        });

        // Add custom CSS for better mobile experience
        $(document).ready(function() {
            if ($(window).width() < 768) {
                $('.dt-buttons').addClass('d-flex flex-wrap gap-1 mb-3');
                $('.dt-button').addClass('btn-sm').removeClass('btn-sm');
            }
        });
    </script>
@endpush
