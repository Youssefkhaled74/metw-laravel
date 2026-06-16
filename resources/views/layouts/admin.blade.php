<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin-dashboard.dashboard')) - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 78px;
            --sidebar-bg-start: #667eea;
            --sidebar-bg-end: #764ba2;
            --topbar-bg: #ffffff;
            --topbar-border: #e7ecf3;
            --topbar-shadow: 0 10px 28px rgba(17, 24, 39, 0.08);
            --topbar-text: #111827;
            --topbar-muted: #6b7280;
            --topbar-accent: #4f46e5;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background-color: #f8f9fa;
            overflow: hidden;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card .card-body {
            padding: 1.5rem;
        }

        .stat-icon {
            font-size: 2.3rem;
            opacity: 0.9;
            transition: transform 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .table-search-input {
            max-width: 220px;
            transition: all 0.3s ease;
        }

        .table-search-input:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }

        /* Alert animations */
        .alert {
            animation: slideInDown 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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

        .btn-close {
            transition: all 0.2s ease;
        }

        .btn-close:hover {
            opacity: 0.8;
            transform: scale(1.1);
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            color: rgba(255, 255, 255, 0.85);
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
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .sidebar-brand .brand-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle {
            border: none;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .sidebar-toggle::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.5s ease, height 0.5s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }

        .sidebar-toggle:hover::before {
            width: 100%;
            height: 100%;
        }

        .sidebar-toggle:active {
            transform: scale(0.95);
        }

        .sidebar-menu {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-inline-end: 0.25rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 3px;
            transition: background 0.3s ease;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.45);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.65rem 0.9rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: visible;
            flex-wrap: wrap;
            align-content: center;
            width: 100%;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 0;
        }

        .sidebar .nav-link > * {
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
            flex-shrink: 1;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
            transition: transform 0.3s ease;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            order: -1;
        }

        .sidebar .nav-link .link-text {
            white-space: normal;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            overflow: visible;
            line-height: 1.3;
            flex: 1;
            min-width: 0;
            display: block;
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

        .sidebar .nav-link .chevron {
            margin-inline-start: auto;
            font-size: 0.75rem;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            order: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar .nav-link[aria-expanded="true"] .chevron {
            transform: rotate(180deg);
        }

        #settingsSubmenu {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            margin-top: 5px;
            overflow: hidden;
        }

        #settingsSubmenu.collapse:not(.show) {
            padding: 0 0.35rem;
            opacity: 0;
        }

        #settingsSubmenu.collapse.show {
            padding: 0.5rem 0.35rem;
            opacity: 1;
        }

        #settingsSubmenu.collapsing {
            padding: 0 0.35rem;
            opacity: 0.7;
            will-change: height, opacity;
            transition: height 0.22s ease, opacity 0.18s ease;
        }

        #settingsSubmenu .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            border-radius: 8px;
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 1rem;
            transition: all 0.3s ease;
        }

        .sidebar-footer button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .sidebar-footer button:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.4) !important;
        }

        .sidebar-footer button:active {
            transform: translateY(0);
        }

        .main-content {
            margin-inline-start: var(--sidebar-width);
            height: 100vh;
            height: 100dvh;
            background-color: #f8f9fa;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            transition: margin-inline-start 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.no-sidebar {
            margin-inline-start: 0;
            height: 100vh;
            height: 100dvh;
            overflow-y: auto;
        }

        .main-content::-webkit-scrollbar {
            width: 8px;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.35);
            border-radius: 999px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.55);
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
            height: 0;
            padding: 0;
            margin: 0;
            display: none;
            transition: opacity 0.3s ease 0.1s, visibility 0.3s ease 0.1s;
        }

        body.sidebar-collapsed .sidebar .nav-link i {
            margin: 0 !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            opacity: 1;
            visibility: visible;
            width: 32px !important;
            height: 32px;
            font-size: 1.1rem;
            flex: none;
            flex-shrink: 0;
            order: 0;
        }

        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
            align-items: center;
            gap: 0;
            min-height: 48px;
            width: 48px;
            padding: 0.75rem 0;
            flex-wrap: nowrap;
            flex-direction: column;
        }

        body.sidebar-collapsed .sidebar .chevron {
            display: none !important;
        }

        body.sidebar-collapsed .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        body.sidebar-collapsed ul.nav.flex-column {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        body.sidebar-collapsed .nav-item {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        body.sidebar-collapsed #settingsSubmenu {
            max-height: 0;
            overflow: hidden;
            padding: 0;
            opacity: 0;
        }

        .page-header-wrapper {
            position: sticky;
            top: 0;
            z-index: 1100;
            background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
            border: 1px solid var(--topbar-border);
            border-radius: 14px;
            margin-bottom: 1.25rem;
            box-shadow: var(--topbar-shadow);
            overflow: visible;
            backdrop-filter: blur(8px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .topbar-shell {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            border: 0;
            margin: 0;
            padding: 1rem 1.1rem;
            background:
                radial-gradient(120px 60px at 100% 0%, rgba(79, 70, 229, 0.08), transparent 70%),
                radial-gradient(120px 60px at 0% 0%, rgba(59, 130, 246, 0.08), transparent 70%);
            overflow: visible;
        }

        .page-title-wrapper {
            flex: 1 1 auto;
            min-width: 0;
        }

        .page-title-wrapper h1 {
            font-weight: 700;
            color: var(--topbar-text);
            line-height: 1.2;
            letter-spacing: 0.2px;
            margin: 0;
            font-size: 1.4rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.3s ease;
        }

        .page-title-wrapper h1::after {
            content: '';
            display: block;
            width: 42px;
            height: 3px;
            border-radius: 999px;
            margin-top: 0.55rem;
            background: linear-gradient(90deg, var(--topbar-accent), #60a5fa);
            opacity: 0.9;
        }

        .page-actions-wrapper {
            flex-shrink: 0;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .page-actions-group {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.5rem;
            align-items: center;
            transition: all 0.3s ease;
        }

        .topbar-control {
            border: 1px solid #d6deea;
            background: #fff;
            color: #344054;
            border-radius: 999px;
            min-height: 40px;
            padding: 0.45rem 0.9rem;
            box-shadow: 0 3px 10px rgba(2, 6, 23, 0.04);
            overflow: visible;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .topbar-control:hover {
            border-color: #b8c5db;
            background: #f8fbff;
            color: #1f2937;
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(2, 6, 23, 0.12);
        }

        .topbar-control:focus,
        .topbar-control:active {
            border-color: #a7b9d7;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.14);
        }

        #languageDropdown img {
            width: 16px;
            height: 16px;
            object-fit: cover;
        }

        #languageDropdown {
            min-width: 118px;
            font-weight: 700;
            color: #1f2937;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.45rem;
            white-space: nowrap;
            padding-inline: 0.85rem 0.7rem;
            min-height: 34px;
            font-size: 0.88rem;
            opacity: 1 !important;
        }

        #languageDropdown span {
            color: #1f2937;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 66px;
            display: inline-block;
            opacity: 1 !important;
        }

        #languageDropdown.dropdown-toggle::after {
            margin-inline-start: 0.25rem;
            margin-inline-end: 0;
            flex-shrink: 0;
        }

        .language-menu {
            min-width: 150px;
            border-radius: 10px;
            padding: 0.35rem;
        }

        .language-menu .dropdown-item {
            border-radius: 8px;
            font-weight: 600;
            color: #1f2937;
            padding: 0.5rem 0.65rem;
            opacity: 1 !important;
        }

        .language-menu .dropdown-item.active,
        .language-menu .dropdown-item:active {
            background: #eaf0ff;
            color: #1e3a8a;
        }

        .language-menu .dropdown-item:hover {
            background: #f3f6ff;
            color: #1f2937;
            opacity: 1 !important;
        }

        .language-menu .dropdown-item::before {
            display: none;
        }

        .show > #languageDropdown,
        #languageDropdown:focus,
        #languageDropdown:active,
        .language-menu,
        .language-menu .dropdown-item.active,
        .language-menu .dropdown-item:active,
        .language-menu img,
        .language-menu span {
            opacity: 1 !important;
        }

        #notificationDropdown {
            width: 40px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
            position: relative;
            border-radius: 999px;
        }

        .page-actions-wrapper .btn.btn-sm.topbar-control {
            border-radius: 999px;
            font-weight: 700;
            padding-inline: 0.9rem;
        }

        #notificationDropdown .badge {
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            font-size: 0.68rem;
            font-weight: 700;
            line-height: 18px;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35);
            z-index: 2;
        }

        .page-actions-wrapper .dropdown {
            position: relative;
            z-index: 1300;
        }

        .page-actions-wrapper .dropdown-menu {
            z-index: 1400;
        }

        .page-actions-group .btn {
            white-space: nowrap;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .page-actions-group .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transition: left 0.3s ease;
            z-index: 0;
        }

        .page-actions-group .btn > * {
            position: relative;
            z-index: 1;
        }

        .page-actions-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .page-actions-group .btn:hover::before {
            left: 100%;
        }

        .page-actions-group .btn:active {
            transform: translateY(0);
        }

        .page-actions-group .btn-group {
            display: inline-flex;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
                max-width: 85vw;
                z-index: 1400;
            }

            html[dir="rtl"] .sidebar {
                transform: translateX(100%);
                inset-inline-start: auto;
                inset-inline-end: 0;
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

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

            .page-header-wrapper {
                margin-bottom: 1rem;
                border-radius: 12px;
            }

            .page-header-wrapper > div {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }

            .topbar-shell {
                flex-wrap: wrap;
                align-items: flex-start;
            }

            .topbar-shell {
                padding: 0.85rem 0.9rem;
            }

            .page-title-wrapper h1 {
                font-size: 1.5rem;
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
            }

            .page-title-wrapper,
            .page-actions-wrapper {
                width: 100%;
            }

            .page-actions-wrapper {
                justify-content: space-between;
                align-items: center;
                gap: 0.6rem;
            }

            .page-title-wrapper h1 {
                font-size: 1.5rem;
                margin-bottom: 0;
            }

            .page-actions-wrapper {
                width: 100%;
                justify-content: flex-start;
            }

            .page-actions-group {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                order: 3;
                margin-top: 0.25rem;
            }

            .page-actions-group .btn {
                width: 100%;
                justify-content: center;
            }

            .topbar-control {
                width: auto;
            }

            #languageDropdown {
                min-width: 114px;
                padding-inline: 0.45rem;
            }

            .page-actions-wrapper .dropdown-menu {
                max-height: 60vh;
                overflow-y: auto;
            }

            .page-actions-wrapper .dropdown-menu-end {
                inset-inline-end: 0;
                inset-inline-start: auto;
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

            .page-header-wrapper {
                border-radius: 10px;
            }

            .topbar-shell {
                padding: 0.7rem 0.75rem;
            }

            .page-title-wrapper h1 {
                font-size: 1.05rem;
            }

            .page-title-wrapper h1::after {
                width: 34px;
                margin-top: 0.45rem;
            }

            .page-actions-wrapper {
                gap: 0.45rem;
            }

            #languageDropdown {
                min-width: 108px;
                padding-inline: 0.4rem;
                font-size: 0.88rem;
            }

            #languageDropdown span {
                max-width: 58px;
            }

            .language-menu {
                min-width: 150px;
            }

            #notificationDropdown {
                width: 38px;
                min-width: 38px;
            }

            .page-actions-wrapper .dropdown-menu {
                width: min(92vw, 320px) !important;
                max-width: min(92vw, 320px) !important;
                max-height: 58vh;
            }

            .btn-toolbar .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }

            .dropdown-menu {
                width: calc(100vw - 1rem) !important;
                max-width: calc(100vw - 1rem) !important;
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

        html[dir="rtl"] .sidebar {
            box-shadow: -4px 0 14px rgba(0, 0, 0, 0.08);
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

        /* Enhanced button styling */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: visible;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .btn:not(:disabled):not(.disabled):active,
        .btn:not(:disabled):not(.disabled):focus {
            box-shadow: 0 0 0 0.3rem rgba(102, 126, 234, 0.25);
            transform: scale(0.98);
        }

        .btn-outline-light {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Dropdown improvements */
        .dropdown-menu {
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
            opacity: 0;
            transform: translate3d(0, -6px, 0);
            transition: opacity 0.12s ease, transform 0.12s ease;
            will-change: transform, opacity;
            backface-visibility: hidden;
            transform-origin: top center;
        }

        .dropdown-menu.show {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        .page-actions-wrapper .dropdown-menu-end {
            transform-origin: top right;
        }

        html[dir="rtl"] .page-actions-wrapper .dropdown-menu-end {
            transform-origin: top left;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .dropdown-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #667eea;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item:hover::before {
            opacity: 1;
        }

        /* Ensure popups/modals are always above topbar/sidebar layers */
        .modal {
            z-index: 2060 !important;
        }

        .modal-backdrop {
            z-index: 2050 !important;
        }

        html[dir="rtl"] .dropdown-item::before {
            left: auto;
            right: 0;
        }

        /* Card improvements */
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .card.stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card.stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.15);
        }
    </style>
</head>

<body>
    @if (auth('admin')->check() || auth('employee')->check())
        <nav class="sidebar" id="adminSidebar">
            <div class="sidebar-inner">
                <div class="sidebar-brand">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-crown"></i>
                        <span class="brand-text">{{ __('admin-dashboard.admin_panel') }}</span>
                    </div>
                    <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="{{ __('Toggle sidebar') }}">
                        <i class="fas fa-bars"></i>
                    </button>
                        </div>
                <div class="sidebar-menu">
                    <ul class="nav flex-column">
                        {{-- ✅ Admin sees everything --}}
                        @if (auth('admin')->check())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span class="link-text">{{ __('admin-dashboard.dashboard') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard.monthly-revenue') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard.monthly-revenue') }}">
                                    <i class="fas fa-chart-line"></i>
                                    <span class="link-text">{{ __('admin-dashboard.monthly_revenue') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.shipment-orders*') ? 'active' : '' }}"
                                href="{{ route('admin.shipment-orders') }}">
                                    <i class="fas fa-shipping-fast"></i>
                                    <span class="link-text">{{ __('admin-dashboard.shipment_orders') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.ecommerce-orders*') ? 'active' : '' }}"
                                href="{{ route('admin.ecommerce-orders') }}">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="link-text">{{ __('admin-dashboard.ecommerce_orders') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.vendors*') ? 'active' : '' }}"
                                href="{{ route('admin.vendors') }}">
                                    <i class="fas fa-store"></i>
                                    <span class="link-text">{{ __('admin-dashboard.vendors') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.shipment-companies*') ? 'active' : '' }}"
                                href="{{ route('admin.shipment-companies') }}">
                                    <i class="fas fa-truck"></i>
                                    <span class="link-text">{{ __('admin-dashboard.shipment_companies') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                                href="{{ route('admin.users') }}">
                                    <i class="fas fa-users"></i>
                                    <span class="link-text">{{ __('admin-dashboard.users') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.commissions*') ? 'active' : '' }}"
                                href="{{ route('admin.commissions') }}">
                                    <i class="fas fa-coins"></i>
                                    <span class="link-text">{{ __('admin-dashboard.commissions') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.custom-notifications*') ? 'active' : '' }}"
                                href="{{ route(name: 'admin.custom-notifications') }}">
                                    <i class="fas fa-bell"></i>
                                    <span class="link-text">{{ __('admin-dashboard.custom_notifications') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}"
                                href="{{ route('admin.products') }}">
                                    <i class="fas fa-box"></i>
                                    <span class="link-text">{{ __('admin-dashboard.products') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.return-requests*') ? 'active' : '' }}"
                                href="{{ route('admin.return-requests') }}">
                                    <i class="fas fa-exchange-alt"></i>
                                    <span class="link-text">{{ __('admin-dashboard.return_requests') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.employees*') ? 'active' : '' }}"
                                href="{{ route('admin.employees.index') }}">
                                    <i class="fas fa-users"></i>
                                    <span class="link-text">{{ __('admin-dashboard.employees') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}"
                                   href="{{ route('admin.roles.index') }}">
                                    <i class="fas fa-user-shield"></i>
                                    <span class="link-text">{{ __('admin-dashboard.roles') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.permissions*') ? 'active' : '' }}"
                                   href="{{ route('admin.permissions.index') }}">
                                    <i class="fas fa-key"></i>
                                    <span class="link-text">{{ __('admin-dashboard.permissions') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#settingsSubmenu" data-bs-toggle="collapse"
                                aria-expanded="{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}">
                                    <i class="fas fa-cog"></i>
                                    <span class="link-text">{{ __('admin-dashboard.settings') }}</span>
                                    <i class="fas fa-chevron-down chevron"></i>
                                </a>
                                <ul class="nav flex-column collapse {{ request()->routeIs('admin.settings.*') ? 'show' : '' }}"
                                    id="settingsSubmenu">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.banners.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.banners.index') }}">
                                            <i class="fas fa-images"></i>
                                            <span class="link-text">{{ __('admin-dashboard.banners') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.contact-admins.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.contact-admins.index') }}">
                                            <i class="fas fa-address-card"></i>
                                            <span class="link-text">{{ __('admin-dashboard.contact_admin') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.cancel-reasons.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.cancel-reasons.index') }}">
                                            <i class="fas fa-ban"></i>
                                            <span class="link-text">{{ __('admin-dashboard.cancel_reasons') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.brands.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.brands.index') }}">
                                            <i class="fas fa-tags"></i>
                                            <span class="link-text">{{ __('admin-dashboard.brands') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.main-categories.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.main-categories.index') }}">
                                            <i class="fas fa-list"></i>
                                            <span class="link-text">{{ __('admin-dashboard.main_categories') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.categories.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.categories.index') }}">
                                            <i class="fas fa-list"></i>
                                            <span class="link-text">{{ __('admin-dashboard.categories') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.promo_codes.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.promo_codes.index') }}">
                                            <i class="fas fa-tags"></i>
                                            <span class="link-text">{{ __('admin-dashboard.promo_codes') }}</span>
                                        </a>
                                    </li>
                                    {{-- <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.countries.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.countries.index') }}">
                                            <i class="fas fa-globe"></i>
                                            <span class="link-text">{{ __('admin-dashboard.countries') }}</span>
                                        </a>
                                    </li> --}}
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.states.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.states.index') }}">
                                            <i class="fas fa-globe"></i>
                                            <span class="link-text">{{ __('admin-dashboard.states') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.cities.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.cities.index') }}">
                                            <i class="fas fa-city"></i>
                                            <span class="link-text">{{ __('admin-dashboard.cities') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.zones.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.zones.index') }}">
                                            <i class="fas fa-city"></i>
                                            <span class="link-text">{{ __('admin-dashboard.zones') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.consignment-types.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.consignment-types.index') }}">
                                            <i class="fas fa-box-open"></i>
                                            <span class="link-text">{{ __('admin-dashboard.consignment_types') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.delivery-types.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.delivery-types.index') }}">
                                            <i class="fas fa-truck-loading"></i>
                                            <span class="link-text">{{ __('admin-dashboard.delivery_types') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.pages.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.pages.index') }}">
                                            <i class="fas fa-file-alt"></i>
                                            <span class="link-text">{{ __('admin-dashboard.settings_pages') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.whatsapp-templates.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.whatsapp-templates.index') }}">
                                            <i class="fab fa-whatsapp"></i>
                                            <span class="link-text">{{ __('admin-dashboard.whatsapp_templates_management') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.product-sizes.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.product-sizes.index') }}">
                                            <i class="fas fa-ruler-combined"></i>
                                            <span class="link-text">{{ __('admin-dashboard.product_sizes') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.sizes.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.sizes.index') }}">
                                            <i class="fas fa-ruler"></i>
                                            <span class="link-text">{{ __('admin-dashboard.sizes') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.colors.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.colors.index') }}">
                                            <i class="fas fa-palette"></i>
                                            <span class="link-text">{{ __('admin-dashboard.colors') }}</span>
                                        </a>
                                    </li>
                                    {{-- <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.price-per-km.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.price-per-km.index') }}">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span class="link-text">{{ __('admin-dashboard.price_per_km') }}</span>
                                        </a>
                                    </li> --}}
                                </ul>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}"
                                href="{{ route('admin.reports') }}">
                                    <i class="fas fa-chart-bar"></i>
                                    <span class="link-text">{{ __('admin-dashboard.reports') }}</span>
                                </a>
                            </li> --}}

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.settings.configs.*') ? 'active' : '' }}"
                                href="{{ route('admin.configs.index') }}">
                                    <i class="fas fa-cogs"></i>
                                    <span class="link-text">{{ __('admin-dashboard.configs') }}</span>
                                </a>
                            </li>
                    {{-- 👇 Employee only sees permitted items --}}
                    @elseif (auth('employee')->check())
                        @php
                            $employee = auth('employee')->user();
                        @endphp
                    {{-- @php
                        dd($employee->getAllPermissions()->pluck('name')->toArray());
                    @endphp --}}
                        @if($employee->can('admin.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <span class="link-text">{{ __('admin-dashboard.dashboard') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard.monthly-revenue') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard.monthly-revenue') }}">
                                        <i class="fas fa-chart-line"></i>
                                        <span class="link-text">{{ __('admin-dashboard.monthly_revenue') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.shipment-orders.show'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.shipment-orders*') ? 'active' : '' }}"
                                href="{{ route('admin.shipment-orders') }}">
                                        <i class="fas fa-shipping-fast"></i>
                                        <span class="link-text">{{ __('admin-dashboard.shipment_orders') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.ecommerce-orders'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.ecommerce-orders*') ? 'active' : '' }}"
                                href="{{ route('admin.ecommerce-orders') }}">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span class="link-text">{{ __('admin-dashboard.ecommerce_orders') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.vendors'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.vendors*') ? 'active' : '' }}"
                                href="{{ route('admin.vendors') }}">
                                        <i class="fas fa-store"></i>
                                        <span class="link-text">{{ __('admin-dashboard.vendors') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.shipment-companies'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.shipment-companies*') ? 'active' : '' }}"
                                href="{{ route('admin.shipment-companies') }}">
                                        <i class="fas fa-truck"></i>
                                        <span class="link-text">{{ __('admin-dashboard.shipment_companies') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.users'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                                href="{{ route('admin.users') }}">
                                        <i class="fas fa-users"></i>
                                        <span class="link-text">{{ __('admin-dashboard.users') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.products'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}"
                                href="{{ route('admin.products') }}">
                                        <i class="fas fa-box"></i>
                                        <span class="link-text">{{ __('admin-dashboard.products') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.return-requests'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.return-requests*') ? 'active' : '' }}"
                                href="{{ route('admin.return-requests') }}">
                                        <i class="fas fa-exchange-alt"></i>
                                        <span class="link-text">{{ __('admin-dashboard.return_requests') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.employees.index'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.employees*') ? 'active' : '' }}"
                                href="{{ route('admin.employees.index') }}">
                                        <i class="fas fa-user-tie"></i>
                                        <span class="link-text">{{ __('admin-dashboard.employees') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.roles.index'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}"
                                   href="{{ route('admin.roles.index') }}">
                                    <i class="fas fa-user-shield"></i>
                                    <span class="link-text">{{ __('admin-dashboard.roles') }}</span>
                                </a>
                            </li>
                        @endif
                        @if($employee->can('admin.permissions.index'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.permissions*') ? 'active' : '' }}"
                                   href="{{ route('admin.permissions.index') }}">
                                    <i class="fas fa-key"></i>
                                    <span class="link-text">{{ __('admin-dashboard.permissions') }}</span>
                                </a>
                            </li>
                        @endif
                        @if (
                            $employee->can('admin.settings.banners.index') ||
                            $employee->can('admin.settings.brands.index') ||
                            $employee->can('admin.settings.categories.index') ||
                            $employee->can('admin.settings.whatsapp-templates.index')
                        )
                            <li class="nav-item">
                                    <a class="nav-link" href="#settingsSubmenu" data-bs-toggle="collapse"
                                aria-expanded="{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}">
                                        <i class="fas fa-cog"></i>
                                        <span class="link-text">{{ __('admin-dashboard.settings') }}</span>
                                        <i class="fas fa-chevron-down chevron"></i>
                                </a>
                                <ul class="nav flex-column collapse {{ request()->routeIs('admin.settings.*') ? 'show' : '' }}"
                                        id="settingsSubmenu">
                                    @if($employee->can('admin.settings.warehouses.index'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.warehouses.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.warehouses.index') }}">
                                                    <i class="fas fa-warehouse"></i>
                                                    <span class="link-text">Warehouses</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if($employee->can('admin.settings.banners.index'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.banners.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.banners.index') }}">
                                                    <i class="fas fa-images"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.banners') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.contact-admins.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.contact-admins.*') ? 'active' : '' }}"
                                               href="{{ route('admin.settings.contact-admins.index') }}">
                                                <i class="fas fa-address-card"></i>
                                                <span class="link-text">{{ __('admin-dashboard.contact_admin') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.brands.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.brands.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.brands.index') }}">
                                                    <i class="fas fa-tags"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.brands') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.main-categories.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.main-categories.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.main-categories.index') }}">
                                                    <i class="fas fa-list"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.main_categories') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.categories.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.categories.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.categories.index') }}">
                                                    <i class="fas fa-list"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.categories') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.promo_codes.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.promo_codes.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.promo_codes.index') }}">
                                                    <i class="fas fa-tags"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.promo_codes') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.countries.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.countries.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.countries.index') }}">
                                                    <i class="fas fa-globe"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.countries') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.states.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.states.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.states.index') }}">
                                                    <i class="fas fa-globe"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.states') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.cities.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.cities.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.cities.index') }}">
                                                    <i class="fas fa-city"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.cities') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.zones.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.zones.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.zones.index') }}">
                                                    <i class="fas fa-city"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.zones') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.consignment-types.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.consignment-types.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.consignment-types.index') }}">
                                                    <i class="fas fa-box-open"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.consignment_types') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.delivery-types.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.delivery-types.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.delivery-types.index') }}">
                                                    <i class="fas fa-truck-loading"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.delivery_types') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.pages.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.pages.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.pages.index') }}">
                                                    <i class="fas fa-file-alt"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.settings_pages') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.whatsapp-templates.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.whatsapp-templates.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.whatsapp-templates.index') }}">
                                                    <i class="fab fa-whatsapp"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.whatsapp_templates_management') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.product-sizes.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.product-sizes.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.product-sizes.index') }}">
                                                    <i class="fas fa-ruler-combined"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.product_sizes') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.sizes.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.sizes.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.sizes.index') }}">
                                                    <i class="fas fa-ruler"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.sizes') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.colors.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.colors.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.colors.index') }}">
                                                    <i class="fas fa-palette"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.colors') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    {{-- @if($employee->can('admin.settings.price-per-km.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.price-per-km.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.price-per-km.index') }}">
                                                    <i class="fas fa-dollar-sign"></i>
                                                    <span class="link-text">{{ __('admin-dashboard.price_per_km') }}</span>
                                            </a>
                                        </li>
                                    @endif --}}
                                </ul>
                            </li>
                        @endif
                    @endif
                    </ul>
                </div>
                <div class="sidebar-footer">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                        <button type="submit" class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="link-text">{{ __('admin-dashboard.logout') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </nav>

        <main class="main-content px-3 px-md-4" id="mainContent">
                    <div class="page-header-wrapper">
                        <div class="topbar-shell d-flex justify-content-between flex-wrap align-items-start gap-3 pt-4 pb-3 mb-4 border-bottom">
                            <div class="page-title-wrapper">
                                <h1 class="h2 mb-0">@yield('page-title', 'Dashboard')</h1>
                            </div>
                            <div class="page-actions-wrapper d-flex flex-wrap align-items-center gap-2">
                                <!-- Mobile Sidebar Toggle -->
                                <button class="btn btn-outline-secondary topbar-control d-lg-none" id="sidebarMobileToggle" type="button" aria-label="{{ __('Toggle sidebar') }}">
                                    <i class="fas fa-bars"></i>
                                </button>

                                <!-- Language Dropdown -->
                                <div class="dropdown">
                                    <button
                                        class="btn btn-sm btn-outline-secondary topbar-control dropdown-toggle d-flex align-items-center"
                                        type="button"
                                        id="languageDropdown"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
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
                                                <span>@lang('shipment-dashboard.lang_en')</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === 'ar' ? 'active' : '' }}"
                                            href="{{ route('lang.switch', 'ar') }}">
                                                <img src="{{ asset('images/flags/ar.png') }}" alt="Arabic" width="20" class="me-2 rounded-circle">
                                                <span>@lang('shipment-dashboard.lang_ar')</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Notifications -->
                                @if (auth('admin')->check())
                                <div class="dropdown">
                                        <button class="btn btn-light topbar-control position-relative" id="notificationDropdown"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-bell"></i>
                                        @if(auth('admin')->user()->unreadNotifications->count() > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ auth('admin')->user()->unreadNotifications->count() }}
                                            </span>
                                        @endif
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow"
                                        aria-labelledby="notificationDropdown"
                                        style="width: 320px; max-height: 400px; overflow-y: auto;">

                                        <li class="dropdown-header">{{__('admin-dashboard.notifications')}}</li>

                                        @forelse(auth('admin')->user()->unreadNotifications as $notification)
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
                                            <li><p class="text-center text-muted py-2">{{__('admin-dashboard.no_notifications')}}</p></li>
                                        @endforelse

                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-center text-primary" href="{{ route('admin.notifications.index') }}">
                                                {{__('admin-dashboard.view_all')}}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                @endif

                                <!-- Page Actions -->
                                <div class="page-actions-group">
                                    @yield('page-actions')
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Flash Messages -->
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
    @else
        <main class="main-content no-sidebar py-5">
            @yield('content')
        </main>
    @endif
<script>
function markNotificationAsRead(id) {
    fetch(`/admin/notifications/${id}/read`, {
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

function attachProductsTableSortHandlers() {
    document.querySelectorAll('.products-table').forEach(table => {
        if (table.dataset.sortBound === '1') return;

        const tbody = table.querySelector('tbody');
        const sortableHeaders = table.querySelectorAll('.sortable-col[data-sort-key]');
        if (!tbody || !sortableHeaders.length) return;

        const numericKeys = ['id', 'price', 'stock', 'created'];

        const sortTableRows = (key, direction) => {
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                const aVal = a.dataset[key] ?? '';
                const bVal = b.dataset[key] ?? '';

                if (numericKeys.includes(key)) {
                    const aNum = Number(aVal);
                    const bNum = Number(bVal);
                    return direction === 'asc' ? aNum - bNum : bNum - aNum;
                }

                return direction === 'asc'
                    ? String(aVal).localeCompare(String(bVal))
                    : String(bVal).localeCompare(String(aVal));
            });

            rows.forEach(row => tbody.appendChild(row));
        };

        sortableHeaders.forEach(header => {
            header.addEventListener('click', function () {
                const key = this.dataset.sortKey;
                const nextDir = this.dataset.sortDir === 'asc' ? 'desc' : 'asc';

                sortableHeaders.forEach(h => {
                    h.classList.remove('active');
                    h.dataset.sortDir = '';

                    const icon = h.querySelector('.sort-icon');
                    if (icon) {
                        icon.classList.remove('fa-sort-up', 'fa-sort-down');
                        icon.classList.add('fa-sort');
                    }
                });

                this.classList.add('active');
                this.dataset.sortDir = nextDir;

                const currentIcon = this.querySelector('.sort-icon');
                if (currentIcon) {
                    currentIcon.classList.remove('fa-sort');
                    currentIcon.classList.add(nextDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
                }

                sortTableRows(key, nextDir);
            });
        });

        table.dataset.sortBound = '1';
    });
}

function getSidebarElement() {
    return document.getElementById('adminSidebar');
}

function saveSidebarScrollPosition() {
    const sidebar = getSidebarElement();
    if (!sidebar) return;
    sessionStorage.setItem('adminSidebarScrollTop', String(sidebar.scrollTop || 0));
}

function restoreSidebarScrollPosition() {
    const sidebar = getSidebarElement();
    if (!sidebar) return;
    const stored = sessionStorage.getItem('adminSidebarScrollTop');
    if (stored === null) return;
    const top = Number(stored);
    if (!Number.isNaN(top)) {
        sidebar.scrollTop = top;
    }
}

function syncPageScopedStyles(doc) {
    // Remove any previously active page-scoped styles.
    document.querySelectorAll('style[data-page-style], link[data-page-style]').forEach(node => {
        node.remove();
    });

    if (!doc) return;

    // Apply page-scoped styles from the fetched document.
    doc.querySelectorAll('style[data-page-style], link[data-page-style]').forEach(node => {
        const clone = node.cloneNode(true);
        document.head.appendChild(clone);
    });
}

function syncPageScopedScripts(doc) {
    // Remove any previously active page-scoped scripts.
    document.querySelectorAll('script[data-page-script]').forEach(node => {
        node.remove();
    });

    if (!doc) return;

    // Execute page-scoped scripts from the fetched document.
    doc.querySelectorAll('script[data-page-script]').forEach(node => {
        const script = document.createElement('script');

        // Copy attributes (including data-page-script, src, type, etc.)
        Array.from(node.attributes).forEach(attr => {
            script.setAttribute(attr.name, attr.value);
        });

        if (node.src) {
            script.src = node.src;
        } else {
            script.textContent = node.textContent;
        }

        document.body.appendChild(script);
    });
}

function initMonthlyRevenueChartInMainContent() {
    const canvas = document.querySelector('#mainContent #monthlyRevenueChart');
    if (!canvas || typeof Chart === 'undefined') return;
    if (canvas.dataset.chartBound === '1') return;

    const parseJson = (value, fallback) => {
        try {
            return JSON.parse(value || '');
        } catch (e) {
            return fallback;
        }
    };

    const shipmentSeries = parseJson(canvas.dataset.shipmentSeries, []);
    const ecommerceSeries = parseJson(canvas.dataset.ecommerceSeries, []);
    const shipmentLabel = canvas.dataset.shipmentLabel || 'Shipment Revenue';
    const ecommerceLabel = canvas.dataset.ecommerceLabel || 'Ecommerce Revenue';
    const yearFilter = document.querySelector('#mainContent #revenueYearFilter');
    const monthFilter = document.querySelector('#mainContent #revenueMonthFilter');

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const locale = document.documentElement.lang || 'en';
    const docDir = (document.documentElement.getAttribute('dir') || '').toLowerCase();
    const isRtl = docDir === 'rtl' || /^ar\b/i.test(locale);
    const formatNumber = (value) => new Intl.NumberFormat(document.documentElement.lang || 'en').format(value);
    const monthNames = Array.from({ length: 12 }, (_, idx) =>
        new Intl.DateTimeFormat(locale, { month: 'short' }).format(new Date(2024, idx, 1))
    );
    const allMonthsLabel = isRtl ? 'كل الشهور' : 'All Months';

    const toKey = (year, month) => `${year}-${month}`;

    const makeSeriesMap = (series) => {
        const map = new Map();
        series.forEach(item => {
            const year = Number(item.year);
            const month = Number(item.month);
            const total = Number(item.total || 0);
            if (!Number.isFinite(year) || !Number.isFinite(month)) return;
            map.set(toKey(year, month), total);
        });
        return map;
    };

    const shipmentMap = makeSeriesMap(shipmentSeries);
    const ecommerceMap = makeSeriesMap(ecommerceSeries);

    const years = [...new Set([
        ...shipmentSeries.map(item => Number(item.year)),
        ...ecommerceSeries.map(item => Number(item.year))
    ].filter(Number.isFinite))].sort((a, b) => b - a);

    if (!yearFilter || !monthFilter || years.length === 0) return;

    yearFilter.innerHTML = years
        .map(year => `<option value="${year}">${year}</option>`)
        .join('');

    monthFilter.innerHTML = [
        `<option value="all">${allMonthsLabel}</option>`,
        ...monthNames.map((name, idx) => `<option value="${idx + 1}">${name}</option>`)
    ].join('');

    yearFilter.value = String(years[0]);
    monthFilter.value = 'all';

    let chartInstance = null;

    const getMonthValue = (map, year, month) => map.get(toKey(year, month)) ?? 0;

    const getLineChartConfig = (selectedYear) => {
        const shipmentGradient = ctx.createLinearGradient(0, 0, 0, 420);
        shipmentGradient.addColorStop(0, 'rgba(37, 99, 235, 0.38)');
        shipmentGradient.addColorStop(1, 'rgba(37, 99, 235, 0.03)');

        const ecommerceGradient = ctx.createLinearGradient(0, 0, 0, 420);
        ecommerceGradient.addColorStop(0, 'rgba(245, 158, 11, 0.38)');
        ecommerceGradient.addColorStop(1, 'rgba(245, 158, 11, 0.03)');

        return {
            type: 'line',
            data: {
                labels: monthNames,
                datasets: [{
                    label: shipmentLabel,
                    data: Array.from({ length: 12 }, (_, idx) => getMonthValue(shipmentMap, selectedYear, idx + 1)),
                    yAxisID: 'yShipment',
                    borderColor: 'rgb(37, 99, 235)',
                    backgroundColor: shipmentGradient,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgb(37, 99, 235)',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    pointHoverBorderWidth: 2,
                    pointRadius: 3.5,
                    borderWidth: 4,
                    tension: 0.42,
                    fill: true,
                }, {
                    label: ecommerceLabel,
                    data: Array.from({ length: 12 }, (_, idx) => getMonthValue(ecommerceMap, selectedYear, idx + 1)),
                    yAxisID: 'yEcommerce',
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: ecommerceGradient,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgb(245, 158, 11)',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    pointHoverBorderWidth: 2,
                    pointRadius: 3.5,
                    borderWidth: 4,
                    tension: 0.42,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                layout: {
                    padding: {
                        top: 8,
                        right: 8,
                        bottom: 4,
                        left: 8,
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        rtl: isRtl,
                        textDirection: isRtl ? 'rtl' : 'ltr',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 16,
                            font: {
                                size: 12,
                                weight: '600',
                            },
                        }
                    },
                    tooltip: {
                        rtl: isRtl,
                        textDirection: isRtl ? 'rtl' : 'ltr',
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        borderColor: 'rgba(148, 163, 184, 0.35)',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 12,
                        titleFont: {
                            weight: '700'
                        },
                        bodyFont: {
                            weight: '600'
                        },
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${formatNumber(context.parsed.y)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        reverse: isRtl,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.14)',
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#475569',
                            maxRotation: 0,
                            autoSkipPadding: 14,
                        }
                    },
                    yShipment: {
                        position: 'left',
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.14)',
                            drawBorder: false,
                        },
                        ticks: {
                            color: 'rgb(37, 99, 235)',
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        },
                        title: {
                            display: true,
                            text: shipmentLabel,
                            color: 'rgb(37, 99, 235)',
                            font: {
                                weight: '700'
                            }
                        }
                    },
                    yEcommerce: {
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                            color: 'rgba(245, 158, 11, 0.25)',
                            drawBorder: false,
                        },
                        ticks: {
                            color: 'rgb(217, 119, 6)',
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        },
                        title: {
                            display: true,
                            text: ecommerceLabel,
                            color: 'rgb(217, 119, 6)',
                            font: {
                                weight: '700'
                            }
                        }
                    }
                }
            }
        };
    };

    const getBarChartConfig = (selectedYear, selectedMonth) => {
        const shipmentValue = getMonthValue(shipmentMap, selectedYear, selectedMonth);
        const ecommerceValue = getMonthValue(ecommerceMap, selectedYear, selectedMonth);
        const selectedMonthLabel = `${monthNames[selectedMonth - 1]} ${selectedYear}`;

        return {
            type: 'bar',
            data: {
                labels: [shipmentLabel, ecommerceLabel],
                datasets: [{
                    label: selectedMonthLabel,
                    data: [shipmentValue, ecommerceValue],
                    backgroundColor: ['rgba(37, 99, 235, 0.75)', 'rgba(245, 158, 11, 0.78)'],
                    borderColor: ['rgb(37, 99, 235)', 'rgb(217, 119, 6)'],
                    borderWidth: 2,
                    borderRadius: 12,
                    borderSkipped: false,
                    maxBarThickness: 64,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        rtl: isRtl,
                        textDirection: isRtl ? 'rtl' : 'ltr',
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        borderColor: 'rgba(148, 163, 184, 0.35)',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 12,
                        titleFont: {
                            weight: '700'
                        },
                        bodyFont: {
                            weight: '600'
                        },
                        callbacks: {
                            title: function() {
                                return selectedMonthLabel;
                            },
                            label: function(context) {
                                return `${context.label}: ${formatNumber(context.parsed.y)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        reverse: isRtl,
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#475569',
                            font: {
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.16)',
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#475569',
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        }
                    }
                }
            }
        };
    };

    const renderChart = () => {
        const selectedYear = Number(yearFilter.value);
        const selectedMonth = monthFilter.value;

        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }

        const config = selectedMonth === 'all'
            ? getLineChartConfig(selectedYear)
            : getBarChartConfig(selectedYear, Number(selectedMonth));

        chartInstance = new Chart(ctx, config);
    };

    yearFilter.addEventListener('change', renderChart);
    monthFilter.addEventListener('change', renderChart);

    renderChart();

    canvas.dataset.chartBound = '1';
}

function renderAjaxMainContent(html, targetUrl, options = {}) {
    const mainContent = document.getElementById('mainContent');
    if (!mainContent) return;

    const { pushHistory = true } = options;
    const previousPath = normalizePath(window.location.href);
    const nextPath = normalizePath(targetUrl || window.location.href);
    const shouldKeepScroll = previousPath === nextPath;
    const previousScrollTop = mainContent.scrollTop;

    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newContent = doc.querySelector('main.main-content');
    const newTitle = doc.querySelector('.page-title-wrapper h1');

    syncPageScopedStyles(doc);

    if (!newContent) {
        window.location.href = targetUrl || window.location.href;
        return;
    }

    mainContent.style.transition = 'opacity 0.3s ease';
    mainContent.innerHTML = newContent.innerHTML;

    syncPageScopedScripts(doc);
    initMonthlyRevenueChartInMainContent();

    attachTableSearchHandlers();
    attachProductsTableSortHandlers();
    prepareContentModals();
    initMonthlyRevenueChartInMainContent();

    if (newTitle) {
        const titleElement = document.querySelector('.page-title-wrapper h1');
        if (titleElement) {
            titleElement.textContent = newTitle.textContent;
        }
    }

    if (pushHistory && targetUrl) {
        window.history.pushState({ adminAjax: true, url: targetUrl }, '', targetUrl);
    }

    updateSidebarActiveState(targetUrl || window.location.href);
    restoreSidebarScrollPosition();

    if (shouldKeepScroll) {
        mainContent.scrollTop = previousScrollTop;
    } else if (typeof mainContent.scrollTo === 'function') {
        mainContent.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        window.scrollTo(0, 0);
    }

    if (typeof syncRolePermissionMatrix === 'function') {
        syncRolePermissionMatrix();
    }
}

function prepareContentModals() {
    document.querySelectorAll('#mainContent .modal').forEach(modal => {
        if (modal.dataset.portalPrepared === '1') return;
        modal.dataset.portalPrepared = '1';

        modal.addEventListener('show.bs.modal', function () {
            saveSidebarScrollPosition();

            // Move modal to body so it is not clipped by scrolling containers.
            if (this.parentElement !== document.body) {
                document.body.appendChild(this);
            }
        });

        modal.addEventListener('hidden.bs.modal', function () {
            restoreSidebarScrollPosition();
        });
    });
}

// AJAX Navigation Handler
function loadContentViaAjax(url, event, options = {}) {
    const { pushHistory = true } = options;

    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    }

    saveSidebarScrollPosition();

    // Show loading state
    const mainContent = document.getElementById('mainContent');
    if (mainContent) {
        mainContent.style.opacity = '0.6';
        mainContent.style.pointerEvents = 'none';
    }

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.text();
    })
    .then(html => {
        renderAjaxMainContent(html, url, { pushHistory });

        // Restore opacity
        setTimeout(() => {
            mainContent.style.opacity = '1';
            mainContent.style.pointerEvents = 'auto';
        }, 100);
    })
    .catch(error => {
        console.error('AJAX Load Error:', error);
        // Fallback to regular navigation
        mainContent.style.opacity = '1';
        mainContent.style.pointerEvents = 'auto';
        window.location.href = url;
    });
}

function normalizePath(url) {
    try {
        const parsed = new URL(url, window.location.origin);
        const cleanPath = parsed.pathname.replace(/\/+$/, '');
        return cleanPath || '/';
    } catch (e) {
        return '/';
    }
}

function updateSidebarActiveState(targetUrl) {
    const sidebar = document.getElementById('adminSidebar');
    if (!sidebar) return;

    const targetPath = normalizePath(targetUrl);
    const allNavLinks = sidebar.querySelectorAll('a.nav-link[href]');

    allNavLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#')) return;
        link.classList.remove('active');
    });

    let bestMatch = null;
    let bestLength = -1;

    allNavLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

        const linkPath = normalizePath(href);
        const isExactMatch = targetPath === linkPath;
        const isNestedMatch = linkPath !== '/' && targetPath.startsWith(linkPath + '/');

        if ((isExactMatch || isNestedMatch) && linkPath.length > bestLength) {
            bestMatch = link;
            bestLength = linkPath.length;
        }
    });

    if (bestMatch) {
        bestMatch.classList.add('active');
    }

    const settingsSubmenu = document.getElementById('settingsSubmenu');
    const settingsToggle = sidebar.querySelector('a.nav-link[href="#settingsSubmenu"]');

    if (settingsSubmenu && settingsToggle) {
        const isSettingsRoute = !!(bestMatch && bestMatch.closest('#settingsSubmenu'));
        settingsSubmenu.classList.toggle('show', isSettingsRoute);
        settingsToggle.setAttribute('aria-expanded', isSettingsRoute ? 'true' : 'false');
    }
}

function getRolePermissionCheckboxes() {
    return Array.from(document.querySelectorAll('.permission-checkbox[name="permissions[]"]'));
}

function updateRolePermissionSelectedCount() {
    const counter = document.getElementById('rolePermissionsSelectedCount');
    if (!counter) return;

    const checkedCount = getRolePermissionCheckboxes().filter(checkbox => checkbox.checked).length;
    counter.textContent = `{{ __('admin-dashboard.selected_permissions_count', ['count' => '__COUNT__']) }}`.replace('__COUNT__', String(checkedCount));
}

function syncRolePermissionGroupState(groupKey) {
    const groupCheckboxes = getRolePermissionCheckboxes().filter(checkbox => checkbox.dataset.group === groupKey);
    const groupButton = document.querySelector(`[onclick="window.rolePermissionsToggleGroup('${groupKey}')"]`);

    if (!groupButton) return;

    const checkedCount = groupCheckboxes.filter(checkbox => checkbox.checked).length;
    const allChecked = groupCheckboxes.length > 0 && checkedCount === groupCheckboxes.length;

    groupButton.classList.toggle('btn-primary', allChecked);
    groupButton.classList.toggle('btn-outline-primary', !allChecked);
}

function syncRolePermissionMatrix() {
    const groupKeys = [...new Set(getRolePermissionCheckboxes().map(checkbox => checkbox.dataset.group))];
    groupKeys.forEach(syncRolePermissionGroupState);
    updateRolePermissionSelectedCount();
}

window.rolePermissionsSelectAllPermissions = function () {
    getRolePermissionCheckboxes().forEach(checkbox => {
        checkbox.checked = true;
    });
    syncRolePermissionMatrix();
};

window.rolePermissionsClearAllPermissions = function () {
    getRolePermissionCheckboxes().forEach(checkbox => {
        checkbox.checked = false;
    });
    syncRolePermissionMatrix();
};

window.rolePermissionsToggleGroup = function (groupKey) {
    const groupCheckboxes = getRolePermissionCheckboxes().filter(checkbox => checkbox.dataset.group === groupKey);
    const shouldCheck = groupCheckboxes.some(checkbox => !checkbox.checked);

    groupCheckboxes.forEach(checkbox => {
        checkbox.checked = shouldCheck;
    });

    syncRolePermissionMatrix();
};

window.rolePermissionsToggleCheckbox = function () {
    syncRolePermissionMatrix();
};

document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const toggleButton = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent');
    const sidebar = document.getElementById('adminSidebar');

    restoreSidebarScrollPosition();

    // Ensure current entry has state so browser back/forward works with AJAX content.
    if (!window.history.state || !window.history.state.adminAjax) {
        window.history.replaceState({ adminAjax: true, url: window.location.href }, '', window.location.href);
    }

    // Set initial active item on first load
    updateSidebarActiveState(window.location.href);
    syncRolePermissionMatrix();

    // Attach AJAX navigation to sidebar links
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            // Only use AJAX for navigation links, not for collapse toggles
            if (href && !href.startsWith('#')) {
                saveSidebarScrollPosition();
                updateSidebarActiveState(href);
                loadContentViaAjax(href, e);
            }
        });
    });

    // Keep sidebar state by handling in-content links/forms via AJAX.
    mainContent?.addEventListener('click', function (event) {
        const link = event.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;
        const bootstrapToggle = (link.dataset.bsToggle || '').toLowerCase();
        if (bootstrapToggle && bootstrapToggle !== 'tooltip') return;
        if (link.closest('.language-menu')) return;
        if (href.includes('/lang/')) return;

        saveSidebarScrollPosition();
        loadContentViaAjax(href, event);
    });

    mainContent?.addEventListener('submit', function (event) {
        const form = event.target.closest('form');
        if (!form) return;

        const action = form.getAttribute('action');
        if (!action) return;

        if (form.hasAttribute('data-no-ajax')) return;

        event.preventDefault();
        saveSidebarScrollPosition();

        const formData = new FormData(form);
        mainContent.style.opacity = '0.6';
        mainContent.style.pointerEvents = 'none';

        fetch(action, {
            method: (form.getAttribute('method') || 'POST').toUpperCase(),
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.text().then(html => ({ html, url: response.url }));
        })
        .then(({ html, url }) => {
            renderAjaxMainContent(html, url, { pushHistory: true });
        })
        .catch(() => {
            saveSidebarScrollPosition();
            form.submit();
        })
        .finally(() => {
            setTimeout(() => {
                mainContent.style.opacity = '1';
                mainContent.style.pointerEvents = 'auto';
            }, 100);
        });
    });

    attachTableSearchHandlers();
    attachProductsTableSortHandlers();
    prepareContentModals();

    if (!toggleButton) {
        return;
    }

    // Handle browser back/forward buttons.
    window.addEventListener('popstate', function () {
        saveSidebarScrollPosition();
        loadContentViaAjax(window.location.href, null, { pushHistory: false });
    });

    const isDesktop = () => window.innerWidth >= 992;

    const toggleDesktopState = () => {
        body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('adminSidebarCollapsed', body.classList.contains('sidebar-collapsed'));
    };

    const closeMobileSidebar = () => {
        if (!isDesktop() && body.classList.contains('sidebar-open')) {
            body.classList.remove('sidebar-open');
        }
    };

    const storedCollapsed = localStorage.getItem('adminSidebarCollapsed') === 'true';

    if (storedCollapsed && isDesktop()) {
        body.classList.add('sidebar-collapsed');
    }

    // Close sidebar on mobile when clicking a link
    if (sidebar) {
        sidebar.addEventListener('scroll', saveSidebarScrollPosition, { passive: true });

        sidebar.addEventListener('click', function(e) {
            if (!isDesktop() && e.target.closest('a.nav-link')) {
                setTimeout(closeMobileSidebar, 300);
            }
        });
    }

    toggleButton.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (isDesktop()) {
            toggleDesktopState();
        } else {
            body.classList.toggle('sidebar-open');
        }
    });

    document.addEventListener('click', function (event) {
        const mobileToggle = event.target.closest('#sidebarMobileToggle');
        if (!mobileToggle) return;

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

    // Close sidebar on escape key
    document.addEventListener('keyup', function (event) {
        if (event.key === 'Escape' && body.classList.contains('sidebar-open')) {
            closeMobileSidebar();
        }
    });

    // Handle resize and orientation change
    const handleResize = () => {
        if (isDesktop()) {
            body.classList.remove('sidebar-open');
            const shouldCollapse = localStorage.getItem('adminSidebarCollapsed') === 'true';
            body.classList.toggle('sidebar-collapsed', shouldCollapse);
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
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>

</html>
