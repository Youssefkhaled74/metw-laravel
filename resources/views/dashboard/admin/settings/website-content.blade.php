@extends('layouts.admin')

@section('title', 'محتوى الموقع')
@section('page-title', 'محتوى الموقع')

@component('dashboard.admin.settings.partials.module-shell', [
    'kicker' => 'مركز إدارة محتوى الموقع',
    'title' => 'محتوى الموقع',
    'description' => 'من هنا تفتح كل أدوات التحكم في الصفحات العامة، الصور، الفيديوهات، الخدمات، التطبيقات، والسياسات.',
    'actions' => [
        ['label' => 'العودة للوحة', 'url' => route('admin.dashboard'), 'icon' => 'fas fa-house', 'class' => 'btn-outline-secondary'],
    ],
])
    <div class="website-content-page">
    <div class="settings-content-card">
        <div class="card-header py-3">
            <h3 class="card-title">الأقسام المتاحة</h3>
        </div>
        <div class="card-body">
            <div class="website-content-grid">
                @forelse($sections as $section)
                    <div class="content-card">
                        <div class="icon"><i class="{{ $section['icon'] }}"></i></div>
                        <h5>{{ $section['title'] }}</h5>
                        <p>{{ $section['description'] }}</p>
                        <a href="{{ $section['route'] }}" class="btn btn-primary btn-sm w-100">فتح القسم</a>
                    </div>
                @empty
                    <div class="website-content-empty">
                        <div class="settings-empty-state">لا توجد أقسام متاحة لحسابك حاليًا.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    </div>
@endcomponent

@push('styles')
<style>
    .website-content-page {
        display: grid;
        gap: 16px;
        margin-top: -0.35rem;
        width: 100%;
        min-width: 0;
        max-width: 100%;
    }

    .website-content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        width: 100%;
        min-width: 0;
        max-width: 100%;
    }

    .website-content-empty {
        grid-column: 1 / -1;
    }

    .content-card {
        background: #fff;
        border-radius: 18px;
        padding: 20px;
        border: 1px solid #ece5f3;
        box-shadow: 0 10px 24px rgba(16, 24, 40, 0.05);
        min-height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }

    .content-card .icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        background: #f4ecfb;
        color: #7b1fa2;
        font-size: 18px;
    }

    .content-card h5 {
        margin-bottom: 8px;
        color: #24172e;
        font-weight: 900;
    }

    .content-card p {
        color: #6b7280;
        line-height: 1.8;
        margin-bottom: 16px;
    }

    @media (max-width: 992px) {
        .website-content-page {
            margin-top: -0.2rem;
        }
    }

    @media (max-width: 600px) {
        .website-content-page {
            margin-top: 0;
        }
    }
</style>
@endpush
