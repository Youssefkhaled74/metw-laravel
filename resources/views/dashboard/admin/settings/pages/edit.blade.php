@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_page'))

@push('styles')
<link data-page-style href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style data-page-style>
    .page-form-card { border: 0; border-radius: 16px; box-shadow: 0 12px 30px rgba(16, 24, 40, 0.08); }
    .page-form-section { border: 1px solid #e9ecf2; border-radius: 12px; padding: 18px; background: #fff; }
    .page-form-section + .page-form-section { margin-top: 14px; }
    .page-form-title { font-size: 14px; font-weight: 700; letter-spacing: 0.02em; text-transform: uppercase; color: #667085; margin-bottom: 12px; }
    .editor-shell { border: 1px solid #d0d5dd; border-radius: 12px; overflow: hidden; }
    .quill-editor { min-height: 220px; background: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @php
        $oldActiveFrom = old('active_from', optional($page->active_from)->format('Y-m-d'));
        $oldActiveTo = old('active_to', optional($page->active_to)->format('Y-m-d'));
        $inferredMode = old('validity_mode');
        $inferredYear = old('valid_year');

        if (!$inferredMode && $oldActiveFrom && $oldActiveTo) {
            $fromTs = strtotime($oldActiveFrom);
            $toTs = strtotime($oldActiveTo);
            $fromYear = (int) date('Y', $fromTs);
            $toYear = (int) date('Y', $toTs);
            $isFullYear = date('m-d', $fromTs) === '01-01' && date('m-d', $toTs) === '12-31' && $fromYear === $toYear;

            if ($isFullYear) {
                $inferredMode = $fromYear === (int) now()->year ? 'current_year' : 'specific_year';
                $inferredYear = $fromYear;
            }
        }

        if (!$inferredMode) $inferredMode = 'custom_range';
        if (!$inferredYear) $inferredYear = (int) now()->year;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.edit_page') }}</h1>
        <a href="{{ route('admin.settings.pages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
        </a>
    </div>

    <div class="card page-form-card mb-4">
        <div class="card-body p-4 p-lg-4">
            <form action="{{ route('admin.settings.pages.update', $page) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="page-form-section">
                    <div class="page-form-title">{{ app()->getLocale() === 'ar' ? 'البيانات الأساسية' : 'Basic Information' }}</div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="title">{{ __('admin-dashboard.page_title') }}</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $page->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="title_ar">{{ app()->getLocale() === 'ar' ? 'عنوان الصفحة (عربي)' : 'Page Title (Arabic)' }}</label>
                            <input type="text" class="form-control @error('title_ar') is-invalid @enderror"
                                   id="title_ar" name="title_ar" value="{{ old('title_ar', $page->title_ar) }}">
                            @error('title_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="type">{{ __('admin-dashboard.page_type') }}</label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">{{ __('admin-dashboard.select_type') }}</option>
                                @foreach($pageTypes as $type)
                                    <option value="{{ $type->value }}" {{ old('type', $page->type) == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 d-flex align-items-end">
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-form-section">
                    <div class="page-form-title">{{ app()->getLocale() === 'ar' ? 'فترة التفعيل' : 'Validity Range' }}</div>

                    <div class="form-group">
                        <label for="validity_mode">{{ app()->getLocale() === 'ar' ? 'طريقة تحديد الفترة' : 'Range Mode' }}</label>
                        <select id="validity_mode" name="validity_mode" class="form-control @error('validity_mode') is-invalid @enderror">
                            <option value="custom_range" {{ $inferredMode === 'custom_range' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'فترة مخصصة (يدوي)' : 'Custom Range' }}</option>
                            <option value="current_year" {{ $inferredMode === 'current_year' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'السنة الحالية تلقائيًا' : 'Current Year Automatically' }}</option>
                            <option value="specific_year" {{ $inferredMode === 'specific_year' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'سنة محددة' : 'Specific Year' }}</option>
                        </select>
                        @error('validity_mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row" id="year_row" style="display:none;">
                        <div class="form-group col-md-4">
                            <label for="valid_year">{{ app()->getLocale() === 'ar' ? 'السنة' : 'Year' }}</label>
                            <input type="number" class="form-control @error('valid_year') is-invalid @enderror"
                                   id="valid_year" name="valid_year"
                                   min="2000" max="2100" value="{{ $inferredYear }}">
                            @error('valid_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row" id="date_range_row">
                        <div class="form-group col-md-6">
                            <label for="active_from">{{ app()->getLocale() === 'ar' ? 'تاريخ بداية السريان' : 'Active From' }}</label>
                            <input type="date" class="form-control @error('active_from') is-invalid @enderror"
                                   id="active_from" name="active_from" value="{{ $oldActiveFrom }}">
                            @error('active_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="active_to">{{ app()->getLocale() === 'ar' ? 'تاريخ نهاية السريان' : 'Active To' }}</label>
                            <input type="date" class="form-control @error('active_to') is-invalid @enderror"
                                   id="active_to" name="active_to" value="{{ $oldActiveTo }}">
                            @error('active_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="page-form-section">
                    <div class="page-form-title">{{ app()->getLocale() === 'ar' ? 'محتوى الصفحة (Rich Text)' : 'Page Content (Rich Text)' }}</div>

                    <div class="form-group">
                        <label>{{ __('admin-dashboard.page_content') }}</label>
                        <div class="editor-shell">
                            <div id="content_editor" class="quill-editor"></div>
                        </div>
                        {{-- hidden textarea to pass initial content safely without unicode escaping --}}
                        <textarea id="content_initial" class="d-none">{{ old('content', $page->content) }}</textarea>
                        <textarea class="d-none @error('content') is-invalid @enderror" id="content" name="content">{{ old('content', $page->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label>{{ app()->getLocale() === 'ar' ? 'محتوى الصفحة (عربي)' : 'Page Content (Arabic)' }}</label>
                        <div class="editor-shell">
                            <div id="content_ar_editor" class="quill-editor"></div>
                        </div>
                        {{-- hidden textarea to pass initial content safely without unicode escaping --}}
                        <textarea id="content_ar_initial" class="d-none">{{ old('content_ar', $page->content_ar) }}</textarea>
                        <textarea class="d-none @error('content_ar') is-invalid @enderror" id="content_ar" name="content_ar">{{ old('content_ar', $page->content_ar) }}</textarea>
                        @error('content_ar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary px-4">{{ __('admin-dashboard.update_page') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script data-page-script>
    (function () {
        function initQuillEditors() {
            if (typeof Quill === 'undefined') {
                setTimeout(initQuillEditors, 100);
                return;
            }

            const toolbarOptions = [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ align: [] }],
                ['link', 'image', 'blockquote', 'code-block'],
                ['clean']
            ];

            function initQuill(editorId, inputId) {
                const editorElement = document.getElementById(editorId);
                const inputElement = document.getElementById(inputId);
                if (!editorElement || !inputElement) return null;

                const quill = new Quill(editorElement, {
                    theme: 'snow',
                    modules: { toolbar: toolbarOptions }
                });

                // Read initial content from the dedicated hidden textarea (avoids unicode escape issues)
                const initialId = editorId.replace('_editor', '_initial');
                const initialElement = document.getElementById(initialId);
                const initialHtml = initialElement ? initialElement.value.trim() : '';
                if (initialHtml) {
                    quill.root.innerHTML = initialHtml;
                }

                return { quill, input: inputElement };
            }

            const editors = [
                initQuill('content_editor', 'content'),
                initQuill('content_ar_editor', 'content_ar')
            ].filter(Boolean);

            const form = document.querySelector('form[action*="pages"]');
            if (form) {
                form.addEventListener('submit', function () {
                    editors.forEach(function (instance) {
                        const isEmpty = instance.quill.getText().trim().length === 0;
                        instance.input.value = isEmpty ? '' : instance.quill.root.innerHTML;
                    });
                });
            }
        }

        if (typeof Quill === 'undefined') {
            const quillScript = document.createElement('script');
            quillScript.src = 'https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js';
            quillScript.onload = initQuillEditors;
            document.head.appendChild(quillScript);
        } else {
            initQuillEditors();
        }

        // Validity mode logic
        const mode = document.getElementById('validity_mode');
        const yearRow = document.getElementById('year_row');
        const dateRow = document.getElementById('date_range_row');
        const yearInput = document.getElementById('valid_year');
        const activeFrom = document.getElementById('active_from');
        const activeTo = document.getElementById('active_to');

        function applyMode() {
            if (!mode) return;
            const value = mode.value;
            yearRow.style.display = value === 'specific_year' ? '' : 'none';
            dateRow.style.display = value === 'custom_range' ? '' : 'none';

            if (value === 'current_year') {
                const year = new Date().getFullYear();
                activeFrom.value = year + '-01-01';
                activeTo.value = year + '-12-31';
            }
            if (value === 'specific_year' && yearInput.value) {
                activeFrom.value = yearInput.value + '-01-01';
                activeTo.value = yearInput.value + '-12-31';
            }
        }

        mode.addEventListener('change', applyMode);
        yearInput.addEventListener('input', function () {
            if (mode.value === 'specific_year' && yearInput.value.length === 4) {
                activeFrom.value = yearInput.value + '-01-01';
                activeTo.value = yearInput.value + '-12-31';
            }
        });

        applyMode();
    })();
</script>
@endsection
