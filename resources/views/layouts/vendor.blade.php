<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('vendor-dashboard.dashboard')) - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 78px;
            --sidebar-bg-start: #fd7e14;
            --sidebar-bg-end: #e83e8c;
            --topbar-bg: #ffffff;
            --topbar-border: #f1d7c4;
            --topbar-shadow: 0 10px 28px rgba(17, 24, 39, 0.08);
            --topbar-text: #1f2937;
            --topbar-accent: #fd7e14;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background: linear-gradient(160deg, #f0f4f8 0%, #e8eef5 50%, #f5f7fa 100%);
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow: hidden;
        }

        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: box-shadow 0.25s ease, transform 0.25s ease;
        }

        .card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .stat-card .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-icon {
            font-size: 2.3rem;
            opacity: 0.9;
        }

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .table-search-input {
            max-width: 220px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .table-search-input:focus {
            border-color: var(--sidebar-bg-start);
            box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.15);
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            color: rgba(255, 255, 255, 0.9);
            position: fixed;
            top: 0;
            bottom: 0;
            inset-inline-start: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem 1rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1030;
            box-shadow: 4px 0 14px rgba(0, 0, 0, 0.08);
        }

        .sidebar-inner {
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .sidebar-brand .brand-text {
            font-size: 1.05rem;
            font-weight: 600;
            color: #fff;
        }

        .sidebar-toggle {
            border: none;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-inline-end: 0.25rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.65rem 0.9rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: visible;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 0;
        }

        .sidebar .nav-link > * {
            position: relative;
            z-index: 1;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
        }

        .sidebar .nav-link:hover::before,
        .sidebar .nav-link.active::before {
            opacity: 1;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateX(-2px);
        }

        html[dir="rtl"] .sidebar .nav-link:hover,
        html[dir="rtl"] .sidebar .nav-link.active {
            transform: translateX(2px);
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 1rem;
        }

        .main-content {
            margin-inline-start: var(--sidebar-width);
            height: 100vh;
            height: 100dvh;
            overflow-y: auto;
            overflow-x: hidden;
            transition: margin-inline-start 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f8f9fa;
        }

        .main-content::-webkit-scrollbar {
            width: 8px;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: rgba(253, 126, 20, 0.35);
            border-radius: 999px;
        }

        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .main-content {
            margin-inline-start: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .sidebar .link-text,
        body.sidebar-collapsed .sidebar .brand-text {
            opacity: 0;
            visibility: hidden;
            width: 0;
            transition: opacity 0.2s ease;
        }

        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
        }

        body.sidebar-collapsed .sidebar .nav-link i {
            margin: 0;
        }

        .page-header-wrapper {
            position: sticky;
            top: 0;
            z-index: 1100;
            background: linear-gradient(180deg, #ffffff 0%, #fff7f2 100%);
            border: 1px solid var(--topbar-border);
            border-radius: 14px;
            margin-bottom: 1.25rem;
            box-shadow: var(--topbar-shadow);
            overflow: visible;
            backdrop-filter: blur(8px);
        }

        .topbar-shell {
            border: 0;
            margin: 0;
            padding: 1rem 1.1rem;
            background:
                radial-gradient(120px 60px at 100% 0%, rgba(232, 62, 140, 0.10), transparent 70%),
                radial-gradient(120px 60px at 0% 0%, rgba(253, 126, 20, 0.10), transparent 70%);
        }

        .page-title-wrapper h1 {
            font-weight: 700;
            color: var(--topbar-text);
            line-height: 1.2;
            margin: 0;
        }

        .page-title-wrapper h1::after {
            content: '';
            display: block;
            width: 40px;
            height: 3px;
            border-radius: 999px;
            margin-top: 0.5rem;
            background: linear-gradient(90deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
            opacity: 0.9;
        }

        .topbar-control {
            border: 1px solid #efc9af;
            background: #fff;
            color: #4b5563;
            border-radius: 10px;
            min-height: 40px;
            padding: 0.45rem 0.75rem;
            box-shadow: 0 3px 10px rgba(2, 6, 23, 0.04);
        }

        .topbar-control:hover {
            border-color: #e7b08d;
            background: #fffaf6;
            color: #1f2937;
            transform: translateY(-1px);
        }

        .topbar-control:focus,
        .topbar-control:active {
            border-color: #e7b08d;
            box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.16);
        }

        #languageDropdown {
            min-width: 130px;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            color: #1f2937;
            gap: 0.45rem;
        }

        #languageDropdown span {
            color: #1f2937;
        }

        .language-menu {
            min-width: 160px;
            border-radius: 10px;
            padding: 0.35rem;
        }

        .language-menu .dropdown-item {
            border-radius: 8px;
            font-weight: 600;
        }

        .language-menu .dropdown-item.active,
        .language-menu .dropdown-item:active {
            background: #fff2e8;
            color: #b45309;
        }

        #notificationDropdown {
            width: 40px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: visible;
        }

        #notificationDropdown .badge {
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            font-size: 0.68rem;
            font-weight: 700;
            line-height: 18px;
            border: 2px solid #fff;
        }

        .btn-toolbar {
            display: flex;
            align-items: center;
        }

        .btn-toolbar > * + * {
            margin-inline-start: 0.6rem;
        }

        .btn-toolbar .dropdown + .dropdown {
            margin-inline-start: 0.9rem;
        }

        .btn-toolbar .me-2,
        .btn-toolbar .me-3 {
            margin: 0 !important;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                inset-inline-start: 0;
                max-width: 85vw;
                z-index: 1400;
            }

            html[dir="rtl"] .sidebar {
                transform: translateX(100%);
                inset-inline-start: auto;
                inset-inline-end: 0;
            }

            body.sidebar-open .sidebar,
            html[dir="rtl"] body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-inline-start: 0 !important;
                padding: 0.75rem !important;
                height: 100vh;
                height: 100dvh;
                overflow-y: auto;
            }

            body.sidebar-open {
                overflow: hidden;
                position: fixed;
                width: 100%;
            }

            body.sidebar-open .main-content::before {
                content: '';
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1300;
                backdrop-filter: blur(2px);
            }

            body.sidebar-open .page-header-wrapper {
                z-index: 1200;
            }

            .topbar-shell {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }

            .main-content h1 {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }

            .btn-toolbar {
                width: 100%;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .btn-toolbar > * + * {
                margin-inline-start: 0;
            }

            .btn-toolbar .dropdown + .dropdown {
                margin-inline-start: 0;
            }

            .topbar-control {
                width: auto;
            }

            .dropdown-menu {
                max-width: calc(100vw - 2rem);
            }

            .sidebar-toggle,
            #sidebarMobileToggle {
                min-width: 44px;
                min-height: 44px;
                touch-action: manipulation;
            }

            .sidebar .nav-link {
                padding: 0.75rem 0.9rem;
                min-height: 44px;
            }

            .sidebar-footer button {
                min-height: 44px;
            }
        }

        @media (max-width: 575.98px) {
            .main-content {
                padding: 0.5rem !important;
            }

            .main-content h1 {
                font-size: 1.25rem;
            }

            .btn-toolbar .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }

            .dropdown-menu {
                width: calc(100vw - 1rem) !important;
                max-width: calc(100vw - 1rem) !important;
            }

            #languageDropdown {
                min-width: 114px;
                font-size: 0.88rem;
            }

            .sidebar {
                width: 280px;
                max-width: 90vw;
            }

            .card {
                margin-bottom: 1rem;
            }

            table {
                font-size: 0.875rem;
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        .dropdown-menu .dropdown-item.active {
            background-color: #fff2e8;
            color: #b45309;
            font-weight: 600;
        }

        .dropdown-menu img {
            border: 1px solid #ddd;
        }

        /* Ensure tables are scrollable on mobile */
        @media (max-width: 767.98px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="sidebar" id="vendorSidebar">
        <div class="sidebar-inner">
            <div class="sidebar-brand">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-store"></i>
                    <span class="brand-text">@lang('vendor-dashboard.panel_title')</span>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle" type="button">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="sidebar-menu">
                <div class="text-white-50 small mb-3">
                    {{ auth('vendor')->user()->brand_name ?? '' }}
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}"
                           href="{{ route('vendor.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="link-text">@lang('vendor-dashboard.dashboard')</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.branches*') ? 'active' : '' }}"
                           href="{{ route('vendor.branches') }}">
                            <i class="fas fa-code-branch"></i>
                            <span class="link-text">@lang('vendor-dashboard.branches')</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.products*') ? 'active' : '' }}"
                           href="{{ route('vendor.products') }}">
                            <i class="fas fa-box"></i>
                            <span class="link-text">@lang('vendor-dashboard.products')</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.product_reviews*') ? 'active' : '' }}"
                           href="{{ route('vendor.product_reviews') }}">
                            <i class="fas fa-comments"></i>
                            <span class="link-text">@lang('vendor-dashboard.product_reviews')</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.orders*') ? 'active' : '' }}"
                           href="{{ route('vendor.orders') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="link-text">@lang('vendor-dashboard.orders')</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.return-requests*') ? 'active' : '' }}"
                           href="{{ route('vendor.return-requests') }}">
                            <i class="fas fa-exchange-alt"></i>
                            <span class="link-text">@lang('vendor-dashboard.return_requests')</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.reports') ? 'active' : '' }}"
                           href="{{ route('vendor.reports') }}">
                            <i class="fas fa-chart-bar"></i>
                            <span class="link-text">@lang('vendor-dashboard.reports')</span>
                        </a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.profile*') ? 'active' : '' }}"
                           href="{{ route('vendor.profile') }}">
                            <i class="fas fa-user"></i>
                            <span class="link-text">@lang('vendor-dashboard.profile')</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('vendor.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="link-text">@lang('vendor-dashboard.logout')</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="main-content px-3 px-md-4" id="mainContent">
        <div class="page-header-wrapper">
            <div class="topbar-shell d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div class="page-title-wrapper">
                    <h1 class="h2 mb-0">@yield('page-title', __('vendor-dashboard.page_title_dashboard'))</h1>
                </div>
                <div class="btn-toolbar mb-2 mb-md-0 align-items-center">
                <button class="btn btn-outline-secondary topbar-control me-2 d-lg-none" id="sidebarMobileToggle" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="dropdown me-2">
                    <button
                        class="btn btn-sm btn-outline-secondary topbar-control dropdown-toggle d-flex align-items-center"
                        type="button"
                        id="languageDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        @if(app()->getLocale() === 'ar')
                            <img src="{{ asset('images/flags/ar.png') }}" alt="Arabic" width="20" class="me-2 rounded-circle">
                            <span>العربية</span>
                        @else
                            <img src="{{ asset('images/flags/en.png') }}" alt="English" width="20" class="me-2 rounded-circle">
                            <span>English</span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm language-menu" aria-labelledby="languageDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === 'en' ? 'active' : '' }}"
                               href="{{ route('lang.switch', 'en') }}">
                                <img src="{{ asset('images/flags/en.png') }}" alt="English" width="20" class="me-2 rounded-circle">
                                <span>@lang('vendor-dashboard.lang_en')</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === 'ar' ? 'active' : '' }}"
                               href="{{ route('lang.switch', 'ar') }}">
                                <img src="{{ asset('images/flags/ar.png') }}" alt="Arabic" width="20" class="me-2 rounded-circle">
                                <span>@lang('vendor-dashboard.lang_ar')</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="dropdown me-3">
                    <button class="btn btn-light topbar-control position-relative" id="notificationDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        @if(auth('vendor')->user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ auth('vendor')->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow"
                        aria-labelledby="notificationDropdown"
                        style="width: 320px; max-height: 400px; overflow-y: auto;">

                        <li class="dropdown-header">@lang('vendor-dashboard.notifications')</li>

                        @forelse(auth('vendor')->user()->unreadNotifications as $notification)
                            <li>
                                <a class="dropdown-item d-flex align-items-start"
                                   href="{{ $notification->data['url'] ?? '#' }}"
                                   onclick="markNotificationAsRead('{{ $notification->id }}')">
                                    <i class="fas fa-box text-primary me-2"></i>
                                    <div>
                                        <div><strong>{{ $notification->data['title'] }}</strong></div>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li><p class="text-center text-muted py-2">@lang('vendor-dashboard.no_notifications')</p></li>
                        @endforelse

                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center text-primary" href="{{ route('vendor.notifications.index') }}">
                                @lang('vendor-dashboard.view_all')
                            </a>
                        </li>
                    </ul>
                </div>

                @yield('page-actions')
            </div>
        </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
    function markNotificationAsRead(id) {
        fetch(`/vendor/notifications/${id}/read`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });
    }

    function attachTableSearchHandlers() {
        document.querySelectorAll('.table-search-input').forEach(input => {
            const tableId = input.getAttribute('data-table-id');
            const table = document.getElementById(tableId);
            if (!table) return;

            input.addEventListener('input', function () {
                const term = this.value.toLowerCase();
                table.querySelectorAll('tbody tr').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const body = document.body;
        const toggleButton = document.getElementById('sidebarToggle');
        const mobileToggle = document.getElementById('sidebarMobileToggle');
        const mainContent = document.getElementById('mainContent');
        const sidebar = document.getElementById('vendorSidebar');
        const storageKey = 'vendorSidebarCollapsed';

        attachTableSearchHandlers();

        const isDesktop = () => window.innerWidth >= 992;

        const applyStoredState = () => {
            const shouldCollapse = localStorage.getItem(storageKey) === 'true';
            body.classList.toggle('sidebar-collapsed', shouldCollapse && isDesktop());
        };

        applyStoredState();

        const toggleDesktopState = () => {
            body.classList.toggle('sidebar-collapsed');
            localStorage.setItem(storageKey, body.classList.contains('sidebar-collapsed'));
        };

        const closeMobileSidebar = () => {
            if (!isDesktop() && body.classList.contains('sidebar-open')) {
                body.classList.remove('sidebar-open');
            }
        };

        // Close sidebar on mobile when clicking a link
        if (sidebar) {
            sidebar.addEventListener('click', function(e) {
                if (!isDesktop() && e.target.closest('a.nav-link')) {
                    setTimeout(closeMobileSidebar, 300);
                }
            });
        }

        toggleButton?.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (isDesktop()) {
                toggleDesktopState();
            } else {
                body.classList.toggle('sidebar-open');
            }
        });

        mobileToggle?.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (isDesktop()) {
                toggleDesktopState();
            } else {
                body.classList.toggle('sidebar-open');
            }
        });

        // Close sidebar when clicking on overlay
        mainContent?.addEventListener('click', function (e) {
            if (!isDesktop() && body.classList.contains('sidebar-open')) {
                if (e.target === mainContent || e.target.closest('.main-content::before')) {
                    closeMobileSidebar();
                }
            }
        });

        document.addEventListener('keyup', function (event) {
            if (event.key === 'Escape' && body.classList.contains('sidebar-open')) {
                closeMobileSidebar();
            }
        });

        // Handle resize and orientation change
        const handleResize = () => {
            if (isDesktop()) {
                body.classList.remove('sidebar-open');
                applyStoredState();
            } else {
                body.classList.remove('sidebar-collapsed');
            }
        };

        window.addEventListener('resize', handleResize);
        window.addEventListener('orientationchange', function() {
            setTimeout(handleResize, 100);
        });

        // Prevent body scroll when sidebar is open on mobile
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (body.classList.contains('sidebar-open') && !isDesktop()) {
                        document.documentElement.style.overflow = 'hidden';
                    } else {
                        document.documentElement.style.overflow = '';
                    }
                }
            });
        });
        observer.observe(body, { attributes: true });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>


