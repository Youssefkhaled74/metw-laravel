@extends('layouts.admin')

@section('title', __('admin-dashboard.pages_management'))
@section('page-title', __('admin-dashboard.pages_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.pages.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_page') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.pages_management') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-file-alt me-2"></i>
                    {{ app()->getLocale() === 'ar' ? 'الأنواع: ' . $totalTypes . ' | السجلات: ' . $totalPages : 'Types: ' . $totalTypes . ' | Records: ' . $totalPages }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.pages.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-9">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بعنوان الصفحة أو الـ slug أو النوع أو ID...' : 'Search by title, slug, type, or ID...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-3 d-flex gap-2 justify-content-lg-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                    </button>
                    <a href="{{ route('admin.settings.pages.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($groupedPages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap">{{ __('admin-dashboard.type') }}</th>
                                <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'عدد النسخ' : 'History Count' }}</th>
                                <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedPages as $type => $historyPages)
                                @php
                                    $typeLabel = \App\Enum\PageType::tryFrom($type)?->label() ?? strtoupper($type);
                                @endphp
                                <tr>
                                    <td class="fw-semibold">
                                        <a href="{{ route('admin.settings.pages.history', ['type' => $type]) }}" class="text-decoration-none">
                                            {{ $typeLabel }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border">
                                            {{ $historyPages->count() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('admin.settings.pages.history', ['type' => $type]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-history me-1"></i>
                                                {{ app()->getLocale() === 'ar' ? 'عرض السجل' : 'View History' }}
                                            </a>
                                            <a href="{{ route('admin.settings.pages.create', ['type' => $type]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus me-1"></i>
                                                {{ app()->getLocale() === 'ar' ? 'إضافة' : 'Create' }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-file-alt empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_pages_found') }}</h5>
                        <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'لا توجد صفحات مضافة حالياً.' : 'No pages are available at the moment.' }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.settings.pages.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_page') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
