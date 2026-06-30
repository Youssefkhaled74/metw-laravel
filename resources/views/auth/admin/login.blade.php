@extends('layouts.admin')

@section('title', 'Admin Login')
@section('page-title', 'Admin Login')

@push('styles')
    <style>
        .admin-auth-page {
            width: min(1120px, calc(100vw - 32px));
            margin: auto;
            padding: 24px 0;
        }

        .admin-auth-shell {
            position: relative;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            min-height: 650px;
            overflow: hidden;
            border: 1px solid rgba(236, 230, 242, 0.95);
            border-radius: 34px;
            background: rgba(255, 255, 255, 0.82);
            box-shadow: 0 26px 70px rgba(31, 18, 53, 0.16);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
        }

        .admin-auth-shell::before,
        .admin-auth-shell::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            filter: blur(8px);
        }

        .admin-auth-shell::before {
            width: 260px;
            height: 260px;
            top: -96px;
            inset-inline-start: -90px;
            background: rgba(255, 112, 67, 0.18);
        }

        .admin-auth-shell::after {
            width: 320px;
            height: 320px;
            bottom: -140px;
            inset-inline-end: -120px;
            background: rgba(123, 0, 168, 0.15);
        }

        .admin-auth-hero {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(32px, 4vw, 54px);
            color: #FFFFFF;
            background:
                radial-gradient(circle at 15% 12%, rgba(255, 112, 67, 0.42), transparent 18rem),
                radial-gradient(circle at 85% 75%, rgba(255, 255, 255, 0.12), transparent 18rem),
                linear-gradient(150deg, #1F1235 0%, #4C0078 54%, #7B00A8 100%);
        }

        .admin-auth-brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .admin-auth-logo-wrap {
            width: 64px;
            height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.16);
        }

        .admin-auth-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .admin-auth-brand-text span {
            display: block;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.68);
        }

        .admin-auth-brand-text strong {
            display: block;
            margin-top: 3px;
            font-size: 1.35rem;
            font-weight: 900;
            line-height: 1;
            color: #FFFFFF;
        }

        .admin-auth-hero-content {
            max-width: 470px;
            margin-top: 54px;
        }

        .admin-auth-badge {
            width: fit-content;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 18px;
            padding: 9px 14px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.10);
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.82rem;
            font-weight: 800;
        }

        .admin-auth-hero h1 {
            margin: 0;
            font-size: clamp(2.15rem, 4vw, 4.15rem);
            font-weight: 950;
            line-height: 0.98;
            letter-spacing: -0.06em;
        }

        html[dir="rtl"] .admin-auth-hero h1 {
            letter-spacing: 0;
        }

        .admin-auth-hero p {
            max-width: 430px;
            margin: 20px 0 0;
            color: rgba(255, 255, 255, 0.76);
            font-size: 1rem;
            line-height: 1.85;
        }

        .admin-auth-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 34px;
        }

        .admin-auth-stat {
            padding: 16px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.10);
        }

        .admin-auth-stat i {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            border-radius: 13px;
            background: rgba(255, 112, 67, 0.22);
            color: #FFB199;
        }

        .admin-auth-stat strong {
            display: block;
            color: #FFFFFF;
            font-size: 0.95rem;
            font-weight: 900;
        }

        .admin-auth-stat span {
            display: block;
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.58);
            font-size: 0.76rem;
            font-weight: 700;
        }

        .admin-auth-footer-note {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 38px;
            color: rgba(255, 255, 255, 0.68);
            font-size: 0.84rem;
            font-weight: 700;
        }

        .admin-auth-form-panel {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(28px, 4vw, 54px);
            background:
                radial-gradient(circle at 100% 0%, rgba(255, 112, 67, 0.13), transparent 18rem),
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.88));
        }

        .admin-login-card {
            width: 100%;
            max-width: 430px;
        }

        .admin-login-card-header {
            margin-bottom: 28px;
        }

        .admin-login-card-header .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #FFF1EC;
            color: #F45B2E;
            font-size: 0.78rem;
            font-weight: 900;
        }

        .admin-login-card-header h2 {
            margin: 0;
            color: #181022;
            font-size: clamp(1.85rem, 3vw, 2.5rem);
            font-weight: 950;
            letter-spacing: -0.045em;
        }

        html[dir="rtl"] .admin-login-card-header h2 {
            letter-spacing: 0;
        }

        .admin-login-card-header p {
            margin: 10px 0 0;
            color: #7C7285;
            line-height: 1.75;
            font-weight: 650;
        }

        .admin-auth-alert {
            margin-bottom: 18px;
            border: 0;
            border-radius: 18px;
            padding: 14px 16px;
            font-weight: 750;
            box-shadow: 0 8px 20px rgba(36, 23, 46, 0.06);
        }

        .admin-auth-field {
            margin-bottom: 18px;
        }

        .admin-auth-field label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 9px;
            color: #24172E;
            font-size: 0.88rem;
            font-weight: 900;
        }

        .admin-auth-input-wrap {
            position: relative;
        }

        .admin-auth-input-icon {
            position: absolute;
            top: 50%;
            inset-inline-start: 16px;
            transform: translateY(-50%);
            z-index: 2;
            color: #7B00A8;
            opacity: 0.7;
        }

        .admin-auth-input {
            min-height: 56px;
            padding-inline-start: 46px;
            padding-inline-end: 46px;
            border: 1px solid #ECE6F2;
            border-radius: 18px;
            background: #FFFFFF;
            color: #24172E;
            font-weight: 750;
            box-shadow: 0 8px 22px rgba(36, 23, 46, 0.045);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .admin-auth-input:focus {
            border-color: rgba(123, 0, 168, 0.42);
            box-shadow: 0 0 0 4px rgba(123, 0, 168, 0.09), 0 12px 26px rgba(36, 23, 46, 0.07);
            transform: translateY(-1px);
        }

        .admin-auth-input.is-invalid {
            border-color: #FF4B2E;
            padding-inline-end: 46px;
        }

        .admin-password-toggle {
            position: absolute;
            top: 50%;
            inset-inline-end: 10px;
            z-index: 4;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transform: translateY(-50%);
            border: 0;
            border-radius: 13px;
            background: transparent;
            color: #7C7285;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .admin-password-toggle:hover {
            background: #F5EBFF;
            color: #7B00A8;
        }

        .admin-auth-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 4px 0 22px;
        }

        .admin-auth-options .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: auto;
            margin: 0;
            padding: 0;
        }

        .admin-auth-options .form-check-input {
            float: none;
            margin: 0;
            width: 18px;
            height: 18px;
            border-color: #D8CDDF;
            cursor: pointer;
        }

        .admin-auth-options .form-check-input:checked {
            border-color: #7B00A8;
            background-color: #7B00A8;
        }

        .admin-auth-options .form-check-label {
            color: #7C7285;
            font-size: 0.88rem;
            font-weight: 800;
            cursor: pointer;
        }

        .admin-auth-help {
            color: #7B00A8;
            font-size: 0.84rem;
            font-weight: 900;
        }

        .admin-auth-submit {
            min-height: 58px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            border: 0;
            border-radius: 19px;
            background: linear-gradient(135deg, #FF7043 0%, #7B00A8 100%);
            color: #FFFFFF;
            font-weight: 950;
            box-shadow: 0 18px 34px rgba(123, 0, 168, 0.22), 0 12px 28px rgba(255, 112, 67, 0.22);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .admin-auth-submit:hover {
            color: #FFFFFF;
            transform: translateY(-2px);
            filter: saturate(1.05);
            box-shadow: 0 22px 42px rgba(123, 0, 168, 0.26), 0 14px 32px rgba(255, 112, 67, 0.25);
        }

        .admin-auth-submit:active {
            transform: translateY(0);
        }

        .admin-auth-security {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 22px;
            padding: 15px;
            border: 1px solid #ECE6F2;
            border-radius: 18px;
            background: #FBFAFD;
        }

        .admin-auth-security i {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #F5EBFF;
            color: #7B00A8;
        }

        .admin-auth-security strong {
            display: block;
            color: #24172E;
            font-size: 0.84rem;
            font-weight: 950;
        }

        .admin-auth-security span {
            display: block;
            margin-top: 3px;
            color: #7C7285;
            font-size: 0.78rem;
            line-height: 1.55;
            font-weight: 650;
        }

        @media (max-width: 991.98px) {
            .admin-auth-page {
                width: min(680px, calc(100vw - 24px));
            }

            .admin-auth-shell {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .admin-auth-hero {
                padding: 30px;
            }

            .admin-auth-hero-content {
                margin-top: 38px;
            }

            .admin-auth-stats {
                grid-template-columns: 1fr;
            }

            .admin-auth-footer-note {
                margin-top: 28px;
            }
        }

        @media (max-width: 575.98px) {
            .main-content.no-sidebar {
                align-items: flex-start;
                padding-top: 16px !important;
                padding-bottom: 16px !important;
            }

            .admin-auth-page {
                width: calc(100vw - 18px);
                padding: 0;
            }

            .admin-auth-shell {
                border-radius: 24px;
            }

            .admin-auth-hero,
            .admin-auth-form-panel {
                padding: 24px 18px;
            }

            .admin-auth-logo-wrap {
                width: 56px;
                height: 56px;
                border-radius: 18px;
            }

            .admin-auth-logo {
                width: 42px;
                height: 42px;
            }

            .admin-auth-hero h1 {
                font-size: 2.2rem;
            }

            .admin-auth-options {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
    <section class="admin-auth-page">
        <div class="admin-auth-shell">

            <aside class="admin-auth-hero">
                <div>
                    <div class="admin-auth-brand">
                        <div class="admin-auth-logo-wrap">
                            <img
                                src="{{ asset('images/flags/logo.png') }}"
                                alt="{{ config('app.name', 'MetwGo') }} Logo"
                                class="admin-auth-logo"
                            >
                        </div>

                        <div class="admin-auth-brand-text">
                            <span>Admin Panel</span>
                            <strong>{{ config('app.name', 'MetwGo') }}</strong>
                        </div>
                    </div>

                    <div class="admin-auth-hero-content">
                        <div class="admin-auth-badge">
                            <i class="fas fa-shield-halved"></i>
                            Secure Admin Access
                        </div>

                        <h1>Control your operations with confidence.</h1>

                        <p>
                            Login to manage vendors, products, orders, shipments, employees,
                            permissions, commissions, and system settings from one professional workspace.
                        </p>

                        <div class="admin-auth-stats">
                            <div class="admin-auth-stat">
                                <i class="fas fa-boxes-stacked"></i>
                                <strong>Orders</strong>
                                <span>Track operations</span>
                            </div>

                            <div class="admin-auth-stat">
                                <i class="fas fa-store"></i>
                                <strong>Vendors</strong>
                                <span>Manage partners</span>
                            </div>

                            <div class="admin-auth-stat">
                                <i class="fas fa-user-shield"></i>
                                <strong>Roles</strong>
                                <span>Control access</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-auth-footer-note">
                    <i class="fas fa-lock"></i>
                    <span>Protected dashboard for authorized administrators only.</span>
                </div>
            </aside>

            <div class="admin-auth-form-panel">
                <div class="admin-login-card">
                    <div class="admin-login-card-header">
                        <div class="eyebrow">
                            <i class="fas fa-crown"></i>
                            Admin Login
                        </div>

                        <h2>Welcome back</h2>

                        <p>
                            Enter your admin credentials to continue to the dashboard.
                        </p>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success admin-auth-alert" role="alert">
                            <i class="fas fa-circle-check me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger admin-auth-alert" role="alert">
                            <i class="fas fa-triangle-exclamation me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger admin-auth-alert" role="alert">
                            <i class="fas fa-triangle-exclamation me-2"></i>
                            Please check your login details and try again.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login') }}" novalidate>
                        @csrf

                        <div class="admin-auth-field">
                            <label for="username">
                                <span>Username</span>
                            </label>

                            <div class="admin-auth-input-wrap">
                                <i class="fas fa-user admin-auth-input-icon"></i>

                                <input
                                    type="text"
                                    class="form-control admin-auth-input @error('username') is-invalid @enderror"
                                    id="username"
                                    name="username"
                                    value="{{ old('username') }}"
                                    placeholder="Enter your username"
                                    autocomplete="username"
                                    required
                                    autofocus
                                >

                                @error('username')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="admin-auth-field">
                            <label for="password">
                                <span>Password</span>
                            </label>

                            <div class="admin-auth-input-wrap">
                                <i class="fas fa-key admin-auth-input-icon"></i>

                                <input
                                    type="password"
                                    class="form-control admin-auth-input @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="admin-password-toggle"
                                    id="toggleAdminPassword"
                                    aria-label="Show password"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>

                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="admin-auth-options">
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="remember"
                                    name="remember"
                                    {{ old('remember') ? 'checked' : '' }}
                                >

                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <span class="admin-auth-help">
                                <i class="fas fa-circle-info me-1"></i>
                                Admin only
                            </span>
                        </div>

                        <button type="submit" class="btn admin-auth-submit">
                            <span>Login to Dashboard</span>
                            <i class="fas fa-arrow-right-to-bracket"></i>
                        </button>
                    </form>

                    <div class="admin-auth-security">
                        <i class="fas fa-fingerprint"></i>

                        <div>
                            <strong>Secure session</strong>
                            <span>
                                Your access is validated through the admin guard and protected by CSRF security.
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('toggleAdminPassword');
            const passwordInput = document.getElementById('password');

            if (!toggleButton || !passwordInput) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const icon = toggleButton.querySelector('i');
                const isPassword = passwordInput.getAttribute('type') === 'password';

                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleButton.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');

                if (icon) {
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                }
            });
        });
    </script>
@endsection