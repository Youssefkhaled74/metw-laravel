@extends('website.layout')

@section('title', 'تواصل معنا | Metw')

@push('styles')
<style>
    .contact-page-wrapper {
        background-color: #fcfaf6;
        padding: 60px 0 60px;
        color: #444;
    }

    .contact-content {
        max-width: 940px;
        margin: 0 auto;
        text-align: center;
    }

    .section-title {
        font-size: clamp(22px, 3vw, 30px);
        font-weight: 700;
        color: #3c2415;
        margin-bottom: 22px;
    }

    .contact-info-grid {
        display: grid;
        gap: 14px;
        margin-bottom: 30px;
    }

    .info-row {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .info-label {
        font-size: 16px;
        color: #444;
    }

    .info-value {
        font-size: 16px;
        font-weight: 700;
        color: #7b1fa2;
        text-decoration: none;
        direction: ltr;
        display: inline-block;
    }

    .address-section {
        margin-top: 10px;
        margin-bottom: 40px;
    }

    .address-text {
        font-size: 16px;
        color: #444;
        line-height: 1.9;
        text-align: center;
    }

    .whatsapp-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background-color: #25D366;
        color: #fff !important;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: background-color 0.3s;
        margin: 20px 0;
    }

    .jobs-box {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 12px 30px rgba(16, 24, 40, 0.06);
        margin-top: 28px;
    }

    .jobs-box p {
        font-size: 16px;
        color: #444;
        line-height: 1.9;
        margin-bottom: 22px;
    }

    @media (max-width: 600px) {
        .info-row {
            flex-direction: column;
            gap: 6px;
        }
    }
</style>
@endpush

@section('content')
@php
    $contactItems = collect($contacts ?? []);
    $phones = $contactItems->where('type', 'phone')->values();
    $emails = $contactItems->where('type', 'email')->values();
    $addresses = $contactItems->where('type', 'address')->values();
    $whatsapps = $contactItems->where('type', 'whatsapp')->values();
@endphp

<div class="container contact-page-wrapper">
    <div class="contact-content">
        <div class="section-title">{{ $contactHeading ?? 'تواصل مع ميتو' }}</div>

        <div class="contact-info-grid">
            @forelse($phones as $phone)
                <div class="info-row">
                    <span class="info-label">{{ $phone->label_ar ?: $phone->label_en ?: 'هاتف' }}</span>
                    <a href="tel:{{ preg_replace('/\D+/', '', $phone->value ?? '') }}" class="info-value">{{ $phone->value }}</a>
                </div>
            @empty
                <div class="info-row">
                    <span class="info-label">هاتف</span>
                    <span class="info-value">01093298208</span>
                </div>
            @endforelse

            @forelse($emails as $email)
                <div class="info-row">
                    <span class="info-label">{{ $email->label_ar ?: $email->label_en ?: 'البريد الإلكتروني' }}</span>
                    <a href="mailto:{{ $email->value }}" class="info-value">{{ $email->value }}</a>
                </div>
            @empty
                <div class="info-row">
                    <span class="info-label">البريد الإلكتروني</span>
                    <a href="mailto:metwinfo@gmail.com" class="info-value">metwinfo@gmail.com</a>
                </div>
            @endforelse
        </div>

        @if($whatsapps->isNotEmpty())
            @foreach($whatsapps as $whatsapp)
                @php $whatsappNumber = preg_replace('/\D+/', '', $whatsapp->value ?? ''); @endphp
                <a href="https://wa.me/{{ $whatsappNumber }}" class="whatsapp-link" target="_blank" rel="noopener">
                    <i class="fab fa-whatsapp"></i>
                    {{ $whatsapp->label_ar ?: $whatsapp->label_en ?: 'تواصل معنا على الواتس' }}
                </a>
            @endforeach
        @else
            <a href="https://wa.me/201093298208" class="whatsapp-link" target="_blank" rel="noopener">
                <i class="fab fa-whatsapp"></i>
                تواصل معنا على الواتس
            </a>
        @endif

        <div class="address-section">
            <div class="info-label" style="font-weight:700; margin-bottom:10px;">{{ $contactAddressLabel ?? 'عنوان ميتو' }}</div>
            <p class="address-text">{{ $addresses->first()?->value ?? $contactAddress }}</p>
        </div>

        <hr class="section-divider">

        <div class="jobs-box">
            <div class="section-title">{{ $contactJobsHeading ?? 'وظائف ميتو' }}</div>
            <p>{{ $contactJobsDescription ?? '' }}</p>
            <div class="info-row">
                <span class="info-label" style="font-weight:700;">{{ app()->getLocale() === 'ar' ? 'بريد الوظائف' : 'Jobs email' }}</span>
                <a href="mailto:{{ $contactJobsEmail ?? 'metwjob@gmail.com' }}" class="info-value">{{ $contactJobsEmail ?? 'metwjob@gmail.com' }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
