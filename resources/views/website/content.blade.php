@extends('website.layout')

@section('title', 'محتوى ميتو للخدمات والمستخدمين')

@push('styles')
<style>
    .content-page-wrapper {
        background-color: #f9f9f9;
        padding: 40px 0 80px;
    }

    .page-main-title {
        text-align: center;
        font-size: clamp(22px, 3vw, 30px);
        font-weight: 700;
        color: #222;
        margin-bottom: 34px;
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .service-card {
        background: #fff;
        border: 2px solid #fcd8c7;
        border-radius: 16px;
        padding: 26px 22px;
        text-align: center;
        box-shadow: 0 12px 24px rgba(16, 24, 40, 0.05);
    }

    .service-card h2 {
        font-size: 18px;
        font-weight: 700;
        color: #4a1f6e;
        margin-bottom: 12px;
    }

    .service-card p {
        font-size: 15px;
        color: #555;
        line-height: 1.9;
        margin-bottom: 0;
    }

    @media (max-width: 992px) {
        .service-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .service-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container content-page-wrapper">
    <h1 class="page-main-title">هذا المحتوى موجه لمقدمي الخدمات والمستخدمين بمنصة وتطبيقات ميتو</h1>

    <div class="service-grid">
        @forelse(($cards ?? []) as $card)
            <div class="service-card">
                <h2>{{ $card['title'] ?? '' }}</h2>
                <p>{!! $card['content'] ?? '' !!}</p>
            </div>
        @empty
            <div class="service-card">
                <h2>لا يوجد محتوى بعد</h2>
                <p>أضف صفحات من نوع "Other" من لوحة الإدارة لتظهر هنا.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
