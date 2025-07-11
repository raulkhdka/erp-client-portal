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
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-building me-2"></i>ERP System
            </a>

            @auth
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </li>

                        @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                                <i class="fas fa-users me-2"></i>Clients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                                <i class="fas fa-user-tie me-2"></i>Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                                <i class="fas fa-concierge-bell me-2"></i>Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('call-logs.*') ? 'active' : '' }}" href="{{ route('call-logs.index') }}">
                                <i class="fas fa-phone me-2"></i>Call Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                                <i class="fas fa-tasks me-2"></i>Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}" href="{{ route('documents.index') }}">
                                <i class="fas fa-folder me-2"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dynamic-forms.*') ? 'active' : '' }}" href="{{ route('dynamic-forms.index') }}">
                                <i class="fas fa-clipboard-list me-2"></i>Dynamic Forms
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isEmployee())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('call-logs.*') ? 'active' : '' }}" href="{{ route('call-logs.index') }}">
                                <i class="fas fa-phone me-2"></i>My Call Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tasks.my-tasks') ? 'active' : '' }}" href="{{ route('tasks.my-tasks') }}">
                                <i class="fas fa-tasks me-2"></i>My Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                                <i class="fas fa-list me-2"></i>All Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}" href="{{ route('documents.index') }}">
                                <i class="fas fa-folder me-2"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-briefcase me-2"></i>My Clients
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isClient())
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-file-alt me-2"></i>My Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-clipboard-check me-2"></i>Forms
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @else
            <main class="col-12">
            @endauth
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @auth
    <!-- Session Management -->
    <script>
        let sessionTimeout = {{ config('session.lifetime') * 60 * 1000 }}; // Convert to milliseconds
        let warningTime = sessionTimeout - (5 * 60 * 1000); // 5 minutes before expiration
        let sessionTimer;
        let warningTimer;

        function resetSessionTimer() {
            clearTimeout(sessionTimer);
            clearTimeout(warningTimer);

            // Set warning timer
            warningTimer = setTimeout(function() {
                if (confirm('Your session will expire in 5 minutes. Click OK to extend your session.')) {
                    // Make a request to refresh the session
                    fetch('/csrf-token')
                        .then(response => response.json())
                        .then(data => {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                            resetSessionTimer();
                        })
                        .catch(console.error);
                }
            }, warningTime);

            // Set session expiration timer
            sessionTimer = setTimeout(function() {
                alert('Your session has expired. You will be redirected to the login page.');
                window.location.href = '/login?expired=1';
            }, sessionTimeout);
        }

        // Initialize session timer
        resetSessionTimer();

        // Reset timer on user activity
        document.addEventListener('click', resetSessionTimer);
        document.addEventListener('keypress', resetSessionTimer);
        document.addEventListener('scroll', resetSessionTimer);

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
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
