<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'MERS'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #06b6d4;
            --dark-bg: #0f172a;
            --light-bg: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --success-color: #10b981;
            --danger-color: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light-bg);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Modern Navbar */
        .navbar-modern {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            /* Ensure dropdowns appear above page content */
            position: relative;
            z-index: 1080;
            /* higher than alerts (1050) and default dropdown (1000) */
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            letter-spacing: -0.5px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar-brand i {
            margin-right: 0.5rem;
            font-size: 1.75rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            margin: 0 0.25rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .sidebar-toggle-btn {
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.65rem;
        }

        .sidebar-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 0.5rem;
            margin-top: 0.5rem;
            animation: slideDown 0.3s ease;
            /* Make sure menu is above any positioned main content */
            z-index: 1085;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .dropdown-item:hover {
            background: var(--light-bg);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .dropdown-item i {
            margin-right: 0.5rem;
            width: 20px;
        }

        /* Main Content */
        main {
            min-height: calc(100vh - 80px);
            padding: 2rem 0;
        }

        /* Fullpage Snap Mode */
        main.snap-root {
            height: 100vh;
            overflow-y: auto;
            scroll-snap-type: y mandatory;
            scroll-padding-top: 72px;
            padding: 0;
            margin: 0;
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            animation: slideInDown 0.4s ease;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid var(--danger-color);
        }

        .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }

        /* User Avatar */
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            margin-right: 0.5rem;
        }

        .admin-shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 1.25rem;
            align-items: start;
        }

        .admin-sidebar {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            position: sticky;
            top: 1.25rem;
            overflow: hidden;
        }

        .admin-sidebar-header {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: #fff;
            padding: 1rem 1.1rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .admin-sidebar-links {
            padding: 0.75rem;
        }

        .admin-sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
            color: #334155;
            padding: 0.7rem 0.8rem;
            border-radius: 10px;
            font-weight: 600;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
        }

        .admin-sidebar-link i {
            width: 18px;
            text-align: center;
            color: #64748b;
        }

        .admin-sidebar-link:hover {
            background: #eef2ff;
            color: #3730a3;
        }

        .admin-sidebar-link.active {
            background: #e0e7ff;
            color: #312e81;
        }

        .admin-sidebar-link.active i {
            color: #312e81;
        }

        .admin-content {
            min-width: 0;
        }

        .sidebar-backdrop {
            display: none;
        }

        #app.sidebar-collapsed .admin-shell {
            grid-template-columns: 86px minmax(0, 1fr);
        }

        #app.sidebar-collapsed .admin-sidebar-header,
        #app.sidebar-collapsed .admin-sidebar-link span {
            display: none;
        }

        #app.sidebar-collapsed .admin-sidebar-links {
            padding: 0.6rem;
        }

        #app.sidebar-collapsed .admin-sidebar-link {
            justify-content: center;
            padding: 0.72rem 0.5rem;
        }

        #app.sidebar-collapsed .admin-sidebar-link i {
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.25rem;
            }

            .nav-link {
                margin: 0.25rem 0;
            }

            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: fixed;
                top: 88px;
                left: 0;
                bottom: 0;
                width: 280px;
                border-radius: 0 16px 16px 0;
                z-index: 1090;
                transform: translateX(-105%);
                transition: transform 0.25s ease;
            }

            #app.sidebar-open .admin-sidebar {
                transform: translateX(0);
            }

            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.35);
                z-index: 1085;
            }

            #app.sidebar-open .sidebar-backdrop {
                display: block;
            }

            #app.sidebar-collapsed .admin-shell {
                grid-template-columns: 1fr;
            }

            #app.sidebar-collapsed .admin-sidebar-header,
            #app.sidebar-collapsed .admin-sidebar-link span {
                display: initial;
            }

            #app.sidebar-collapsed .admin-sidebar-link {
                justify-content: flex-start;
                padding: 0.7rem 0.8rem;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

    </style>

    @stack('styles')
</head>

