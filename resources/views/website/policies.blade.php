@extends('website.layout')

@section('content')
@php
    $blocks = $policyBlocks ?? [];
@endphp

<section class="inner-hero">
    <div class="container">
        <span class="pill"><span class="dot"></span> {{ $breadcrumb ?? 'السياسات' }}</span>
        <h1 style="color:var(--purple);font-size:clamp(38px,5vw,64px);margin:18px 0 12px">{{ $heading ?? 'سياسات الاستخدام' }}</h1>
        <p class="section-sub">{{ $summary ?? 'صفحة ثابتة لعرض سياسات الموقع.' }}</p>
    </div>
</section>

<section class="section" style="padding-top:24px">
    <div class="container">
        <div class="content-card policy-list">
            @forelse ($blocks as $block)
                <div class="policy-item">
                    <h3>{{ $block['title'] }}</h3>
                    <p>{{ $block['body'] }}</p>
                </div>
            @empty
                <div class="policy-item">
                    <h3>الشروط العامة</h3>
                    <p>يستخدم الموقع للتعريف بخدمات ميتولوجيستيك وتمكين الدخول إلى لوحات التحكم المختلفة.</p>
                </div>
                <div class="policy-item">
                    <h3>الخصوصية</h3>
                    <p>تتم معالجة البيانات والطلبات بما يخدم تشغيل المنظومة فقط، مع الحفاظ على سرية الاستخدام.</p>
                </div>
                <div class="policy-item">
                    <h3>التحديثات</h3>
                    <p>قد يتم تحديث هذه السياسات عند إضافة خدمات أو لوحات أو محتوى جديد من الإدارة.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
