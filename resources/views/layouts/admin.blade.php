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
    <!-- Arabic font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700;800&family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')

    <style>
        /* =========================================================
           MetwGo Phase 2 Admin UI/UX Redesign
           File: resources/views/layouts/admin.blade.php

           Goals:
           - Apply MetwGo business identity: orange + purple
           - Keep backend logic, routes, permissions, translations unchanged
           - Improve sidebar, topbar, cards, buttons, forms, tables, RTL/LTR
           - Create a calmer professional admin dashboard experience
        ========================================================= */

        :root {
            --sidebar-width: 284px;
            --sidebar-collapsed-width: 84px;

            --metw-orange: #FF7043;
            --metw-orange-dark: #F45B2E;
            --metw-orange-soft: #FFF1EC;

            --metw-purple: #7B00A8;
            --metw-purple-dark: #4C0078;
            --metw-purple-deep: #1F1235;
            --metw-purple-soft: #F5EBFF;

            --metw-bg: #F7F5FA;
            --metw-bg-2: #FBFAFD;
            --metw-surface: #FFFFFF;
            --metw-surface-soft: #F8F7FA;

            --metw-text: #24172E;
            --metw-heading: #181022;
            --metw-muted: #7C7285;
            --metw-border: #ECE6F2;

            --metw-success: #35D36F;
            --metw-danger: #FF4B2E;
            --metw-warning: #FFB347;
            --metw-info: #7B00A8;

            --metw-gradient-primary: linear-gradient(135deg, #FF7043 0%, #7B00A8 100%);
            --metw-gradient-purple: linear-gradient(135deg, #8A00B8 0%, #4C0078 100%);
            --metw-gradient-orange: linear-gradient(135deg, #FF7A45 0%, #FF5A3C 100%);
            --metw-gradient-admin: linear-gradient(160deg, #1F1235 0%, #4C0078 55%, #7B00A8 100%);

            --metw-radius-sm: 12px;
            --metw-radius-md: 16px;
            --metw-radius-lg: 22px;
            --metw-radius-xl: 28px;

            --metw-shadow-xs: 0 3px 10px rgba(36, 23, 46, 0.04);
            --metw-shadow-sm: 0 8px 24px rgba(36, 23, 46, 0.07);
            --metw-shadow-md: 0 16px 38px rgba(36, 23, 46, 0.12);
            --metw-shadow-purple: 0 18px 36px rgba(123, 0, 168, 0.20);
            --metw-shadow-orange: 0 14px 28px rgba(255, 112, 67, 0.22);

            --topbar-bg: #FFFFFF;
            --topbar-border: var(--metw-border);
            --topbar-shadow: var(--metw-shadow-sm);
            --topbar-text: var(--metw-heading);
            --topbar-muted: var(--metw-muted);
            --topbar-accent: var(--metw-orange);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            height: 100%;
        }

        body {
            margin: 0;
            overflow: hidden;
            color: var(--metw-text);
            background:
                radial-gradient(circle at top left, rgba(123, 0, 168, 0.10), transparent 34rem),
                radial-gradient(circle at top right, rgba(255, 112, 67, 0.09), transparent 30rem),
                linear-gradient(180deg, #FFFFFF 0%, var(--metw-bg) 100%);
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }

        html[dir="rtl"] body {
            font-family: "El Messiri", "Cairo", "Tajawal", "IBM Plex Sans Arabic", sans-serif;
        }

        a {
            text-decoration: none;
        }

        ::selection {
            background: rgba(255, 112, 67, 0.22);
            color: var(--metw-heading);
        }

        /* =========================
           App Shell
        ========================= */

        .main-content {
            margin-inline-start: var(--sidebar-width);
            height: 100vh;
            height: 100dvh;
            overflow-y: auto;
            overflow-x: hidden;
            padding-top: 1rem;
            background: transparent !important;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            transition:
                margin-inline-start 0.28s ease,
                padding 0.28s ease,
                opacity 0.2s ease;
        }

        .main-content.no-sidebar {
            margin-inline-start: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            height: 100dvh;
            overflow-y: auto;
            background:
                radial-gradient(circle at top center, rgba(123, 0, 168, 0.13), transparent 34rem),
                radial-gradient(circle at bottom center, rgba(255, 112, 67, 0.13), transparent 28rem),
                linear-gradient(135deg, #FFFFFF 0%, #F8F2FF 50%, #FFF3EE 100%) !important;
        }

        .main-content::-webkit-scrollbar {
            width: 9px;
        }

        .main-content::-webkit-scrollbar-track {
            background: rgba(236, 230, 242, 0.45);
        }

        .main-content::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(123, 0, 168, 0.45), rgba(255, 112, 67, 0.50));
            border-radius: 999px;
            border: 2px solid rgba(248, 245, 250, 0.85);
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgba(123, 0, 168, 0.65), rgba(255, 112, 67, 0.68));
        }

        body.sidebar-collapsed .main-content {
            margin-inline-start: var(--sidebar-collapsed-width);
        }

        /* =========================
           Sidebar
        ========================= */

        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            inset-block: 0;
            inset-inline-start: 0;
            height: 100vh;
            height: 100dvh;
            z-index: 1030;
            padding: 1rem 0.85rem;
            overflow-y: auto;
            overflow-x: hidden;
            color: rgba(255, 255, 255, 0.86);
            background:
                radial-gradient(circle at 20% 0%, rgba(255, 112, 67, 0.22), transparent 15rem),
                radial-gradient(circle at 100% 35%, rgba(255, 255, 255, 0.08), transparent 18rem),
                var(--metw-gradient-admin) !important;
            border-inline-end: 1px solid rgba(255, 255, 255, 0.10);
            box-shadow: 14px 0 34px rgba(31, 18, 53, 0.20);
            transition:
                width 0.28s ease,
                transform 0.28s ease,
                box-shadow 0.28s ease;
        }

        html[dir="rtl"] .sidebar {
            box-shadow: -14px 0 34px rgba(31, 18, 53, 0.20);
        }

        .sidebar::-webkit-scrollbar,
        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track,
        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.24);
            border-radius: 999px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.42);
        }

        .sidebar-inner {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .sidebar-brand {
            min-height: 58px;
            padding: 0.65rem 0.5rem 0.95rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.13);
        }

        .sidebar-brand > .d-flex {
            min-width: 0;
        }

        .sidebar-brand .fa-crown {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 38px;
            border-radius: 16px;
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.13);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
        }

        .sidebar-brand .brand-text {
            color: #FFFFFF;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.01em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-toggle {
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 38px;
            cursor: pointer;
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.10);
            transition:
                background 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.20);
            transform: translateY(-1px);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
        }

        .sidebar-toggle:active {
            transform: translateY(0);
        }

        .sidebar-toggle::before {
            display: none;
        }

        .sidebar-menu {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding-inline-end: 0.2rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.26) transparent;
        }

        .sidebar-menu .nav.flex-column {
            gap: 0.28rem;
        }

        .sidebar .nav-item {
            width: 100%;
        }

        .sidebar .nav-link {
            min-height: 45px;
            width: 100%;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.56rem 0.64rem;
            overflow: visible;
            border-radius: 16px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: 0.005em;
            transition:
                color 0.2s ease,
                background 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .sidebar .nav-link::before {
            display: none;
        }

        .sidebar .nav-link > * {
            position: relative;
            z-index: 1;
        }

        .sidebar .nav-link i:not(.chevron) {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            color: rgba(255, 255, 255, 0.88);
            background: rgba(255, 255, 255, 0.09);
            font-size: 0.92rem;
            transition:
                color 0.2s ease,
                background 0.2s ease,
                transform 0.2s ease;
        }

        .sidebar .nav-link .link-text {
            flex: 1 1 auto;
            min-width: 0;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .sidebar .nav-link .chevron {
            margin-inline-start: auto;
            flex: 0 0 auto;
            font-size: 0.72rem;
            opacity: 0.82;
            transition: transform 0.2s ease;
        }

        .sidebar .nav-link[aria-expanded="true"] .chevron {
            transform: rotate(180deg);
        }

        .sidebar .nav-link:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.10);
            transform: translateX(2px);
        }

        html[dir="rtl"] .sidebar .nav-link:hover {
            transform: translateX(-2px);
        }

        .sidebar .nav-link:hover i:not(.chevron) {
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar .nav-link.active {
            color: var(--metw-purple-dark) !important;
            background: #FFFFFF;
            box-shadow: 0 14px 28px rgba(20, 8, 31, 0.25);
            transform: none;
        }

        .sidebar .nav-link.active i:not(.chevron) {
            color: #FFFFFF;
            background: var(--metw-gradient-primary);
            box-shadow: 0 8px 18px rgba(123, 0, 168, 0.20);
        }

        .sidebar .nav-link.active .chevron {
            color: var(--metw-purple-dark);
        }

        #settingsSubmenu {
            margin: 0.35rem 0 0.45rem;
            padding: 0;
            overflow: hidden;
            border-radius: 18px;
            background: rgba(20, 8, 31, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        #settingsSubmenu.collapse:not(.show) {
            padding: 0 0.35rem;
            opacity: 0;
        }

        #settingsSubmenu.collapse.show {
            padding: 0.42rem;
            opacity: 1;
        }

        #settingsSubmenu.collapsing {
            padding: 0 0.42rem;
            opacity: 0.75;
            transition: height 0.22s ease, opacity 0.16s ease;
        }

        #settingsSubmenu .nav.flex-column {
            gap: 0.18rem;
        }

        #settingsSubmenu .nav-link {
            min-height: 38px;
            padding: 0.42rem 0.55rem;
            border-radius: 13px;
            font-size: 0.84rem;
            font-weight: 650;
        }

        #settingsSubmenu .nav-link i:not(.chevron) {
            width: 29px;
            height: 29px;
            flex-basis: 29px;
            border-radius: 11px;
            font-size: 0.78rem;
        }

        .sidebar-footer {
            padding-top: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.13);
        }

        .sidebar-footer button {
            min-height: 45px;
            border-radius: 16px !important;
            border-color: rgba(255, 255, 255, 0.22) !important;
            background: rgba(255, 255, 255, 0.09) !important;
            color: #FFFFFF !important;
            font-weight: 800;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }

        .sidebar-footer button:hover {
            background: rgba(255, 112, 67, 0.95) !important;
            border-color: rgba(255, 112, 67, 0.95) !important;
            transform: translateY(-1px);
        }

        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
            padding-inline: 0.75rem;
        }

        body.sidebar-collapsed .sidebar-brand {
            justify-content: center;
            padding-inline: 0;
        }

        body.sidebar-collapsed .sidebar-brand .brand-text,
        body.sidebar-collapsed .sidebar .link-text {
            display: none;
            width: 0;
            height: 0;
            opacity: 0;
            visibility: hidden;
        }

        body.sidebar-collapsed .sidebar-brand .fa-crown {
            display: none;
        }

        body.sidebar-collapsed .sidebar-toggle {
            margin-inline: auto;
        }

        body.sidebar-collapsed .sidebar .nav.flex-column,
        body.sidebar-collapsed .sidebar-menu {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.34rem;
        }

        body.sidebar-collapsed .sidebar .nav-item {
            display: flex;
            justify-content: center;
        }

        body.sidebar-collapsed .sidebar .nav-link {
            width: 48px;
            min-height: 48px;
            padding: 0;
            justify-content: center;
            gap: 0;
        }

        body.sidebar-collapsed .sidebar .nav-link i:not(.chevron) {
            margin: 0 !important;
            width: 38px !important;
            height: 38px;
            flex: 0 0 38px;
            font-size: 1rem;
        }

        body.sidebar-collapsed .sidebar .chevron,
        body.sidebar-collapsed #settingsSubmenu {
            display: none !important;
        }

        /* =========================
           Topbar / Page Header
        ========================= */

        .page-header-wrapper {
            position: sticky;
            top: 1rem;
            z-index: 1100;
            margin-bottom: 1.1rem;
            overflow: visible;
            border: 1px solid rgba(236, 230, 242, 0.92);
            border-radius: var(--metw-radius-xl);
            background: rgba(255, 255, 255, 0.88);
            box-shadow: var(--metw-shadow-sm);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .topbar-shell {
            display: flex;
            align-items: center !important;
            justify-content: space-between;
            gap: 1rem !important;
            margin: 0 !important;
            padding: 1rem 1.15rem !important;
            overflow: visible;
            border: 0 !important;
            border-radius: var(--metw-radius-xl);
            background:
                radial-gradient(circle at 100% 0%, rgba(255, 112, 67, 0.10), transparent 14rem),
                radial-gradient(circle at 0% 0%, rgba(123, 0, 168, 0.09), transparent 14rem),
                rgba(255, 255, 255, 0.94);
        }

        .page-title-wrapper {
            flex: 1 1 auto;
            min-width: 0;
        }

        .page-title-wrapper h1 {
            margin: 0;
            color: var(--metw-heading);
            font-size: clamp(1.28rem, 1.7vw, 1.8rem);
            font-weight: 850;
            line-height: 1.15;
            letter-spacing: -0.035em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .page-title-wrapper h1::after {
            content: '';
            display: block;
            width: 56px;
            height: 4px;
            margin-top: 0.58rem;
            border-radius: 999px;
            background: var(--metw-gradient-primary);
            box-shadow: 0 5px 12px rgba(255, 112, 67, 0.22);
        }

        html[dir="rtl"] .page-title-wrapper h1 {
            letter-spacing: 0;
        }

        .page-actions-wrapper {
            flex: 0 0 auto;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end;
            gap: 0.6rem !important;
            white-space: nowrap;
            min-width: 0;
        }

        .page-actions-group {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .topbar-control {
            min-height: 42px;
            border-radius: 16px !important;
            border: 1px solid var(--metw-border) !important;
            background: #FFFFFF !important;
            color: var(--metw-text) !important;
            font-weight: 800;
            box-shadow: var(--metw-shadow-xs);
            transition:
                color 0.2s ease,
                border-color 0.2s ease,
                background 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .topbar-control:hover,
        .topbar-control:focus {
            color: var(--metw-purple) !important;
            border-color: rgba(123, 0, 168, 0.22) !important;
            background: #FFFFFF !important;
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(123, 0, 168, 0.10);
        }

        #sidebarMobileToggle {
            width: 42px;
            padding-inline: 0;
        }

        #languageDropdown {
            min-width: 130px;
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.52rem;
            padding-inline: 0.8rem;
            font-size: 0.88rem;
        }

        #languageDropdown img,
        .language-menu img {
            width: 18px !important;
            height: 18px !important;
            object-fit: cover;
            box-shadow: 0 0 0 2px #FFFFFF;
        }

        #languageDropdown span {
            max-width: 72px;
            overflow: hidden;
            text-overflow: ellipsis;
            color: inherit;
        }

        #languageDropdown.dropdown-toggle::after {
            margin-inline-start: 0.24rem;
        }

        #notificationDropdown {
            width: 42px;
            height: 42px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px !important;
        }

        #notificationDropdown .badge {
            min-width: 19px;
            height: 19px;
            padding: 0 5px;
            border: 2px solid #FFFFFF;
            font-size: 0.68rem;
            font-weight: 850;
            line-height: 17px;
            background: var(--metw-danger) !important;
            box-shadow: 0 4px 10px rgba(255, 75, 46, 0.28);
        }

        /* =========================
           Cards / Surfaces
        ========================= */

        .card {
            overflow: hidden;
            border: 1px solid rgba(236, 230, 242, 0.94) !important;
            border-radius: var(--metw-radius-lg) !important;
            background: var(--metw-surface) !important;
            box-shadow: var(--metw-shadow-sm) !important;
            transition:
                box-shadow 0.22s ease,
                transform 0.22s ease,
                border-color 0.22s ease;
        }

        .card:hover {
            border-color: rgba(123, 0, 168, 0.14) !important;
            transform: translateY(-2px);
            box-shadow: var(--metw-shadow-md) !important;
        }

        .card-header {
            padding: 1rem 1.15rem;
            border-bottom: 1px solid var(--metw-border) !important;
            background:
                linear-gradient(180deg, #FFFFFF 0%, #FBFAFD 100%) !important;
        }

        .card-body {
            padding: 1.15rem;
        }

        .card-footer {
            border-top: 1px solid var(--metw-border) !important;
            background: #FBFAFD !important;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            color: #FFFFFF !important;
            border: 0 !important;
            border-radius: 24px !important;
            background:
                radial-gradient(circle at 100% 0%, rgba(255, 255, 255, 0.18), transparent 9rem),
                var(--metw-gradient-purple) !important;
            box-shadow: var(--metw-shadow-purple) !important;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: auto -25% -45% auto;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.10);
            opacity: 1;
            pointer-events: none;
        }

        .stat-card:nth-of-type(2n) {
            background:
                radial-gradient(circle at 100% 0%, rgba(255, 255, 255, 0.18), transparent 9rem),
                var(--metw-gradient-primary) !important;
            box-shadow: var(--metw-shadow-orange) !important;
        }

        .stat-card .card-body {
            position: relative;
            z-index: 1;
            padding: 1.35rem !important;
        }

        .stat-card h1,
        .stat-card h2,
        .stat-card h3,
        .stat-card h4,
        .stat-card h5,
        .stat-card h6,
        .stat-card p,
        .stat-card span {
            color: inherit;
        }

        .stat-icon {
            width: 66px;
            height: 66px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.20);
            background: rgba(255, 255, 255, 0.16);
            font-size: 1.72rem !important;
            opacity: 1;
            transition: transform 0.2s ease;
        }

        .stat-card:hover .stat-icon {
            transform: translateY(-2px) scale(1.03);
        }

        /* =========================
           Buttons
        ========================= */

        .btn {
            position: relative;
            overflow: visible;
            border-radius: 14px !important;
            font-weight: 800 !important;
            letter-spacing: 0.005em;
            transition:
                color 0.2s ease,
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .btn::after,
        .page-actions-group .btn::before {
            display: none !important;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0) scale(0.99);
        }

        .btn-primary,
        .btn.btn-primary {
            border-color: transparent !important;
            background: var(--metw-gradient-orange) !important;
            color: #FFFFFF !important;
            box-shadow: var(--metw-shadow-orange);
        }

        .btn-primary:hover,
        .btn.btn-primary:hover {
            background: linear-gradient(135deg, #FF6736 0%, #F04D27 100%) !important;
            box-shadow: 0 16px 30px rgba(255, 112, 67, 0.28);
        }

        .btn-outline-primary {
            color: var(--metw-purple) !important;
            border-color: rgba(123, 0, 168, 0.28) !important;
            background: #FFFFFF !important;
        }

        .btn-outline-primary:hover {
            color: #FFFFFF !important;
            background: var(--metw-gradient-purple) !important;
            border-color: transparent !important;
            box-shadow: var(--metw-shadow-purple);
        }

        .btn-outline-secondary,
        .btn-light {
            color: var(--metw-text) !important;
            border-color: var(--metw-border) !important;
            background: #FFFFFF !important;
        }

        .btn-outline-secondary:hover,
        .btn-light:hover {
            color: var(--metw-purple) !important;
            border-color: rgba(123, 0, 168, 0.20) !important;
            background: var(--metw-purple-soft) !important;
        }

        .btn-danger {
            border-color: transparent !important;
            background: linear-gradient(135deg, #FF4B2E 0%, #DD2C12 100%) !important;
            box-shadow: 0 12px 24px rgba(255, 75, 46, 0.20);
        }

        .btn-success {
            border-color: transparent !important;
            background: linear-gradient(135deg, #35D36F 0%, #1FB85A 100%) !important;
            box-shadow: 0 12px 24px rgba(53, 211, 111, 0.18);
        }

        /* =========================
           Forms
        ========================= */

        .form-label {
            color: var(--metw-heading);
            font-weight: 800;
            font-size: 0.92rem;
        }

        .form-control,
        .form-select {
            min-height: 44px;
            border-radius: 15px !important;
            border: 1px solid var(--metw-border) !important;
            background-color: var(--metw-surface-soft) !important;
            color: var(--metw-text) !important;
            font-weight: 600;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .form-control::placeholder {
            color: #B1A8BA;
            font-weight: 500;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #FFFFFF !important;
            border-color: rgba(123, 0, 168, 0.32) !important;
            box-shadow: 0 0 0 0.22rem rgba(123, 0, 168, 0.10) !important;
        }

        .input-group-text {
            border-color: var(--metw-border) !important;
            background: #FFFFFF !important;
            color: var(--metw-muted);
            border-radius: 15px !important;
        }

        /* =========================
           Tables
        ========================= */

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
        }

        .table-search-input {
            max-width: 250px;
        }

        .table {
            margin-bottom: 0;
            vertical-align: middle;
            color: var(--metw-text);
        }

        .table thead th {
            border-bottom: 1px solid var(--metw-border) !important;
            background: #FBFAFD;
            color: var(--metw-muted);
            font-size: 0.76rem;
            font-weight: 850;
            letter-spacing: 0.055em;
            text-transform: uppercase;
        }

        html[dir="rtl"] .table thead th {
            letter-spacing: 0;
        }

        .table tbody td {
            border-color: #F1ECF6 !important;
            color: #3B3045;
            font-weight: 560;
        }

        .table tbody tr {
            transition: background 0.18s ease;
        }

        .table tbody tr:hover {
            background: #FFFBF9;
        }

        .table-responsive {
            border-radius: var(--metw-radius-lg);
        }

        /* =========================
           Badges / Alerts / Dropdowns
        ========================= */

        .badge {
            border-radius: 999px;
            font-weight: 850;
            letter-spacing: 0.01em;
        }

        .bg-primary {
            background-color: var(--metw-purple) !important;
        }

        .bg-danger {
            background-color: var(--metw-danger) !important;
        }

        .bg-success {
            background-color: var(--metw-success) !important;
        }

        .text-primary {
            color: var(--metw-purple) !important;
        }

        .text-muted {
            color: var(--metw-muted) !important;
        }

        .alert {
            border: 0 !important;
            border-radius: 18px !important;
            box-shadow: var(--metw-shadow-sm);
            font-weight: 700;
            animation: metwSlideIn 0.24s ease;
        }

        .alert-success {
            color: #155B31;
            background: #EFFFF5;
        }

        .alert-danger {
            color: #8E2415;
            background: #FFF0EC;
        }

        @keyframes metwSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-close {
            transition: opacity 0.18s ease, transform 0.18s ease;
        }

        .btn-close:hover {
            opacity: 0.8;
            transform: scale(1.04);
        }

        .dropdown-menu {
            z-index: 1400;
            padding: 0.45rem !important;
            border: 1px solid rgba(236, 230, 242, 0.95) !important;
            border-radius: 18px !important;
            background: #FFFFFF;
            box-shadow: 0 20px 44px rgba(36, 23, 46, 0.16) !important;
            opacity: 0;
            transform: translate3d(0, -6px, 0);
            transform-origin: top center;
            transition: opacity 0.13s ease, transform 0.13s ease;
        }

        .dropdown-menu.show {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        .dropdown-menu-end {
            transform-origin: top right;
        }

        html[dir="rtl"] .dropdown-menu-end {
            transform-origin: top left;
        }

        .dropdown-header {
            color: var(--metw-heading);
            font-weight: 850;
        }

        .dropdown-item {
            position: relative;
            padding: 0.6rem 0.75rem;
            border-radius: 13px;
            color: var(--metw-text);
            font-weight: 700;
            transition: color 0.18s ease, background 0.18s ease;
        }

        .dropdown-item::before {
            display: none;
        }

        .dropdown-item:hover,
        .dropdown-item.active,
        .dropdown-item:active {
            color: var(--metw-purple) !important;
            background: var(--metw-purple-soft) !important;
        }

        .dropdown-divider {
            border-color: var(--metw-border);
        }

        .language-menu {
            min-width: 162px;
        }

        .page-actions-wrapper .dropdown {
            position: relative;
            z-index: 1300;
        }

        /* Notifications dropdown refinement */
        #notificationDropdown + .dropdown-menu {
            width: min(360px, calc(100vw - 2rem)) !important;
            max-height: 420px !important;
            overflow-y: auto;
        }

        #notificationDropdown + .dropdown-menu .dropdown-item {
            gap: 0.65rem;
            white-space: normal;
        }

        #notificationDropdown + .dropdown-menu .dropdown-item i {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 34px;
            border-radius: 12px;
            background: var(--metw-purple-soft);
            color: var(--metw-purple) !important;
        }

        /* =========================
           Modals
        ========================= */

        .modal {
            z-index: 2060 !important;
        }

        .modal-backdrop {
            z-index: 2050 !important;
        }

        .modal-content {
            border: 0;
            border-radius: 24px;
            box-shadow: 0 28px 70px rgba(36, 23, 46, 0.22);
        }

        .modal-header,
        .modal-footer {
            border-color: var(--metw-border);
        }

        /* =========================
           Responsive
        ========================= */

        @media (max-width: 1199.98px) {
            :root {
                --sidebar-width: 270px;
            }
        }

        @media (max-width: 991.98px) {
            body {
                overflow: auto;
            }

            .sidebar {
                width: var(--sidebar-width);
                max-width: 88vw;
                z-index: 1400;
                transform: translateX(-105%);
                box-shadow: 18px 0 46px rgba(31, 18, 53, 0.34);
            }

            html[dir="rtl"] .sidebar {
                inset-inline-start: auto;
                inset-inline-end: 0;
                transform: translateX(105%);
                box-shadow: -18px 0 46px rgba(31, 18, 53, 0.34);
            }

            body.sidebar-open .sidebar,
            html[dir="rtl"] body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-inline-start: 0 !important;
                padding: 0.8rem !important;
                height: 100vh;
                height: 100dvh;
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
                z-index: 1300;
                background: rgba(31, 18, 53, 0.46);
                backdrop-filter: blur(3px);
            }

            body.sidebar-open .page-header-wrapper {
                z-index: 1200;
            }

            .page-header-wrapper {
                top: 0.5rem;
                margin-bottom: 0.9rem;
                border-radius: 22px;
            }

            .topbar-shell {
                align-items: flex-start !important;
                padding: 0.9rem !important;
                border-radius: 22px;
            }

            .page-title-wrapper {
                width: 100%;
                flex-basis: 100%;
            }

            .page-title-wrapper h1 {
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
                font-size: 1.28rem;
            }

            .page-actions-wrapper {
                width: 100%;
                flex: 1 1 100%;
                justify-content: space-between !important;
                gap: 0.55rem !important;
            }

            .page-actions-group {
                width: 100%;
                justify-content: flex-start;
                order: 3;
                margin-top: 0.25rem;
            }

            .page-actions-group .btn {
                min-height: 40px;
            }

            .sidebar-toggle,
            #sidebarMobileToggle {
                min-width: 44px;
                min-height: 44px;
                touch-action: manipulation;
            }

            .sidebar .nav-link {
                min-height: 46px;
            }

            .sidebar-footer button {
                min-height: 46px;
            }

            .dropdown-menu {
                max-width: calc(100vw - 1.5rem);
            }
        }

        @media (max-width: 575.98px) {
            .main-content {
                padding: 0.55rem !important;
            }

            .page-header-wrapper {
                top: 0.35rem;
                border-radius: 18px;
            }

            .topbar-shell {
                padding: 0.75rem !important;
                border-radius: 18px;
            }

            .page-title-wrapper h1 {
                font-size: 1.12rem;
            }

            .page-title-wrapper h1::after {
                width: 42px;
                height: 3px;
                margin-top: 0.45rem;
            }

            #languageDropdown {
                min-width: 112px;
                padding-inline: 0.55rem;
                font-size: 0.84rem;
            }

            #languageDropdown span {
                max-width: 58px;
            }

            #notificationDropdown {
                width: 40px;
                height: 40px;
            }

            .page-actions-group {
                gap: 0.4rem;
            }

            .page-actions-group .btn {
                flex: 1 1 auto;
                justify-content: center;
            }

            .card {
                border-radius: 18px !important;
            }

            .card-body {
                padding: 1rem;
            }

            table {
                display: block;
                overflow-x: auto;
                font-size: 0.875rem;
                -webkit-overflow-scrolling: touch;
            }

            .sidebar {
                width: 282px;
                max-width: 92vw;
            }

            .dropdown-menu {
                width: min(94vw, 340px) !important;
                max-width: min(94vw, 340px) !important;
            }
        }

        @media (max-width: 767.98px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                transition-duration: 0.001ms !important;
                animation-duration: 0.001ms !important;
                scroll-behavior: auto !important;
            }
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
                            <!-- <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.shipment-orders*') ? 'active' : '' }}"
                                href="{{ route('admin.shipment-orders') }}">
                                    <i class="fas fa-shipping-fast"></i>
                                    <span class="link-text">{{ __('admin-dashboard.shipment_orders') }}</span>
                                </a>
                            </li> -->

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.shipment-requests*') ? 'active' : '' }}"
                                href="{{ route('admin.shipment-requests.index') }}">
                                    <i class="fas fa-file-invoice"></i>
                                    <span class="link-text">{{ __('admin-dashboard.shipment_requests') }}</span>
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
                        @if($employee->can('admin.shipment-requests.index'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.shipment-requests*') ? 'active' : '' }}"
                                href="{{ route('admin.shipment-requests.index') }}">
                                        <i class="fas fa-file-invoice"></i>
                                        <span class="link-text">{{ __('admin-dashboard.shipment_requests') }}</span>
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
                                    @if($employee->can('admin.settings.governorates.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.governorates.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.governorates.index') }}">
                                                    <i class="fas fa-map-location-dot"></i>
                                                    <span class="link-text">{{ app()->getLocale() === 'ar' ? 'المحافظات' : 'Governorates' }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($employee->can('admin.settings.cities.index'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.settings.cities.*') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.cities.index') }}">
                                                    <i class="fas fa-city"></i>
                                                    <span class="link-text">{{ app()->getLocale() === 'ar' ? 'المدن' : 'Cities' }}</span>
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