<body>
    <div id="app">
        <!-- Modern Navbar -->
        <nav class="navbar navbar-modern navbar-expand-lg navbar-dark">
            <div class="container">
                <button type="button" class="btn sidebar-toggle-btn" id="sidebarToggle"
                    aria-label="Toggle sidebar" aria-expanded="true">
                    <i class="bi bi-list"></i>
                </button>

                <a class="navbar-brand" href="{{ route('super-admin.dashboard') }}">
                    {{ config('app.name', 'MERS') }}
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @yield('nav-left')
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        @php
                            $currentUser = Auth::user();
                            $isSuperAdmin = $currentUser?->isSuperAdmin() ?? false;
                        @endphp

                        @if (!$currentUser)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('super-admin.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Start
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center"
                                    href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    @php
                                        $displayName =
                                            $currentUser->name ??
                                            (isset($currentUser->email)
                                                ? strstr($currentUser->email, '@', true)
                                                : 'User');
                                        $initial = strtoupper(substr($displayName, 0, 1));
                                    @endphp
                                    <span class="user-avatar">{{ $initial }}</span>
                                    <span class="d-none d-md-inline">{{ $displayName }}</span>
                                    @if ($isSuperAdmin)
                                        <span class="badge bg-warning text-dark ms-2">Super Admin</span>
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <div class="px-3 py-2 border-bottom">
                                        <small class="text-muted">Signed in as
                                            {{ $isSuperAdmin ? 'super administrator' : 'User' }}</small>
                                        <div class="fw-bold">{{ $currentUser->email ?? 'N/A' }}</div>
                                    </div>

                                    @if ($isSuperAdmin)
                                        <a class="dropdown-item" href="{{ route('super-admin.dashboard') }}">
                                            <i class="bi bi-speedometer2"></i>
                                            Dashboard
                                        </a>
                                    @else
                                        {{ redirect()->route(route: 'logout')->with('error', 'You do not have access to this page.') }}
                                    @endif

                                    <a class="dropdown-item" href="{{ route('super-admin.profile.show') }}">
                                        <i class="bi bi-person-circle"></i>
                                        Profile
                                    </a>

                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-gear"></i>
                                        Settings
                                    </a>

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item text-danger"
                                        href="{{ $isSuperAdmin ? route('super-admin.logout') : route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i>
                                        Logout
                                    </a>

                                    <form id="logout-form"
                                        action="{{ $isSuperAdmin ? route('super-admin.logout') : route('logout') }}"
                                        method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main @if (View::hasSection('fullpage')) class="snap-root" @endif>
            @if (View::hasSection('fullpage'))
                <!-- Flash messages still shown at top (overlay style) -->
                <div class="position-fixed top-0 start-50 translate-middle-x mt-3"
                    style="z-index:1050; width: min(90%, 800px);">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show mb-2" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Info!</strong> {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show mb-2" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <strong>Warning!</strong> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                </div>
                @yield('content')
            @else
                <div class="container-fluid px-3 px-lg-4">
                    <div class="admin-shell">
                        <aside class="admin-sidebar">
                            <div class="admin-sidebar-header">
                                Super Admin Menu
                            </div>
                            <nav class="admin-sidebar-links" aria-label="Super Admin Sidebar">
                                <a class="admin-sidebar-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}"
                                    href="{{ route('super-admin.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>
                                    <span>Dashboard</span>
                                </a>
                                <a class="admin-sidebar-link {{ request()->routeIs('super-admin.locations.*') ? 'active' : '' }}"
                                    href="{{ route('super-admin.locations.index') }}">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>Manage Location</span>
                                </a>
                                <a class="admin-sidebar-link {{ request()->routeIs('super-admin.tourist_spots.*') ? 'active' : '' }}"
                                    href="{{ route('super-admin.tourist_spots.index') }}">
                                    <i class="bi bi-camera-fill"></i>
                                    <span>Manage Tourist Spot</span>
                                </a>
                                <a class="admin-sidebar-link {{ request()->routeIs('super-admin.criteria_types.*') ? 'active' : '' }}"
                                    href="{{ route('super-admin.criteria_types.index') }}">
                                    <i class="bi bi-ui-checks-grid"></i>
                                    <span>Manage Criteria Type</span>
                                </a>
                                <a class="admin-sidebar-link {{ request()->routeIs('super-admin.criteria.*') ? 'active' : '' }}"
                                    href="{{ route('super-admin.criteria.index') }}">
                                    <i class="bi bi-sliders"></i>
                                    <span>Manage Criteria</span>
                                </a>
                                <a class="admin-sidebar-link {{ request()->routeIs('super-admin.submissions.*') ? 'active' : '' }}"
                                    href="{{ route('super-admin.submissions.index') }}">
                                    <i class="bi bi-inbox-fill"></i>
                                    <span>View Submission</span>
                                </a>
                                <a class="admin-sidebar-link {{ request()->routeIs('super-admin.sus_submissions.*') ? 'active' : '' }}"
                                    href="{{ route('super-admin.sus_submissions.index') }}">
                                    <i class="bi bi-clipboard2-pulse"></i>
                                    <span>View SUS Submission</span>
                                </a>
                            </nav>
                        </aside>

                        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

                        <div class="admin-content">
                            <!-- Flash Messages -->
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <strong>Success!</strong> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Error!</strong> {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('info'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <strong>Info!</strong> {{ session('info') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                    <strong>Warning!</strong> {{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @yield('content')
                        </div>
                    </div>
                </div>
            @endif
        </main>
@include('layouts.partials.footer')
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const appRoot = document.getElementById('app');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Prevent rapid duplicate import submissions from modal forms.
            const importRoute = '{{ route('import-data') }}';
            const forms = document.querySelectorAll('form[action]');

            forms.forEach(function(form) {
                const action = form.getAttribute('action') || '';
                const isImportForm = action === importRoute || action.endsWith('/import-data');

                if (!isImportForm) {
                    return;
                }

                form.addEventListener('submit', function() {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (!submitButton) {
                        return;
                    }

                    submitButton.disabled = true;
                    submitButton.classList.add('disabled');
                    submitButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Importing...';
                });
            });

            if (appRoot && sidebarToggle) {
                const desktopMedia = window.matchMedia('(min-width: 769px)');
                const savedSidebarState = localStorage.getItem('superAdminSidebarCollapsed');

                if (savedSidebarState === 'true' && desktopMedia.matches) {
                    appRoot.classList.add('sidebar-collapsed');
                    sidebarToggle.setAttribute('aria-expanded', 'false');
                }

                sidebarToggle.addEventListener('click', function() {
                    if (desktopMedia.matches) {
                        const collapsed = appRoot.classList.toggle('sidebar-collapsed');
                        localStorage.setItem('superAdminSidebarCollapsed', collapsed ? 'true' : 'false');
                        sidebarToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    } else {
                        const opened = appRoot.classList.toggle('sidebar-open');
                        sidebarToggle.setAttribute('aria-expanded', opened ? 'true' : 'false');
                    }
                });

                if (sidebarBackdrop) {
                    sidebarBackdrop.addEventListener('click', function() {
                        appRoot.classList.remove('sidebar-open');
                        sidebarToggle.setAttribute('aria-expanded', 'false');
                    });
                }
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
