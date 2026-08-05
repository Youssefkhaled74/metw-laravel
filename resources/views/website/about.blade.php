@extends('website.layout')

@section('title', $pageTitle ?? 'من نحن')

@push('styles')
<style>
    .about-page-wrapper {
        background-color: #fcfaf6;
        padding: 60px 0 80px;
        color: #222;
    }

    .about-content {
        max-width: 980px;
        margin: 0 auto;
    }

    .about-title {
        font-size: clamp(26px, 4vw, 40px);
        font-weight: 700;
        color: #3c2415;
        margin-bottom: 18px;
        line-height: 1.6;
        text-align: center;
    }

    .about-summary {
        font-size: 18px;
        line-height: 1.9;
        color: #555;
        text-align: center;
        margin-bottom: 28px;
    }

    .about-body {
        background: #fff;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 12px 30px rgba(16, 24, 40, 0.06);
        line-height: 1.9;
        color: #444;
    }

    .about-body h2,
    .about-body h3 {
        color: #4a1f6e;
        margin-top: 1.25rem;
    }

    @media (max-width: 600px) {
        .about-body {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="container about-page-wrapper">
    <div class="about-content">
        <h1 class="about-title">{{ $heading ?? 'من نحن' }}</h1>
        <p class="about-summary">{{ $summary ?? '' }}</p>

        @if(!empty($aboutHtml))
            <div class="about-body">
                {!! $aboutHtml !!}
            </div>
        @else
            <div class="about-body">
                <p>منصة ميتو وتطبيقاتها تجمع بين السوق والخدمات اللوجستية والتشغيل اليومي في تجربة واحدة.</p>
                <ul>
                    <li>الموقع الإلكتروني الرسمي لمنصة ميتو</li>
                    <li>تطبيق السوق الإلكتروني Metwzon</li>
                    <li>تطبيق الشحن والتوصيل MetwExpress</li>
                    <li>تطبيق طلب المناديب MetwGo</li>
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
