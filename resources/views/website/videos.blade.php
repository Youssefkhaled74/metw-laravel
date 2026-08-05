@extends('website.layout')

@section('title', 'فيديوهات موقع ميتو')

@push('styles')
<style>
    .videos-page-wrapper {
        padding: 60px 0 80px;
        background: #fff;
    }

    .videos-header {
        text-align: center;
        margin-bottom: 36px;
    }

    .videos-header h1 {
        font-size: clamp(28px, 4vw, 40px);
        color: #3c2415;
        margin-bottom: 10px;
    }

    .videos-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .video-card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(16, 24, 40, 0.08);
        background: #fff;
        border: 1px solid #f1e6d7;
    }

    .video-thumb {
        aspect-ratio: 16 / 9;
        background: linear-gradient(135deg, #4a1f6e, #f6891f);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 18px;
    }

    .video-card body,
    .video-body {
        padding: 18px;
    }

    .video-body h3 {
        font-size: 18px;
        color: #4a1f6e;
        margin-bottom: 8px;
    }

    .video-body p {
        margin: 0 0 12px;
        color: #555;
        line-height: 1.8;
    }

    @media (max-width: 992px) {
        .videos-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .videos-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container videos-page-wrapper">
    <div class="videos-header">
        <h1>فيديوهات موقع ميتو</h1>
        <p>تظهر الفيديوهات المنشورة من لوحة الإدارة هنا مباشرة.</p>
    </div>

    <div class="videos-grid">
        @forelse(($videos ?? []) as $video)
            <article class="video-card">
                <div class="video-thumb">
                    <div>
                        <div class="fw-bold mb-2">{{ $video['title'] ?? 'فيديو' }}</div>
                        <div class="small">{{ $video['description'] ?? '' }}</div>
                    </div>
                </div>
                <div class="video-body">
                    @if(!empty($video['video_path']))
                        <a href="{{ asset($video['video_path']) }}" target="_blank" rel="noopener" class="btn btn-primary w-100">عرض الفيديو</a>
                    @elseif(!empty($video['thumbnail']))
                        <img src="{{ asset($video['thumbnail']) }}" alt="{{ $video['title'] ?? 'فيديو' }}" class="img-fluid rounded">
                    @endif
                </div>
            </article>
        @empty
            <div class="text-muted">لا توجد فيديوهات منشورة بعد.</div>
        @endforelse
    </div>
</div>
@endsection
