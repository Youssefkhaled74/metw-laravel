<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metw - @yield('title', 'Home')</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* CSS Reset & Global Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #fcfbfa;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        ul {
            list-style: none;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Colors */
        :root {
            --orange: #f6891f;
            --dark-purple: #3c1f64;
            --bg-light-orange: #fcf4ea;
            --text-dark: #222;
        }

        /* --- Fixed Header Styling --- */
        header {
            background: #fff;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 32px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .logo .orange {
            color: var(--orange);
        }

        .logo .purple {
            color: var(--dark-purple);
        }

        /* --- Top Links: One line with Arrows --- */
        .top-links-wrapper {
            display: flex;
            align-items: center;
            flex: 1;
            gap: 8px;
            min-width: 0;
        }

        .top-links {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            flex: 1;
            flex-wrap: nowrap;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 0 5px;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .top-links::-webkit-scrollbar {
            display: none;
        }

        .top-links a {
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid transparent;
            background: #f8f9fa;
            color: #555;
            transition: 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .top-links a.active {
            border-color: var(--orange);
            background: transparent;
            color: var(--orange);
        }

        .top-links a.lang-toggle {
            background: #eaf5e8;
            color: #2a7e4c;
            padding: 4px 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* --- Shared Arrow Button Styling --- */
        .nav-btn {
            background: #fff;
            border: 1px solid #eee;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: 0.2s;
            z-index: 2;
        }

        .nav-btn:hover {
            background: var(--bg-light-orange);
            border-color: var(--orange);
        }

        .nav-btn svg {
            fill: var(--orange);
            width: 18px;
            height: 18px;
        }

        /* --- Sub Navigation Wrapper (With Arrows) --- */
        .sub-nav-wrapper {
            background-color: var(--bg-light-orange);
            padding: 12px 20px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
        }

        .sub-nav-inner {
            display: flex;
            gap: 20px;
            align-items: center;
            flex: 1;
            flex-wrap: nowrap;
            overflow-x: auto;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .sub-nav-inner::-webkit-scrollbar {
            display: none;
        }

        .sub-nav-inner a {
            white-space: nowrap;
            flex-shrink: 0;
            color: var(--orange);
            font-weight: 700;
            font-size: 15px;
        }

        .sub-nav-inner a:hover {
            color: #cc6f13;
        }

        /* Main Content Styling */
        .hero-section {
            display: flex;
            gap: 40px;
            padding: 60px 20px;
            align-items: center;
        }

        .hero-text {
            flex: 1;
            text-align: right;
        }

        .hero-text h1 {
            font-size: 40px;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .hero-text h2 {
            font-size: 28px;
            color: #444;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .hero-text p {
            line-height: 1.8;
            color: #666;
            font-size: 18px;
            max-width: 600px;
        }

        .hero-video-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .main-video {
            width: 100%;
            max-width: 500px;
            aspect-ratio: 16/9.5;
            background-color: var(--dark-purple);
            border-radius: 16px;
            position: relative;
            box-shadow: 0 10px 25px rgba(60, 31, 100, 0.2);
        }

        .video-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
        }

        .play-btn {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 55px;
            height: 55px;
            background-color: var(--orange);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .play-btn::after {
            content: '';
            display: block;
            margin-left: 4px;
            border-style: solid;
            border-width: 10px 0 10px 16px;
            border-color: transparent transparent transparent #fff;
        }

        .carousel-section {
            padding: 0 20px 40px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .carousel-arrow {
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            flex-shrink: 0;
        }

        .carousel-arrow svg {
            fill: var(--orange);
            width: 100%;
            height: 100%;
        }

        .carousel-items {
            display: flex;
            flex: 1;
            gap: 15px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 0 10px;
        }

        .carousel-items::-webkit-scrollbar {
            display: none;
        }

        .thumb-video {
            flex: 0 0 15%;
            min-width: 140px;
            aspect-ratio: 16/10;
            background-color: var(--dark-purple);
            border-radius: 10px;
            position: relative;
        }

        .thumb-video .video-badge {
            top: 8px;
            right: 10px;
            font-size: 12px;
        }

        .thumb-video .play-btn {
            width: 30px;
            height: 30px;
            bottom: 8px;
            right: 10px;
        }

        .thumb-video .play-btn::after {
            border-width: 6px 0 6px 10px;
            margin-left: 3px;
        }

        @media (max-width: 900px) {
            .hero-section {
                flex-direction: column-reverse;
                text-align: center;
            }

            .hero-text {
                text-align: center;
            }

            .hero-text p {
                max-width: 100%;
            }

            .sub-nav-wrapper {
                justify-content: flex-start;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <header>
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <span class="orange">metw</span>
                    <span class="purple">ميتو</span>
                </div>

                {{-- TOP LINKS WITH ARROWS --}}
                <div class="top-links-wrapper">
                    <button class="nav-btn" onclick="scrollTopNav(-150)" aria-label="Scroll top menu left">
                        <svg viewBox="0 0 24 24">
                            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                        </svg>
                    </button>

                    <div class="top-links" id="topLinksContainer">
                        <a href="#" class="lang-toggle"><span class="globe-icon">🌐</span> العربية</a>
                        
                        {{-- Updated Admin Link --}}
                        <a href="{{ route('admin.login') }}" class="{{ request()->routeIs('admin.login') ? 'active' : '' }}">إدارة ميتو</a>
                        
                        {{-- Updated Vendor Link --}}
                        <a href="{{ route('vendor.login') }}" class="{{ request()->routeIs('vendor.login') ? 'active' : '' }}">دخول المتاجر</a>
                        
                        {{-- Updated Shipment/Warehouse Link --}}
                        <a href="{{ route('shipment.login') }}" class="{{ request()->routeIs('shipment.login') ? 'active' : '' }}">دخول المستودعات</a>

                        <a href="{{ route('website.contact') }}" class="{{ request()->routeIs('website.contact') ? 'active' : '' }}">تواصل معنا</a>
                        <a href="{{ route('website.about') }}" class="{{ request()->routeIs('website.about') ? 'active' : '' }}">نحن</a>
                        <a href="{{ route('website.policies.list') }}" class="{{ request()->routeIs('website.policies.list') ? 'active' : '' }}">السياسات والشروط</a>
                        <a href="{{ route('website.apps') }}" class="{{ request()->routeIs('website.apps') ? 'active' : '' }}">تطبيقات ميتو</a>
                        <a href="{{ route('website.metwservices') }}" class="{{ request()->routeIs('website.metwservices') ? 'active' : '' }}">خدمات ميتو</a>
                        <a href="{{ route('website.content') }}" class="{{ request()->routeIs('website.content') ? 'active' : '' }}">منشورات الموقع</a>
                        <a href="{{ route('website.images') }}">صور الموقع</a>
                        <a href="{{ route('website.videos') }}" class="{{ request()->routeIs('website.videos') ? 'active' : '' }}">فيديوهات الموقع</a>
                    </div>

                    <button class="nav-btn" onclick="scrollTopNav(150)" aria-label="Scroll top menu right">
                        <svg viewBox="0 0 24 24">
                            <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z" />
                        </svg>
                    </button>
                </div>

            </div>

            {{-- SUB NAV (BEIGE) WITH ARROWS --}}
            <nav class="sub-nav-wrapper">
                <button class="nav-btn" onclick="scrollSubNav(-150)" aria-label="Scroll sub menu left">
                    <svg viewBox="0 0 24 24">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                    </svg>
                </button>

                <div class="sub-nav-inner" id="subNavContainer">
                    <a href="#">منصة ميتو</a>
                    <a href="#">للتجارة والخدمات اللوجستية</a>
                    <a href="#">ماركت إلكتروني مالي ستور</a>
                    <a href="#">خدمات التخزين للتجار</a>
                    <a href="#">خدمات شحن بين محافظات ومدن وقرى مصر</a>
                    <a href="#">دليفري سريع وآمن</a>
                    <a href="#">فوالف المندوبين من الباب للباب</a>
                </div>

                <button class="nav-btn" onclick="scrollSubNav(150)" aria-label="Scroll sub menu right">
                    <svg viewBox="0 0 24 24">
                        <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z" />
                    </svg>
                </button>
            </nav>
        </div>
    </header>

    <main>
        {{-- Hero & Carousel will ONLY appear if the user is on the Homepage --}}
        @if(request()->routeIs('home'))
        <div class="hero-section">
            <div class="hero-text">
                <h1>منصة <span style="color: var(--orange);">metw</span> للتجارة الإلكترونية</h1>
                <h2>خدمات شحن وتوصيل سريع وآمن</h2>
                <p>منصة متكاملة تربط التجار والمستودعات والعملاء بأفضل خدمات الشحن واللوجستيات في مصر.</p>
            </div>
            <div class="hero-video-wrapper">
                <div class="main-video">
                    <div class="video-badge">فيديو تعريفي</div>
                    <div class="play-btn"></div>
                </div>
            </div>
        </div>

        <div class="carousel-section">
            <div class="carousel-arrow">
                <svg viewBox="0 0 24 24">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                </svg>
            </div>
            <div class="carousel-items">
                <div class="thumb-video">
                    <div class="video-badge">فيديو 1</div>
                    <div class="play-btn"></div>
                </div>
                <div class="thumb-video">
                    <div class="video-badge">فيديو 2</div>
                    <div class="play-btn"></div>
                </div>
                <div class="thumb-video">
                    <div class="video-badge">فيديو 3</div>
                    <div class="play-btn"></div>
                </div>
                <div class="thumb-video">
                    <div class="video-badge">فيديو 4</div>
                    <div class="play-btn"></div>
                </div>
                <div class="thumb-video">
                    <div class="video-badge">فيديو 5</div>
                    <div class="play-btn"></div>
                </div>
            </div>
            <div class="carousel-arrow">
                <svg viewBox="0 0 24 24">
                    <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z" />
                </svg>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')

    {{-- JS TO CONTROL THE ARROWS --}}
    <script>
        function scrollTopNav(direction) {
            const container = document.getElementById('topLinksContainer');
            if (container) container.scrollBy({
                left: direction,
                behavior: 'smooth'
            });
        }

        function scrollSubNav(direction) {
            const container = document.getElementById('subNavContainer');
            if (container) container.scrollBy({
                left: direction,
                behavior: 'smooth'
            });
        }
    </script>
</body>

</html>