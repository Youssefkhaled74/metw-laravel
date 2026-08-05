@extends('website.layout')

@section('title', 'تسجيل الدخول - المستودعات | ميتو')

@push('styles')
<style>
    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 85vh;
        background: linear-gradient(135deg, #8c2d93 0%, #c7356d 100%);
        padding: 20px;
    }
    .login-container {
        display: flex;
        background: transparent;
        width: 100%;
        max-width: 1100px;
        gap: 30px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }
    .login-video-section { flex: 1.2; min-width: 300px; display: flex; flex-direction: column; gap: 15px; }
    .main-video-box {
        width: 100%; aspect-ratio: 16/9; background-color: #3b1855;
        border-radius: 20px; position: relative; box-shadow: 0 15px 30px rgba(0,0,0,0.3);
    }
    .video-box-badge { position: absolute; top: 20px; right: 20px; color: #fff; font-size: 22px; font-weight: bold; }
    .video-play-btn {
        position: absolute; bottom: 20px; right: 20px; width: 55px; height: 55px;
        background-color: #f57c22; border-radius: 50%; display: flex; justify-content: center; align-items: center;
        cursor: pointer; box-shadow: 0 5px 10px rgba(245, 124, 34, 0.4);
    }
    .video-play-btn::after {
        content: ''; display: block; margin-left: 4px; border-style: solid;
        border-width: 10px 0 10px 15px; border-color: transparent transparent transparent #ffffff;
    }
    .thumb-video-row { display: flex; gap: 15px; justify-content: flex-start; }
    .thumb-video-box { flex: 1; aspect-ratio: 16/9; background-color: #3b1855; border-radius: 12px; position: relative; max-width: 200px; }
    .thumb-video-box .video-box-badge { top: 8px; right: 10px; font-size: 12px; }
    .thumb-video-box .video-play-btn { width: 30px; height: 30px; bottom: 10px; right: 10px; }
    .thumb-video-box .video-play-btn::after { border-width: 6px 0 6px 10px; margin-left: 2px; }
    .login-card-wrapper { flex: 0.8; min-width: 350px; display: flex; justify-content: flex-end; }
    .login-card { background: #fff; border-radius: 20px; width: 100%; max-width: 380px; box-shadow: 0 15px 40px rgba(0,0,0,0.25); overflow: hidden; }
    .login-card-header { background: linear-gradient(135deg, #a72baf 0%, #f57c22 100%); padding: 30px 20px; text-align: center; color: #fff; }
    .login-card-header svg { width: 50px; height: 50px; fill: #fff; margin-bottom: 10px; }
    .login-card-header h2 { font-size: 22px; font-weight: 700; margin-bottom: 5px; }
    .login-card-header p { font-size: 14px; opacity: 0.9; margin: 0; }
    .login-card-body { padding: 30px 25px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 5px; text-align: right; }
    .input-wrapper { display: flex; align-items: center; background-color: #ebf2f9; border-radius: 8px; padding: 0 15px; }
    .input-wrapper svg { width: 20px; height: 20px; fill: #777; }
    .input-wrapper input { width: 100%; padding: 14px 10px; border: none; background: transparent; outline: none; font-size: 15px; font-family: inherit; text-align: right; }
    .form-check { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; justify-content: flex-end; }
    .form-check input { accent-color: #f57c22; width: 18px; height: 18px; }
    .form-check label { font-size: 14px; color: #333; }
    .btn-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, #f57c22 0%, #e46b12 100%); color: #fff; border: none; border-radius: 8px; font-size: 18px; font-weight: 700; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; display: flex; justify-content: center; align-items: center; gap: 8px; font-family: inherit; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(245, 124, 34, 0.4); }
    .forgot-pass { text-align: center; margin-top: 20px; }
    .forgot-pass a { color: #777; font-size: 14px; text-decoration: none; }
    .forgot-pass a:hover { text-decoration: underline; }
    @media (max-width: 800px) { .login-card-wrapper { justify-content: center; } .thumb-video-box { max-width: 120px; } }
</style>
@endpush

@section('content')
<div class="login-wrapper">
    <div class="login-container">
        {{-- Left Side: Video Section --}}
        <div class="login-video-section">
            <div class="main-video-box">
                <div class="video-box-badge">فيديو</div>
                <div class="video-play-btn"></div>
            </div>
            <div class="thumb-video-row">
                <div class="thumb-video-box"><div class="video-box-badge">فيديو 1</div><div class="video-play-btn"></div></div>
                <div class="thumb-video-box"><div class="video-box-badge">فيديو 2</div><div class="video-play-btn"></div></div>
                <div class="thumb-video-box"><div class="video-box-badge">فيديو 3</div><div class="video-play-btn"></div></div>
            </div>
        </div>

        {{-- Right Side: Login Card --}}
        <div class="login-card-wrapper">
            <div class="login-card">
                <div class="login-card-header">
                    <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                    <h2>تسجيل الدخول</h2>
                    {{-- Dynamically prints "المستودعات" based on the $type passed from the controller --}}
                    <p>Metw - {{ $type === 'shipment' ? 'المستودعات' : 'Shipment' }}</p>
                </div>
                
                <div class="login-card-body">
                    {{-- Points exactly to the POST route defined in your web.php --}}
                    <form method="POST" action="{{ route('shipment.login') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label>البريد الإلكتروني</label>
                            <div class="input-wrapper">
                                <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                {{-- Matches the $request->validate(['email' => ...]) in your controller --}}
                                <input type="email" name="email" placeholder="" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>كلمة المرور</label>
                            <div class="input-wrapper">
                                <svg viewBox="0 0 24 24"><path d="M12 17c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm6-9h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10z"/></svg>
                                <input type="password" name="password" placeholder="" required>
                            </div>
                        </div>

                        <div class="form-check">
                            <label for="remember">تذكرني</label>
                            <input type="checkbox" id="remember" name="remember">
                        </div>

                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;transform:rotate(180deg);"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                            تسجيل الدخول
                        </button>

                        <div class="forgot-pass">
                            <a href="#">نسيت كلمة المرور ؟</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection