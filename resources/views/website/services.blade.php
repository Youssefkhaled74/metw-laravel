@extends('website.layout')

@section('title', 'خدمات ميتو الإلكترونية')

@push('styles')
<style>
    .services-page-wrapper {
        background-color: #fdfbf9;
        padding: 60px 0 80px;
    }

    .services-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .services-header h2 {
        font-size: clamp(26px, 4vw, 38px);
        font-weight: 700;
        color: #222;
        margin-bottom: 14px;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
    }

    .service-card {
        background: #fff;
        border-radius: 20px;
        padding: 28px 24px 32px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
        text-align: center;
    }

    .card-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 16px;
        border-radius: 50%;
        background: #f8efe4;
        color: #f6891f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .card-title-en {
        font-size: 22px;
        color: #f6891f;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .card-title-ar {
        font-size: 24px;
        color: #4a1f6e;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .service-card p {
        font-size: 15px;
        color: #444;
        line-height: 1.9;
        margin-bottom: 0;
    }

    @media (max-width: 1024px) {
        .services-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .services-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container services-page-wrapper">
    <div class="services-header">
        <h2>خدمات منصة ميتو الإلكترونية</h2>
        <p>كل خدمة قابلة للإضافة أو التعديل من لوحة الإدارة، وتظهر هنا مباشرة في صفحة الموقع العامة.</p>
    </div>

    <div class="services-grid">
        @forelse(($services ?? []) as $service)
            <div class="service-card">
                <div class="card-icon">
                    @if(!empty($service['icon']))
                        <i class="{{ $service['icon'] }}"></i>
                    @else
                        <i class="fas fa-layer-group"></i>
                    @endif
                </div>
                <div class="card-title-en">{{ $service['en_title'] ?? '' }}</div>
                <div class="card-title-ar">{{ $service['ar_title'] ?? '' }}</div>
                <p>{!! $service['description'] ?? '' !!}</p>
            </div>
        @empty
            <div class="service-card">
                <div class="card-title-ar">لا توجد خدمات بعد</div>
                <p>أضف خدمات من لوحة الإدارة لتظهر هنا.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
