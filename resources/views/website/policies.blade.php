@extends('website.layout')

@section('title', $pageTitle ?? 'السياسات')

@push('styles')
<style>
    .inner-hero {
        padding: 60px 0 30px;
        background: linear-gradient(180deg, #fcfaf6 0%, #fff 100%);
    }

    .policy-list {
        display: grid;
        gap: 18px;
    }

    .policy-item {
        background: #fff;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(16, 24, 40, 0.06);
    }

    .policy-item h3 {
        color: #4a1f6e;
        margin-bottom: 12px;
    }

    .policy-item p {
        margin: 0;
        line-height: 1.9;
        color: #555;
    }
</style>
@endpush

@section('content')
@php $blocks = $policyBlocks ?? []; @endphp

<section class="inner-hero">
    <div class="container">
        <span class="pill"><span class="dot"></span> {{ $breadcrumb ?? 'السياسات' }}</span>
        <h1 style="color:var(--purple);font-size:clamp(38px,5vw,64px);margin:18px 0 12px">{{ $heading ?? 'سياسات الاستخدام' }}</h1>
        <p class="section-sub">{{ $summary ?? '' }}</p>
    </div>
</section>

<section class="section" style="padding-top:24px">
    <div class="container">
        <div class="policy-list">
            @forelse ($blocks as $block)
                <div class="policy-item">
                    <h3>{{ $block['title'] ?? '' }}</h3>
                    <p>{!! $block['body'] ?? '' !!}</p>
                </div>
            @empty
                <div class="policy-item">
                    <h3>لا يوجد محتوى بعد</h3>
                    <p>يمكنك إضافة صفحات السياسات من لوحة الإدارة لتظهر هنا تلقائيًا.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
