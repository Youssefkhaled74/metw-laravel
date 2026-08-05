@extends('website.layout')

@section('title', $pageTitle ?? 'الصفحة الرئيسية')

@push('styles')
<style>
    .home-hero {
        padding: 60px 0 30px;
        background: linear-gradient(180deg, #fcfaf6 0%, #fff 100%);
    }

    .home-hero-grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 32px;
        align-items: center;
    }

    .home-hero h1 {
        font-size: clamp(32px, 5vw, 56px);
        margin-bottom: 12px;
        color: #3c2415;
    }

    .home-hero h2 {
        font-size: clamp(18px, 2.4vw, 28px);
        color: #7b1fa2;
        margin-bottom: 18px;
        line-height: 1.6;
    }

    .home-hero p {
        font-size: 18px;
        line-height: 1.9;
        color: #555;
        margin-bottom: 24px;
    }

    .hero-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .hero-stat {
        background: #fff;
        border: 1px solid #eee2d3;
        border-radius: 16px;
        padding: 18px;
        text-align: center;
        box-shadow: 0 10px 20px rgba(60, 36, 21, 0.05);
    }

    .hero-stat strong {
        display: block;
        font-size: 28px;
        color: #f6891f;
        margin-bottom: 6px;
    }

    .hero-media {
        display: grid;
        gap: 16px;
    }

    .hero-banner {
        min-height: 280px;
        border-radius: 24px;
        background: linear-gradient(135deg, #4a1f6e, #f6891f);
        color: #fff;
        display: flex;
        align-items: flex-end;
        padding: 24px;
        box-shadow: 0 18px 40px rgba(74, 31, 110, 0.22);
        overflow: hidden;
    }

    .hero-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 20px;
    }

    .section-card {
        background: #fff;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 10px 30px rgba(16, 24, 40, 0.06);
        margin-top: 28px;
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: #3c2415;
        margin-bottom: 20px;
    }

    .text-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .text-card {
        border: 1px solid #f1e3d5;
        border-radius: 16px;
        padding: 18px;
        background: #fcfaf6;
    }

    .text-card h3 {
        font-size: 18px;
        margin-bottom: 10px;
        color: #4a1f6e;
    }

    .text-card p {
        margin: 0;
        line-height: 1.8;
        color: #555;
    }

    .video-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .video-card {
        border-radius: 18px;
        overflow: hidden;
        background: #1d1d1d;
        color: #fff;
    }

    .video-thumb {
        aspect-ratio: 16 / 9;
        background: linear-gradient(135deg, #4a1f6e, #7b1fa2);
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 16px;
    }

    .video-body {
        padding: 16px;
    }

    @media (max-width: 992px) {
        .home-hero-grid,
        .text-grid,
        .video-grid {
            grid-template-columns: 1fr;
        }

        .hero-stats {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<section class="home-hero">
    <div class="container">
        <div class="home-hero-grid">
            <div>
                <h1>{{ $heroTitle ?? ($site['name_ar'] ?? 'ميتولوجيستيك') }}</h1>
                <h2>{{ $heroSubtitle ?? ($site['tagline'] ?? '') }}</h2>
                <p>{{ $heroDescription ?? ($site['description'] ?? '') }}</p>

                <div class="hero-stats">
                    @foreach(($heroCards ?? []) as $card)
                        <div class="hero-stat">
                            <strong>{{ $card['value'] ?? '' }}</strong>
                            <span>{{ $card['label'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="hero-media">
                @forelse(($promotionalBanners ?? []) as $banner)
                    <div class="hero-banner" @if(!empty($banner['image'])) style="background-image:url('{{ $banner['image'] }}'); background-size:cover; background-position:center;" @endif>
                        <div>
                            <div class="badge bg-light text-dark mb-2">Banner</div>
                            @if(!empty($banner['link']))
                                <div class="small">{{ $banner['link'] }}</div>
                            @endif
                        </div>
                    </div>
                    @break
                @empty
                    <div class="hero-banner">
                        <div>
                            <div class="badge bg-light text-dark mb-2">Metw</div>
                            <div>لوحة رئيسية قابلة للتحديث من الإدارة</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<section class="container">
    <div class="section-card">
        <div class="section-title">محتوى قابل للإدارة</div>
        <div class="text-grid">
            @forelse(($promotionalTexts ?? []) as $text)
                <div class="text-card">
                    <h3>{{ $text['title'] ?? '' }}</h3>
                    <p>{!! $text['content'] ?? '' !!}</p>
                </div>
            @empty
                <div class="text-card">
                    <h3>لا يوجد محتوى بعد</h3>
                    <p>يمكنك إضافة صفحات من لوحة الإدارة لتظهر هنا وفي صفحة المنشورات.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="section-card">
        <div class="section-title">فيديوهات مميزة</div>
        <div class="video-grid">
            @forelse(($promotionalVideos ?? []) as $video)
                <article class="video-card">
                    <div class="video-thumb">
                        <div>
                            <div class="fw-bold mb-2">{{ $video['title'] ?? 'فيديو' }}</div>
                            <div class="small">{{ $video['description'] ?? '' }}</div>
                        </div>
                    </div>
                    <div class="video-body">
                        @if(!empty($video['video_path']))
                            <a class="btn btn-sm btn-outline-light w-100" href="{{ asset($video['video_path']) }}" target="_blank">عرض الفيديو</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="text-muted">لا توجد فيديوهات منشورة بعد.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection
