@extends('website.layout')

@section('title', 'السياسات والشروط')

@push('styles')
<style>
    .policies-page-wrapper {
        background-color: #fcfaf6;
        padding: 60px 0 80px;
        min-height: 60vh;
    }

    .policies-title {
        text-align: center;
        font-size: clamp(26px, 4vw, 38px);
        font-weight: 700;
        color: #3c2415;
        margin-bottom: 34px;
    }

    .policies-links-list {
        display: grid;
        gap: 14px;
        max-width: 760px;
        margin: 0 auto 40px;
    }

    .policy-link {
        display: block;
        background: #fff;
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 10px 25px rgba(16, 24, 40, 0.06);
        color: #4a1f6e;
        font-weight: 700;
        text-decoration: none;
    }

    .policy-link:hover {
        color: #f6891f;
    }

    .partners-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .partners-title {
        font-size: 20px;
        font-weight: 700;
        color: #3c2415;
    }
</style>
@endpush

@section('content')
<div class="container policies-page-wrapper">
    <h1 class="policies-title">السياسات والشروط الخاصة باستخدام منصة وتطبيقات ميتو</h1>

    <div class="policies-links-list">
        @forelse(($policyItems ?? []) as $item)
            <a href="{{ $item['route'] }}" class="policy-link">{{ $item['title'] }}</a>
        @empty
            <div class="text-center text-muted">لا توجد روابط سياسات بعد.</div>
        @endforelse
    </div>

    <div class="partners-section">
        <h3 class="partners-title">شركاء النجاح</h3>
        <div class="partner-logo">
            <svg viewBox="0 0 200 60" width="140" role="img" aria-label="evyx">
                <text x="0" y="45" font-family="Arial, sans-serif" font-size="55" font-weight="bold" fill="#1a4c82">evy</text>
                <text x="140" y="45" font-family="Arial, sans-serif" font-size="55" font-weight="bold" fill="#ee7724">x</text>
            </svg>
        </div>
    </div>
</div>
@endsection
