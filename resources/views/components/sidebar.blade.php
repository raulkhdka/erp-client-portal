<!-- Sidebar Component -->
<nav id="sidebar" class=" sidebar">
    <div class="sidebar-wrapper">
        <!-- Logo/Brand Section -->
        <div class="sidebar-header">
            <div class="user-profile">
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=10b981&color=fff"
                        alt="{{ Auth::user()->name }}" class="avatar-img">
                </div>
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">{{ Auth::user()->role ?? 'User' }}</span>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="sidebar-menu">
            <div class="menu-section">
                <ul class="nav flex-column">
                    <!-- Dashboard - Available to all users -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}"  title="Dashboard">
                            <i class="fas fa-home"></i>Dashboard
                        </a>
                    </li>

                    <!-- Admin Only Items -->
                    @if (Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}"
                                href="{{ route('admin.clients.index') }}"  title="Clients">
                                <i class="fas fa-users"></i>Clients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}"
                                href="{{ route('admin.employees.index') }}"  title="Employees">
                                <i class="fas fa-user-tie"></i>Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}"
                                href="{{ route('admin.services.index') }}"  title="Services">
                                <i class="fas fa-concierge-bell"></i>Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('call-logs.*') ? 'active' : '' }}"
                                href="{{ route('admin.call-logs.index') }}"  title="Call Logs">
                                <i class="fas fa-phone"></i>Call Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}"
                                href="{{ route('admin.tasks.index') }}" >
                                <i class="fas fa-tasks"></i>Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
                                href="{{ route('admin.documents.index') }}">
                                <i class="fas fa-folder"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('document-categories.*') ? 'active' : '' }}"
                                href="{{ route('admin.document-categories.index') }}">
                                <i class="fas fa-folder-open"></i>Document Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dynamic-forms.*') ? 'active' : '' }}"
                                href="{{ route('admin.dynamic-forms.index') }}">
                                <i class="fas fa-clipboard-list"></i>Dynamic Forms
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="#">
                                <i class="fas fa-cog"></i>Settings
                            </a>
                        </li>
                    @endif

                    <!-- Employee Only Items -->
                    @if (Auth::user()->isEmployee())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('call-logs.*') ? 'active' : '' }}"
                                href="{{ route('employees.call-logs.index') }}">
                                <i class="fas fa-phone"></i>My Call Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employees.tasks.*') ? 'active' : '' }}"
                                href="{{ route('employees.tasks.index') }}">
                                <i class="fas fa-tasks"></i>My Tasks
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
                                href="{{ route('employee.documents.index') }}">
                                <i class="fas fa-folder"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clients*.') ? 'active' : '' }}"
                                href="{{ route('employees.clients.index') }}">
                                <i class="fas fa-users"></i>Clients
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dynamic-forms*.') ? 'active' : '' }}"
                                href="{{ route('employees.dynamic-forms.index') }}">
                                <i class="fas fa-list"></i>Dynamic Forms
                            </a>
                        </li>
                    @endif

                    <!-- Client Only Items -->
                    @if (Auth::user()->isClient())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.services.*') ? 'active' : '' }}"
                                href="{{ route('clients.services.index') }}">
                                <i class="fas fa-handshake"></i>Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.employees.*') ? 'active' : '' }}"
                                href="{{ route('clients.employees.index') }}">
                                <i class="fas fa-users"></i>Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.documents.*') ? 'active' : '' }}"
                                href="{{ route('clients.documents.index') }}">
                                <i class="fas fa-file-alt"></i>My Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clients.forms.*') ? 'active' : '' }}"
                                href="{{ route('clients.forms.index') }}">
                                <i class="fas fa-clipboard-check"></i>Forms
                            </a>
                        </li>
                    @endif

                    <!-- Logout - Available to all users -->
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-start w-100 border-0 p-0"
                                style="color: inherit; text-decoration: none;">
                                <i class="fas fa-sign-out-alt"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
