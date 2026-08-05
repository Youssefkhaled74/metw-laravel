@extends('website.layout')

@section('title', 'صور الموقع')

@push('styles')
<style>
    /* --- Specific Page Styles for "Swar el mawke3" --- */
    
    /* Hero Section */
    .site-images-hero {
        display: flex;
        gap: 50px;
        padding: 50px 0;
        align-items: center;
    }
    
    .hero-text-content {
        flex: 1;
        text-align: right;
    }
    .hero-text-content h1 {
        font-size: 40px;
        color: #222;
        margin-bottom: 10px;
    }
    .hero-text-content h2 {
        font-size: 22px;
        color: #555;
        margin-bottom: 30px;
        font-weight: 500;
        line-height: 1.6;
    }

    /* Image Block */
    .hero-image-block {
        flex: 1;
        display: flex;
        justify-content: center;
    }
    .large-promo-image {
        width: 100%;
        max-width: 500px;
        aspect-ratio: 1.2 / 1;
        background-color: #dcb6f0; /* Soft purple/pink background */
        border: 4px solid var(--orange);
        border-radius: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: var(--orange);
        font-size: 24px;
        font-weight: bold;
    }

    /* Stats Cards */
    .stats-container {
        display: flex;
        gap: 20px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    .stat-card {
        background: #fff;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        text-align: center;
        flex: 1;
        min-width: 120px;
    }
    .stat-number {
        font-size: 28px;
        font-weight: bold;
        color: var(--dark-purple);
        margin-bottom: 5px;
        display: block;
    }
    .stat-label {
        font-size: 14px;
        color: #777;
    }

    /* Bottom Carousel */
    .carousel-purple-wrapper {
        border: 2px solid #e9d5ff; /* Light purple border */
        border-radius: 15px;
        padding: 15px 10px;
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        gap: 15px;
        background: transparent;
    }
    .carousel-arrow-image {
        width: 40px; 
        height: 40px; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        cursor: pointer; 
        flex-shrink: 0;
    }
    .carousel-arrow-image svg {
        fill: var(--orange);
        width: 100%;
        height: 100%;
    }
    .white-cards-scroller {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        scroll-behavior: smooth;
        flex: 1;
    }
    .white-cards-scroller::-webkit-scrollbar { display: none; }
    .white-card-item {
        flex: 0 0 14%;
        min-width: 130px;
        aspect-ratio: 1.3 / 1;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 12px;
        color: #888;
    }

    @media (max-width: 900px) {
        .site-images-hero { flex-direction: column-reverse; text-align: center; }
        .hero-text-content { text-align: center; }
        .stats-container { justify-content: center; }
        .stat-card { min-width: 100px; }
    }
</style>
@endpush

@section('content')
    <!-- Hero Section: Text on Right, Image on Left -->
    <section class="container site-images-hero">
        <div class="hero-text-content">
            <h1>Metw ميتو</h1>
            <h2>منصة خدمات تجارية ولوجستية مصرية، هي الأولى من نوعها في مصر</h2>

            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">متابعة رقمية</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">27</span>
                    <span class="stat-label">محافظة مستهدفة</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">4</span>
                    <span class="stat-label">منصات مرتبطة</span>
                </div>
            </div>
        </div>

        <div class="hero-image-block">
            <div class="large-promo-image">
                صورة دعائية
            </div>
        </div>
    </section>

    <!-- Bottom Carousel with White Cards -->
    <section class="container carousel-purple-wrapper">
        <!-- Left Arrow -->
        <div class="carousel-arrow-image">
            <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
        </div>

        <div class="white-cards-scroller">
            <div class="white-card-item">صورة دعائية</div>
            <div class="white-card-item">صورة دعائية</div>
            <div class="white-card-item">صورة دعائية</div>
            <div class="white-card-item">صورة دعائية</div>
            <div class="white-card-item">صورة دعائية</div>
            <div class="white-card-item">صورة دعائية</div>
            <div class="white-card-item">صورة دعائية</div>
            <div class="white-card-item">صورة دعائية</div>
        </div>

        <!-- Right Arrow -->
        <div class="carousel-arrow-image">
            <svg viewBox="0 0 24 24"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg>
        </div>
    </section>
@endsection