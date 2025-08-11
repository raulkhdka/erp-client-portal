<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ERP System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom Application Styles -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sidebar.css') }}" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-size: 0.975rem;
        }

        .container-fluid,
        .row {
            /* min-height: 100vh; */
            */
            /* full viewport height */
            margin: 50px 0;
            padding: 0;
        }

        .container-fluid>.row {
            display: flex;
            flex-wrap: nowrap;
            /* no wrapping */
        }

        .sidebar {
            flex: 0 0 250px;
            /* adjust to your sidebar width */
            height: 100vh;
            overflow-y: auto;
            position: sticky;
            top: 0;
        }

        main.main-content {
            flex: 1 1 auto;
            height: 100vh;
            overflow-y: auto;
            padding: 0 1rem;
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>

<body class="m-0 p-0">
    <div class="container-fluid">
        <div class="row">
            <!-- Include Sidebar Component -->
            @include('components.sidebar')

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 p-0 main-content">

                <!-- Flash Messages -->
                {{-- @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif --}}

                @if (session('status_update_success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('success', @json(session('status_update_success')));
                        });
                    </script>
                @endif


                @if (session('status_update_error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('error', @json(session('status_update_error')));
                        });
                    </script>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="d-flex align-items-center justify-content-between p-3">
                    <div class="breadcrumb">
                        <a href="{{ Auth::user()->getDashboardUrl() }}">Dashboard</a> @yield('breadcrumb')
                    </div>
                    <div class="actions">
                        @yield('actions')
                    </div>
                </div>
                <div class="px-4">
                    @yield('content')
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Axios CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- FontAwesome CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <!-- Custom Helper Scripts-->
    <script src="{{ asset('assets/js/helpers/toastHelper.js') }}" > </script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    {{-- <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <script src="{{ asset('assets/js/utils.js') }}"></script>
    <script src="{{ asset('assets/js/sidebar.js') }}"></script>

    @auth
        <!-- Session Management -->
        <script>
            let sessionTimeout = {{ config('session.lifetime') * 60 * 1000 }}; // Convert to milliseconds
            let warningTime = sessionTimeout - (5 * 60 * 1000); // 5 minutes before expiration
            let sessionTimer;
            let warningTimer;
            let isModalActive = false; // Flag to disable session warnings during modal interactions

            function resetSessionTimer() {
                if (isModalActive) return; // Skip if modal is active

                clearTimeout(sessionTimer);
                clearTimeout(warningTimer);

                // Set warning timer
                warningTimer = setTimeout(function() {
                    if (!isModalActive && confirm(
                            'Your session will expire in 5 minutes. Click OK to extend your session.')) {
                        fetch('/csrf-token')
                            .then(response => response.json())
                            .then(data => {
                                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data
                                    .csrf_token);
                                resetSessionTimer();
                            })
                            .catch(console.error);
                    }
                }, warningTime);

                // Set session expiration timer
                sessionTimer = setTimeout(function() {
                    if (!isModalActive) {
                        alert('Your session has expired. You will be redirected to the login page.');
                        window.location.href = '/login?expired=1';
                    }
                }, sessionTimeout);
            }

            // Initialize session timer
            resetSessionTimer();

            // Reset timer on user activity
            document.addEventListener('click', function(e) {
                if (!$(e.target).closest('.modal').length) {
                    resetSessionTimer();
                }
            });
            document.addEventListener('keypress', function(e) {
                if (!$(e.target).closest('.modal').length) {
                    resetSessionTimer();
                }
            });
            document.addEventListener('scroll', function(e) {
                if (!$(e.target).closest('.modal').length) {
                    resetSessionTimer();
                }
            });

            // Refresh CSRF token periodically
            setInterval(function() {
                fetch('/csrf-token')
                    .then(response => response.json())
                    .then(data => {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        if (csrfMeta) {
                            csrfMeta.setAttribute('content', data.csrf_token);
                        }
                        const csrfInputs = document.querySelectorAll('input[name="_token"]');
                        csrfInputs.forEach(input => input.value = data.csrf_token);
                    })
                    .catch(console.error);
            }, 300000); // Every 5 minutes

            // Handle modal state
            $(document).on('show.bs.modal', '.modal', function() {
                isModalActive = true;
            });
            $(document).on('hidden.bs.modal', '.modal', function() {
                isModalActive = false;
                resetSessionTimer(); // Reset timer when modal closes
            });
        </script>
    @endauth

    @stack('scripts')
</body>

</html>
