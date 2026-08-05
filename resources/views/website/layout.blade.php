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
            /* Prevents logo from getting squished */
        }

        .logo .orange {
            color: var(--orange);
        }

        .logo .purple {
            color: var(--dark-purple);
        }

        /* Fixed Links to prevent aggressive wrapping */
        .top-links {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            flex-wrap: wrap;
            justify-content: flex-end;
            /* Pushes links to the left in RTL */
            flex: 1;
        }

        .top-links a {
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid transparent;
            background: #f8f9fa;
            color: #555;
            transition: 0.2s;
            white-space: nowrap;
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

        /* Sub Navigation */
        .sub-nav {
            background-color: var(--bg-light-orange);
            padding: 12px 20px;
            border-radius: 6px;
            display: flex;
            gap: 30px;
            align-items: center;
            color: var(--orange);
            font-weight: 700;
            font-size: 15px;
            margin-top: 5px;
            flex-wrap: wrap;
        }

        .sub-nav a:hover {
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

        /* --- Fixed Carousel Styling (Fixes missing arrows and alignment) --- */
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

            .sub-nav {
                justify-content: center;
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
                <div class="top-links">
                    <a href="#" class="lang-toggle"><span class="globe-icon">🌐</span> العربية</a>
                    <a href="#" class="active">إدارة ميتو</a>
                    <a href="#">دخول المتاجر</a>
                    <a href="#">دخول المستودعات</a>
                    <a href="#">تواصل معنا</a>
                    <a href="#">نحن</a>
                    <a href="#">السياسات والشروط</a>
                    <a href="#">تطبيقات ميتو</a>
                    <a href="#">خدمات ميتو</a>
                    <a href="#">منشورات الموقع</a>
                    <a href="{{ route('website.images') }}">صور الموقع</a> <a href="#">فيديوهات الموقع</a>
                </div>
            </div>
            <nav class="sub-nav">
                <a href="#">منصة ميتو</a>
                <a href="#">للتجارة والخدمات اللوجستية</a>
                <a href="#">ماركت إلكتروني مالي ستور</a>
                <a href="#">خدمات التخزين للتجار</a>
                <a href="#">خدمات شحن بين محافظات ومدن وقرى مصر</a>
                <a href="#">دليفري سريع وآمن</a>
                <a href="#">فوالف المندوبين من الباب للباب</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>