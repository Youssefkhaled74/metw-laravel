@extends('website.layout')

@section('title', 'الصفحة الرئيسية')

@section('content')
    <!-- Hero Section -->
    <section class="container hero-section">
        <div class="hero-text">
            <h1>Metw ميتو</h1>
            <h2>منصة واحدة تربط الماركات بالشحن والمستودعات والمندوبين</h2>
            <p>حيث تربط منصة ميتو Metw بين كل من، تطبيق ماركت مالي ستور وتطبيق شحن داخلي ودليفري ومستودعات التخزين لدى الغير وتطبيق طلب مناديب الشحن والتوصيل، في تجربة تشغيل واضحة وسهلة وسريعة.</p>
        </div>

        <div class="hero-video-wrapper">
            <div class="main-video">
                <span class="video-badge">فيديو</span>
                <div class="play-btn"></div>
            </div>
        </div>
    </section>

    <!-- Fixed Carousel Section -->
    <section class="container carousel-section">
        <!-- Arrow pointing left (on the right side in RTL) -->
        <div class="carousel-arrow">
            <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
        </div>

        <div class="carousel-items">
            <div class="thumb-video"><span class="video-badge">فيديو</span><div class="play-btn"></div></div>
            <div class="thumb-video"><span class="video-badge">فيديو</span><div class="play-btn"></div></div>
            <div class="thumb-video"><span class="video-badge">فيديو</span><div class="play-btn"></div></div>
            <div class="thumb-video"><span class="video-badge">فيديو</span><div class="play-btn"></div></div>
            <div class="thumb-video"><span class="video-badge">فيديو</span><div class="play-btn"></div></div>
            <div class="thumb-video"><span class="video-badge">فيديو</span><div class="play-btn"></div></div>
        </div>

        <!-- Arrow pointing right (on the left side in RTL) -->
        <div class="carousel-arrow">
            <svg viewBox="0 0 24 24"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg>
        </div>
    </section>
@endsection