@extends('website.layout')

@section('title', 'تطبيقات منصة ميتو')

@push('styles')
<style>
    .apps-page-wrapper {
        background-color: #fcfaf6;
        padding: 60px 0 80px;
    }

    .apps-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .apps-header h2 {
        font-size: clamp(26px, 4vw, 36px);
        font-weight: 700;
        color: #222;
        margin-bottom: 12px;
    }

    .apps-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 28px;
    }

    .app-card {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 10px 25px rgba(16, 24, 40, 0.06);
        text-align: center;
        border: 1px solid #f0ece4;
    }

    .app-icon {
        width: 88px;
        height: 88px;
        margin: 0 auto 16px;
        border-radius: 22px;
        background: linear-gradient(135deg, #4a1f6e, #f6891f);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
    }

    .app-title {
        font-size: 24px;
        font-weight: 700;
        color: #3c2415;
        margin-bottom: 18px;
    }

    .store-buttons {
        display: grid;
        gap: 10px;
    }

    .store-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 10px;
        padding: 12px 14px;
        background: #111;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
    }

    .store-btn.secondary {
        background: #f6891f;
    }

    @media (max-width: 992px) {
        .apps-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .apps-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container apps-page-wrapper">
    <div class="apps-header">
        <h2>تطبيقات منصة ميتو</h2>
        <p>تظهر التطبيقات هنا من لوحة الإدارة مع روابط المتاجر المختلفة لكل تطبيق.</p>
    </div>

    <div class="apps-grid">
        @forelse(($apps ?? []) as $app)
            <div class="app-card">
                <div class="app-icon">
                    @if(!empty($app['icon']))
                        <i class="{{ $app['icon'] }}"></i>
                    @else
                        <i class="fas fa-mobile-alt"></i>
                    @endif
                </div>

                <div class="app-title">{{ $app['name'] ?? '' }}</div>

                <div class="store-buttons">
                    @if(!empty($app['play_url']))
                        <a class="store-btn" href="{{ $app['play_url'] }}" target="_blank" rel="noopener">
                            <i class="fab fa-google-play"></i>
                            Google Play
                        </a>
                    @endif

                    @if(!empty($app['store_url']))
                        <a class="store-btn secondary" href="{{ $app['store_url'] }}" target="_blank" rel="noopener">
                            <i class="fab fa-app-store-ios"></i>
                            App Store
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="app-card">
                <div class="app-title">لا توجد تطبيقات بعد</div>
                <p>أضف التطبيقات من لوحة الإدارة لتظهر هنا.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
