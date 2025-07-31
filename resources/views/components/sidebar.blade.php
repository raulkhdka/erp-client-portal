<!-- Sidebar Component -->
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="sidebar-wrapper">
        <!-- Logo/Brand Section -->
        <div class="sidebar-header">
            <div class="brand-section">
                <div class="brand-icon">
                    <i class="fas fa-circle"></i>
                </div>
                <span class="brand-text">Flup</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="sidebar-menu">
            <!-- Marketing Section -->
            @if (Auth::user()->isAdmin() || Auth::user()->isEmployee())
                <div class="menu-section">
                    <h6 class="menu-section-title">MARKETING</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                href="{{ route('dashboard') }}">
                                <i class="fas fa-th-large"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('marketplace.*') ? 'active' : '' }}"
                                href="#">
                                <i class="fas fa-shopping-cart"></i>Marketplace
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                                href="#">
                                <i class="fas fa-box"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tracking.*') ? 'active' : '' }}"
                                href="#">
                                <i class="fas fa-shipping-fast"></i>Tracking
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}"
                                href="{{ route('clients.index') }}">
                                <i class="fas fa-users"></i>Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('discounts.*') ? 'active' : '' }}"
                                href="#">
                                <i class="fas fa-tags"></i>Discounts
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

            <!-- Admin Only Sections -->
            @if (Auth::user()->isAdmin())
                <!-- Payments Section -->
                <div class="menu-section">
                    <h6 class="menu-section-title">PAYMENTS</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ledger.*') ? 'active' : '' }}"
                                href="#">
                                <i class="fas fa-book"></i>Ledger
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('taxes.*') ? 'active' : '' }}"
                                href="#">
                                <i class="fas fa-dollar-sign"></i>Taxes
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- System Section -->
                <div class="menu-section">
                    <h6 class="menu-section-title">SYSTEM</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}"
                                href="{{ route('employees.index') }}">
                                <i class="fas fa-user-tie"></i>Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}"
                                href="{{ route('services.index') }}">
                                <i class="fas fa-concierge-bell"></i>Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('call-logs.*') ? 'active' : '' }}"
                                href="{{ route('call-logs.index') }}">
                                <i class="fas fa-phone"></i>Call Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}"
                                href="{{ route('tasks.index') }}">
                                <i class="fas fa-tasks"></i>Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
                                href="{{ route('documents.index') }}">
                                <i class="fas fa-folder"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('document-categories.*') ? 'active' : '' }}"
                                href="{{ route('document-categories.index') }}">
                                <i class="fas fa-folder-open"></i>Document Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dynamic-forms.*') ? 'active' : '' }}"
                                href="{{ route('dynamic-forms.index') }}">
                                <i class="fas fa-clipboard-list"></i>Dynamic Forms
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                                href="#">
                                <i class="fas fa-cog"></i>Settings
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

            <!-- Employee Only Sections -->
            @if (Auth::user()->isEmployee())
                <div class="menu-section">
                    <h6 class="menu-section-title">MY WORK</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('call-logs.*') ? 'active' : '' }}"
                                href="{{ route('call-logs.index') }}">
                                <i class="fas fa-phone"></i>My Call Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tasks.my-tasks') ? 'active' : '' }}"
                                href="{{ route('tasks.my-tasks') }}">
                                <i class="fas fa-tasks"></i>My Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}"
                                href="{{ route('tasks.index') }}">
                                <i class="fas fa-list"></i>All Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
                                href="{{ route('documents.index') }}">
                                <i class="fas fa-folder"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-briefcase"></i>My Clients
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

            <!-- Client Only Sections -->
            @if (Auth::user()->isClient())
                <div class="menu-section">
                    <h6 class="menu-section-title">CLIENT PORTAL</h6>
                    <ul class="nav flex-column">
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
                    </ul>
                </div>
            @endif
        </div>

        <!-- User Profile Section -->
        <div class="sidebar-footer">
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
    </div>
</nav>
