@extends('layouts.admin')

@php
    $typeLabel = \App\Enum\PageType::tryFrom($type)?->label() ?? strtoupper($type);
@endphp

@section('title', app()->getLocale() === 'ar' ? 'هيستوري الصفحات' : 'Pages History')
@section('page-title', app()->getLocale() === 'ar' ? 'هيستوري النوع: ' . $typeLabel : 'History Type: ' . $typeLabel)

@section('page-actions')
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.settings.pages.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
        </a>
        <a href="{{ route('admin.settings.pages.create', ['type' => $type]) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة صفحة لهذا النوع' : 'Create Page For This Type' }}
        </a>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                {{ app()->getLocale() === 'ar' ? 'سجل النسخ لنوع: ' . $typeLabel : 'History Records For Type: ' . $typeLabel }}
            </h5>
            <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                {{ app()->getLocale() === 'ar' ? 'إجمالي: ' . $historyPages->count() : 'Total: ' . $historyPages->count() }}
            </span>
        </div>

        <div class="card-body">
            @if($historyPages->count() > 0)
                <div class="accordion" id="pageHistoryAccordion">
                    @foreach($historyPages as $page)
                        @php
                            $itemId = 'historyItem_' . $page->id;
                            $isCurrentRecord = !empty($currentPageId) && (int) $currentPageId === (int) $page->id;
                        @endphp

                        <div class="accordion-item mb-2 border rounded {{ $isCurrentRecord ? 'border-success shadow-sm' : '' }}">
                            <h2 class="accordion-header" id="heading_{{ $itemId }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $itemId }}" aria-expanded="false" aria-controls="collapse_{{ $itemId }}">
                                    <div class="w-100 d-flex flex-wrap justify-content-between align-items-center pe-3 gap-2">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="fw-semibold text-dark">
                                                {{ app()->getLocale() === 'ar' ? 'من' : 'From' }}
                                                {{ $page->active_from ? $page->active_from->format('Y-m-d') : (app()->getLocale() === 'ar' ? 'البداية' : 'Start') }}
                                                {{ app()->getLocale() === 'ar' ? 'إلى' : 'to' }}
                                                {{ $page->active_to ? $page->active_to->format('Y-m-d') : (app()->getLocale() === 'ar' ? 'مفتوح' : 'Open') }}
                                            </span>

                                            @if($isCurrentRecord)
                                                <span class="badge bg-success">
                                                    {{ app()->getLocale() === 'ar' ? 'الحالي الآن' : 'Current Now' }}
                                                </span>
                                            @endif
                                        </div>

                                        <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $page->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                        </span>
                                    </div>
                                </button>
                            </h2>

                            <div id="collapse_{{ $itemId }}" class="accordion-collapse collapse" aria-labelledby="heading_{{ $itemId }}" data-bs-parent="#pageHistoryAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                        <h6 class="mb-0 text-dark">{{ $page->title }}</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('admin.settings.pages.edit', $page) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit me-1"></i>
                                                {{ __('admin-dashboard.edit') }}
                                            </a>

                                            <form action="{{ route('admin.settings.pages.destroy', $page) }}" method="POST" class="d-inline m-0"
                                                  onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_page') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash me-1"></i>
                                                    {{ __('admin-dashboard.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-3"><span class="text-muted small">ID:</span> <span class="fw-semibold">{{ $page->id }}</span></div>
                                        <div class="col-md-3"><span class="text-muted small">{{ __('admin-dashboard.type') }}:</span> <span class="fw-semibold">{{ \App\Enum\PageType::tryFrom($page->type)?->label() ?? $page->type }}</span></div>
                                        <div class="col-md-3"><span class="text-muted small">Slug:</span> <span class="fw-semibold">{{ $page->slug }}</span></div>
                                        <div class="col-md-3"><span class="text-muted small">{{ __('admin-dashboard.status') }}:</span> <span class="fw-semibold">{{ $page->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}</span></div>
                                    </div>

                                    @if(!empty($page->title_ar))
                                        <div class="mb-2">
                                            <div class="text-muted small mb-1">{{ app()->getLocale() === 'ar' ? 'العنوان العربي' : 'Arabic title' }}</div>
                                            <div class="fw-semibold">{{ $page->title_ar }}</div>
                                        </div>
                                    @endif

                                    <div class="mb-2">
                                        <div class="text-muted small mb-1">{{ app()->getLocale() === 'ar' ? 'المحتوى' : 'Content' }}</div>
                                        <div class="border rounded p-2 bg-light">{!! $page->content !!}</div>
                                    </div>

                                    @if(!empty($page->content_ar))
                                        <div>
                                            <div class="text-muted small mb-1">{{ app()->getLocale() === 'ar' ? 'المحتوى العربي' : 'Arabic content' }}</div>
                                            <div class="border rounded p-2 bg-light">{!! $page->content_ar !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt mb-3 text-muted" style="font-size: 28px;"></i>
                    <h6 class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا يوجد هيستوري لهذا النوع.' : 'No history records found for this type.' }}</h6>
                    <a href="{{ route('admin.settings.pages.create', ['type' => $type]) }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة أول صفحة' : 'Create First Page' }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
