@extends('website.layout')

@section('title', 'صور الموقع')

@push('styles')
<style>
    .gallery-page {
        padding: 60px 0 80px;
        background: #fcfaf6;
    }

    .gallery-title {
        text-align: center;
        margin-bottom: 34px;
        font-size: clamp(26px, 4vw, 38px);
        font-weight: 700;
        color: #3c2415;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .gallery-card {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(16, 24, 40, 0.06);
    }

    .gallery-card img {
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        display: block;
    }

    .gallery-card .caption {
        padding: 16px;
        font-weight: 600;
        color: #4a1f6e;
    }

    @media (max-width: 992px) {
        .gallery-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .gallery-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container gallery-page">
    <h1 class="gallery-title">صور الموقع</h1>

    <div class="gallery-grid">
        @forelse(($galleries ?? []) as $gallery)
            <figure class="gallery-card">
                @if(!empty($gallery['image']))
                    <img src="{{ $gallery['image'] }}" alt="{{ $gallery['title'] ?? 'صورة' }}">
                @else
                    <div style="aspect-ratio:4/3;background:#f1e3d5;display:flex;align-items:center;justify-content:center;">لا توجد صورة</div>
                @endif
                <figcaption class="caption">{{ $gallery['title'] ?? '' }}</figcaption>
            </figure>
        @empty
            <div class="text-muted">لا توجد صور منشورة بعد.</div>
        @endforelse
    </div>
</div>
@endsection
