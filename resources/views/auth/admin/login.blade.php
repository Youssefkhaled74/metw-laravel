@extends('website.layout')

@section('title', 'تسجيل دخول الإدارة')

@push('styles')
<style>
    .admin-login-page {
        padding: 28px 0 70px;
        background: linear-gradient(180deg, #fff 0%, #fff1f2 100%);
    }

    .admin-login-shell {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(360px, .8fr);
        gap: 34px;
        align-items: start;
    }

    .video-panel {
        display: grid;
        gap: 18px;
    }

    .main-video {
        min-height: 370px;
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 30% 30%, rgba(255,255,255,.12), transparent 16rem),
            linear-gradient(135deg, #3b0d5a 0%, #4f1f7a 45%, #8f2f73 100%);
        box-shadow: 0 18px 40px rgba(77, 16, 104, .25);
        border: 2px solid rgba(255,255,255,.18);
    }

    .main-video::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,.02), rgba(0,0,0,.06));
        pointer-events: none;
    }

    .video-label {
        position: absolute;
        top: 26px;
        right: 28px;
        color: #fff;
        font-size: 26px;
        font-weight: 900;
        text-shadow: 0 2px 6px rgba(0,0,0,.2);
        z-index: 1;
    }

    .play-btn {
        position: absolute;
        right: 72px;
        top: 50%;
        transform: translateY(-50%);
        width: 74px;
        height: 74px;
        border-radius: 50%;
        background: #ff7a21;
        box-shadow: 0 14px 28px rgba(255, 122, 33, .38);
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .play-btn::after {
        content: '';
        display: block;
        margin-left: 7px;
        border-style: solid;
        border-width: 15px 0 15px 24px;
        border-color: transparent transparent transparent #fff;
    }

    .thumb-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .thumb-card {
        min-height: 104px;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #3d0f5c 0%, #5b1d7d 60%, #7b1fa2 100%);
        border: 2px solid rgba(255,255,255,.14);
        box-shadow: 0 12px 22px rgba(77, 16, 104, .18);
    }

    .thumb-card .thumb-label {
        position: absolute;
        top: 8px;
        right: 10px;
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        z-index: 1;
    }

    .thumb-card .thumb-play {
        position: absolute;
        right: 12px;
        bottom: 12px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #ff7a21;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .thumb-card .thumb-play::after {
        content: '';
        display: block;
        margin-left: 3px;
        border-style: solid;
        border-width: 6px 0 6px 10px;
        border-color: transparent transparent transparent #fff;
    }

    .login-box {
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 42px rgba(16, 24, 40, .15);
        background: #fff;
    }

    .login-head {
        padding: 28px 24px 26px;
        background: linear-gradient(135deg, #8d15ab 0%, #ff6f3c 100%);
        text-align: center;
        color: #fff;
    }

    .login-head .shield {
        width: 56px;
        height: 56px;
        margin: 0 auto 10px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.12);
        font-size: 24px;
    }

    .login-head h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 900;
        line-height: 1.1;
    }

    .login-head .sub {
        margin-top: 6px;
        font-weight: 800;
        opacity: .92;
    }

    .login-body {
        padding: 26px 24px 22px;
    }

    .login-alert {
        border: 0;
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 14px;
        font-weight: 700;
    }

    .field {
        margin-bottom: 16px;
    }

    .field label {
        display: block;
        margin-bottom: 6px;
        font-size: 15px;
        font-weight: 800;
        color: #444;
        text-align: right;
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        inset-inline-start: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #555;
        z-index: 2;
    }

    .login-input {
        min-height: 46px;
        border-radius: 10px;
        background: #dfeaf8;
        border: 1px solid #f4cdbb;
        padding-inline-start: 38px;
        padding-inline-end: 42px;
        font-weight: 700;
        box-shadow: none;
    }

    .login-input:focus {
        background: #dfeaf8;
        border-color: #ffb58f;
        box-shadow: 0 0 0 3px rgba(255, 111, 60, .12);
    }

    .toggle-pass {
        position: absolute;
        top: 50%;
        inset-inline-end: 8px;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: #555;
        z-index: 2;
    }

    .toggle-pass:hover {
        background: rgba(255,255,255,.45);
    }

    .remember-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        font-weight: 700;
        color: #555;
    }

    .submit-btn {
        width: 100%;
        min-height: 44px;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #ff7f3a 0%, #ff6a2d 100%);
        color: #fff;
        font-weight: 900;
        box-shadow: 0 10px 20px rgba(255, 111, 60, .25);
    }

    .forgot-link {
        display: block;
        margin-top: 8px;
        text-align: center;
        color: #555;
        font-weight: 700;
    }

    @media (max-width: 991px) {
        .admin-login-shell {
            grid-template-columns: 1fr;
        }

        .main-video {
            min-height: 280px;
        }
    }
</style>
@endpush

@section('content')
<section class="admin-login-page">
    <div class="container">
        <div class="admin-login-shell">
            <div class="video-panel">
                <div class="main-video">
                    <div class="video-label">فيديو</div>
                    <div class="play-btn" aria-hidden="true"></div>
                </div>

                <div class="thumb-row">
                    <div class="thumb-card"><div class="thumb-label">فيديو</div><div class="thumb-play"></div></div>
                    <div class="thumb-card"><div class="thumb-label">فيديو</div><div class="thumb-play"></div></div>
                    <div class="thumb-card"><div class="thumb-label">فيديو</div><div class="thumb-play"></div></div>
                </div>
            </div>

            <div class="login-box">
                <div class="login-head">
                    <div class="shield"><i class="fas fa-shield-halved"></i></div>
                    <h2>تسجيل الدخول</h2>
                    <div class="sub">Metw - Admin</div>
                </div>

                <div class="login-body">
                    @if ($errors->any())
                        <div class="alert alert-danger login-alert" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <div class="field">
                        <label for="username">رقم الموبايل / البريد الإلكتروني</label>
                            <div class="input-wrap">
                                <i class="fas fa-mobile-screen input-icon"></i>
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    value="{{ old('username') }}"
                                    class="form-control login-input @error('username') is-invalid @enderror"
                                    autocomplete="username"
                                    required
                                >
                            </div>
                            @error('username')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="password">كلمة المرور</label>
                            <div class="input-wrap">
                                <i class="fas fa-eye input-icon"></i>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control login-input @error('password') is-invalid @enderror"
                                    autocomplete="current-password"
                                    required
                                >
                                <button type="button" class="toggle-pass" id="togglePassword" aria-label="إظهار كلمة المرور">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="remember-row">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>تذكرني</span>
                        </label>

                        <button type="submit" class="btn submit-btn">
                            <i class="fas fa-right-to-bracket me-1"></i>
                            تسجيل الدخول
                        </button>

                        <a href="#" class="forgot-link">نسيت كلمة المرور ؟</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (!togglePassword || !passwordInput) {
            return;
        }

        togglePassword.addEventListener('click', function () {
            const icon = togglePassword.querySelector('i');
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            if (icon) {
                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);
            }
        });
    });
</script>
@endpush
