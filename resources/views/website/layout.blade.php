<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $site['description'] ?? 'MetwLogistic' }}">
    <title>{{ $pageTitle ?? 'MetwLogistic' }}</title>
    <style>
        :root {
            --orange: #ff6b16;
            --orange-dark: #e95604;
            --purple: #55217d;
            --purple-2: #7c37b4;
            --ink: #1d1830;
            --muted: #6d687c;
            --line: rgba(85, 33, 125, .12);
            --soft: #fff6f0;
            --white: #ffffff;
            --shadow: 0 28px 80px rgba(39, 18, 65, .14);
            --shadow-sm: 0 18px 48px rgba(39, 18, 65, .10);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top right, rgba(255,107,22,.14), transparent 30%),
                radial-gradient(circle at top left, rgba(85,33,125,.13), transparent 32%),
                #fffaf7;
            overflow-x: hidden;
        }
        a { color: inherit; text-decoration: none; }
        .container { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
        .pill {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 14px; border-radius: 999px;
            background: rgba(255,255,255,.74); border: 1px solid var(--line);
            color: var(--purple); font-weight: 700; font-size: 13px;
            box-shadow: 0 10px 30px rgba(85,33,125,.08);
        }
        .dot { width: 8px; height: 8px; background: var(--orange); border-radius: 50%; display: inline-block; }
        .site-header {
            position: sticky; top: 0; z-index: 50;
            backdrop-filter: blur(18px);
            background: rgba(255, 250, 247, .84);
            border-bottom: 1px solid rgba(85,33,125,.08);
        }
        .nav { height: 76px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .brand { display: inline-flex; align-items: center; gap: 12px; font-weight: 900; }
        .brand-mark {
            width: 44px; height: 44px; border-radius: 16px;
            background: linear-gradient(135deg, var(--orange), #ff934f 48%, var(--purple));
            display: grid; place-items: center; color: #fff; box-shadow: 0 15px 30px rgba(255,107,22,.24);
        }
        .brand-mark svg { width: 25px; height: 25px; }
        .brand-text strong { display: block; font-size: 18px; line-height: 1; color: var(--purple); }
        .brand-text span { display: block; font-size: 11px; color: var(--muted); margin-top: 5px; letter-spacing: .08em; }
        .nav-links { display: flex; align-items: center; gap: 22px; color: var(--muted); font-weight: 700; font-size: 14px; }
        .nav-links a:hover { color: var(--orange); }
        .nav-actions { display: flex; gap: 10px; align-items: center; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 9px;
            border-radius: 999px; padding: 13px 18px; font-weight: 800;
            border: 1px solid transparent; transition: .2s ease; cursor: pointer;
        }
        .btn-primary { background: var(--orange); color: #fff; box-shadow: 0 16px 34px rgba(255,107,22,.28); }
        .btn-primary:hover { transform: translateY(-2px); background: var(--orange-dark); }
        .btn-ghost { background: #fff; color: var(--purple); border-color: var(--line); }
        .btn-ghost:hover { transform: translateY(-2px); border-color: rgba(255,107,22,.4); color: var(--orange); }

        .hero { padding: 76px 0 44px; position: relative; }
        .hero-grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 46px; align-items: center; }
        .hero h1 { margin: 18px 0 18px; font-size: clamp(42px, 6vw, 78px); line-height: 1.05; letter-spacing: -2px; color: var(--purple); }
        .hero h1 span { color: var(--orange); }
        .hero p { color: var(--muted); font-size: 18px; line-height: 1.9; max-width: 650px; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }
        .hero-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 34px; }
        .metric { background: rgba(255,255,255,.75); border: 1px solid var(--line); border-radius: 22px; padding: 18px; box-shadow: var(--shadow-sm); }
        .metric strong { display: block; color: var(--purple); font-size: 24px; }
        .metric span { color: var(--muted); font-weight: 700; font-size: 13px; }

        .dashboard-preview { position: relative; min-height: 540px; }
        .orb { position: absolute; border-radius: 999px; filter: blur(0); opacity: .9; }
        .orb.one { width: 210px; height: 210px; background: rgba(255,107,22,.16); top: 20px; right: 20px; }
        .orb.two { width: 240px; height: 240px; background: rgba(85,33,125,.13); bottom: 20px; left: 12px; }
        .phone-card {
            position: absolute; inset: 26px auto auto 22px; width: 300px; min-height: 520px;
            background: #fff; border: 1px solid rgba(85,33,125,.13); border-radius: 38px; padding: 18px;
            box-shadow: var(--shadow); transform: rotate(-3deg);
        }
        .phone-screen { border-radius: 28px; overflow: hidden; background: linear-gradient(180deg, #fff, #fff6f0); min-height: 482px; border: 1px solid rgba(255,107,22,.16); }
        .phone-top { background: linear-gradient(135deg, var(--purple), var(--purple-2)); padding: 22px; color: #fff; }
        .search-bar { height: 42px; border-radius: 999px; background: #fff; color: var(--muted); display: flex; align-items: center; padding: 0 15px; margin-top: 16px; font-size: 13px; }
        .category-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; padding: 18px; }
        .mini-icon { background: #fff; border: 1px solid rgba(255,107,22,.15); border-radius: 16px; height: 58px; display: grid; place-items: center; color: var(--orange); font-weight: 900; box-shadow: 0 12px 26px rgba(255,107,22,.08); }
        .shipment-card { margin: 0 18px 14px; border-radius: 22px; padding: 16px; background: #fff; border: 1px solid var(--line); box-shadow: 0 12px 30px rgba(85,33,125,.08); }
        .shipment-card small { color: var(--muted); }
        .shipment-card strong { display: block; color: var(--purple); margin-top: 5px; }
        .floating-card {
            position: absolute; right: 10px; bottom: 70px; width: 260px; padding: 20px; border-radius: 28px;
            background: rgba(255,255,255,.92); border: 1px solid var(--line); box-shadow: var(--shadow); backdrop-filter: blur(20px);
        }
        .floating-card strong { color: var(--purple); font-size: 18px; }
        .progress { height: 9px; background: #f2e9f7; border-radius: 99px; overflow: hidden; margin: 14px 0; }
        .progress span { display: block; height: 100%; width: 72%; background: linear-gradient(90deg, var(--orange), var(--purple)); border-radius: 99px; }

        .section { padding: 78px 0; }
        .section-head { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
        .section h2 { color: var(--purple); font-size: clamp(30px, 4vw, 48px); line-height: 1.2; margin: 10px 0 0; }
        .section-sub { color: var(--muted); line-height: 1.9; max-width: 700px; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .card { background: rgba(255,255,255,.86); border: 1px solid var(--line); border-radius: 30px; padding: 26px; box-shadow: var(--shadow-sm); position: relative; overflow: hidden; }
        .card::before { content: ''; position: absolute; inset: 0 0 auto 0; height: 5px; background: linear-gradient(90deg, var(--orange), var(--purple)); opacity: .85; }
        .card-icon { width: 52px; height: 52px; border-radius: 18px; background: var(--soft); color: var(--orange); display: grid; place-items: center; font-size: 24px; margin-bottom: 18px; }
        .card h3 { color: var(--purple); margin: 0 0 12px; font-size: 21px; }
        .card p { color: var(--muted); line-height: 1.85; margin: 0; }
        .split { display: grid; grid-template-columns: .9fr 1.1fr; gap: 28px; align-items: center; }
        .visual-panel { min-height: 420px; border-radius: 36px; padding: 28px; background: linear-gradient(135deg, var(--purple), #321247); box-shadow: var(--shadow); color: #fff; position: relative; overflow: hidden; }
        .visual-panel::after { content: ''; position: absolute; width: 300px; height: 300px; border-radius: 50%; background: rgba(255,107,22,.26); bottom: -110px; left: -80px; }
        .timeline { display: grid; gap: 14px; position: relative; z-index: 2; }
        .timeline-item { display: flex; gap: 14px; align-items: flex-start; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); border-radius: 20px; padding: 16px; }
        .timeline-number { width: 32px; height: 32px; border-radius: 12px; background: var(--orange); display: grid; place-items: center; flex: 0 0 auto; font-weight: 900; }
        .timeline-item p { margin: 5px 0 0; color: rgba(255,255,255,.76); line-height: 1.7; }

        .apps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .app-card { border-radius: 30px; padding: 24px; color: #fff; min-height: 250px; position: relative; overflow: hidden; box-shadow: var(--shadow); }
        .app-card.metwzon { background: linear-gradient(135deg, #ff6b16, #9f3e10); }
        .app-card.express { background: linear-gradient(135deg, #55217d, #8e42cc); }
        .app-card.go { background: linear-gradient(135deg, #251238, #ff6b16); }
        .app-card::after { content: ''; position: absolute; width: 190px; height: 190px; border-radius: 50%; background: rgba(255,255,255,.16); left: -60px; bottom: -60px; }
        .app-card h3 { margin: 0; font-size: 27px; }
        .app-card p { color: rgba(255,255,255,.82); line-height: 1.8; position: relative; z-index: 2; }
        .store-row { display: flex; flex-wrap: wrap; gap: 10px; position: relative; z-index: 2; margin-top: 22px; }
        .store-badge { background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.22); border-radius: 14px; padding: 10px 12px; font-size: 13px; font-weight: 800; }

        .media-grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 18px; }
        .promo-image, .promo-video { min-height: 280px; border-radius: 32px; overflow: hidden; position: relative; box-shadow: var(--shadow-sm); border: 1px solid var(--line); background: #fff; }
        .promo-image { background: linear-gradient(135deg, rgba(255,107,22,.18), rgba(85,33,125,.18)), #fff; padding: 28px; }
        .promo-image h3, .promo-video h3 { color: var(--purple); margin: 0; font-size: 26px; position: relative; z-index: 2; }
        .promo-image p, .promo-video p { color: var(--muted); line-height: 1.8; position: relative; z-index: 2; max-width: 480px; }
        .promo-video { background: linear-gradient(135deg, #221331, #55217d); color: #fff; padding: 28px; }
        .promo-video h3 { color: #fff; }
        .promo-video p { color: rgba(255,255,255,.78); }
        .play { width: 72px; height: 72px; border-radius: 50%; display: grid; place-items: center; background: var(--orange); color: #fff; font-size: 28px; margin-top: 36px; box-shadow: 0 18px 38px rgba(255,107,22,.3); }

        .login-strip { background: linear-gradient(135deg, var(--purple), #321247); color: #fff; border-radius: 34px; padding: 28px; box-shadow: var(--shadow); display: flex; align-items: center; justify-content: space-between; gap: 18px; }
        .login-strip h2 { color: #fff; margin: 0 0 8px; font-size: 30px; }
        .login-strip p { margin: 0; color: rgba(255,255,255,.78); }
        .login-links { display: flex; flex-wrap: wrap; gap: 10px; }
        .login-links .btn { background: rgba(255,255,255,.12); color: #fff; border-color: rgba(255,255,255,.18); }
        .login-links .btn:hover { background: var(--orange); }

        .footer { padding: 40px 0; color: var(--muted); border-top: 1px solid var(--line); }
        .footer-grid { display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
        .footer-links { display: flex; gap: 18px; flex-wrap: wrap; font-weight: 700; }
        .footer-links a:hover { color: var(--orange); }
        .inner-hero { padding: 72px 0 34px; }
        .content-card { background: rgba(255,255,255,.88); border: 1px solid var(--line); border-radius: 34px; padding: 34px; box-shadow: var(--shadow-sm); line-height: 2; color: var(--muted); }
        .content-card h2, .content-card h3 { color: var(--purple); }
        .policy-list { display: grid; gap: 16px; }
        .policy-item { padding: 18px; border-radius: 22px; background: #fff; border: 1px solid var(--line); }

        @media (max-width: 980px) {
            .hero-grid, .split, .media-grid { grid-template-columns: 1fr; }
            .dashboard-preview { min-height: 500px; }
            .cards, .apps { grid-template-columns: 1fr 1fr; }
            .nav-links { display: none; }
            .login-strip { align-items: flex-start; flex-direction: column; }
        }
        @media (max-width: 640px) {
            .nav { height: auto; padding: 14px 0; align-items: flex-start; flex-direction: column; }
            .nav-actions { width: 100%; overflow-x: auto; padding-bottom: 4px; }
            .hero { padding-top: 38px; }
            .hero-metrics, .cards, .apps { grid-template-columns: 1fr; }
            .phone-card { position: relative; inset: auto; width: 100%; transform: none; }
            .floating-card { position: relative; right: auto; bottom: auto; width: 100%; margin-top: 16px; }
            .dashboard-preview { min-height: auto; }
            .section-head { display: block; }
            .btn { padding: 12px 14px; font-size: 13px; }
        }
    </style>
</head>
<body>
<header class="site-header">
    <div class="container nav">
        <a href="{{ route('website.home') }}" class="brand" aria-label="MetwLogistic Home">
            <span class="brand-mark">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 8.5 12 4l8 4.5v7L12 20l-8-4.5v-7Z" stroke="currentColor" stroke-width="1.8"/><path d="m4 8.5 8 4.5 8-4.5M12 13v7" stroke="currentColor" stroke-width="1.8"/></svg>
            </span>
            <span class="brand-text"><strong>ميتولوجيستيك</strong><span>MetwLogistic</span></span>
        </a>

        <nav class="nav-links" aria-label="Main navigation">
            <a href="{{ route('website.home') }}#services">الخدمات</a>
            <a href="{{ route('website.about') }}">نحن</a>
            <a href="{{ route('website.home') }}#apps">التطبيقات</a>
            <a href="{{ route('website.home') }}#media">المحتوى الدعائي</a>
            <a href="{{ route('website.policies') }}">السياسات والشروط</a>
        </nav>

        <div class="nav-actions">
            <a class="btn btn-ghost" href="{{ route('vendor.login') }}">دخول البائع</a>
            <a class="btn btn-primary" href="{{ route('admin.login') }}">دخول الأدمن</a>
        </div>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="footer">
    <div class="container footer-grid">
        <div class="brand">
            <span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 8.5 12 4l8 4.5v7L12 20l-8-4.5v-7Z" stroke="currentColor" stroke-width="1.8"/><path d="m4 8.5 8 4.5 8-4.5M12 13v7" stroke="currentColor" stroke-width="1.8"/></svg></span>
            <span class="brand-text"><strong>ميتولوجيستيك</strong><span>MetwLogistic</span></span>
        </div>
        <div class="footer-links">
            <a href="{{ route('website.about') }}">نحن</a>
            <a href="{{ route('website.policies') }}">السياسات والشروط</a>
            <a href="{{ route('vendor.register') }}">تسجيل بائع</a>
            <a href="{{ route('shipment.register') }}">تسجيل مستودع شحن</a>
        </div>
        <div>© {{ date('Y') }} MetwLogistic. جميع الحقوق محفوظة.</div>
    </div>
</footer>
</body>
</html>
