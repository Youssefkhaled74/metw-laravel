@extends('layouts.admin')

@section('title', __('admin-dashboard.whatsapp_templates_management'))
@section('page-title', __('admin-dashboard.whatsapp_templates_management'))

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="mb-2">{{ __('admin-dashboard.whatsapp_templates_title') }}</h5>
            <p class="text-muted mb-0">{{ __('admin-dashboard.whatsapp_templates_subtitle') }}</p>
        </div>
    </div>

    @foreach ($templateRows as $row)
        <form method="POST" action="{{ route('admin.settings.whatsapp-templates.update') }}" class="mb-3">
            @csrf
            @method('PATCH')

            <div class="card whatsapp-template-card">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="mb-1">{{ $row['label'] }}</h6>
                            <small class="text-muted">Key: <code>{{ $row['key'] }}</code></small>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <label class="form-label fw-semibold template-label">{{ __('admin-dashboard.whatsapp_template_hints') }}</label>
                    <small class="template-hint d-block mb-3">{{ __('admin-dashboard.whatsapp_template_double_click_hint') }}</small>
                    <div class="placeholder-chips d-flex flex-wrap gap-2 mb-3">
                        @foreach ($row['hints'] as $hint)
                            <button
                                type="button"
                                class="btn btn-sm placeholder-chip js-placeholder-chip"
                                title="Double click to insert"
                                data-placeholder="{{ $hint }}"
                                data-target="template_{{ $row['key'] }}"
                            >
                                {{ $hint }}
                            </button>
                        @endforeach
                    </div>
                    <small class="template-subhint d-block mb-3">{{ __('admin-dashboard.whatsapp_template_hints_help') }}</small>

                    <label for="template_{{ $row['key'] }}" class="form-label fw-semibold template-label">{{ __('admin-dashboard.config_value') }}</label>
                    <textarea
                        id="template_{{ $row['key'] }}"
                        name="templates[{{ $row['key'] }}]"
                        rows="8"
                        data-allowed-placeholders='@json($row['hints'])'
                        class="form-control template-textarea @error('templates.' . $row['key']) is-invalid @enderror"
                    >{{ old('templates.' . $row['key'], $row['content']) }}</textarea>

                    @error('templates.' . $row['key'])
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="card-footer bg-white border-0 pt-0 pb-3">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save me-1"></i>
                            {{ __('admin-dashboard.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endforeach
@endsection

@section('scripts')
    @parent
    <script>
        (function () {
            function extractPlaceholders(text) {
                const matches = text.match(/\{[a-z_]+\}/g) || [];
                return [...new Set(matches)];
            }

            function saveCaretState(textarea) {
                if (!textarea) {
                    return;
                }

                textarea.dataset.caretStart = String(textarea.selectionStart ?? 0);
                textarea.dataset.caretEnd = String(textarea.selectionEnd ?? 0);
                textarea.dataset.lastScrollTop = String(textarea.scrollTop ?? 0);
            }

            function setCopiedState(button) {
                button.classList.remove('btn-light');
                button.classList.add('is-selected');
                setTimeout(() => {
                    button.classList.add('btn-light');
                    button.classList.remove('is-selected');
                }, 500);
            }

            document.querySelectorAll('.template-textarea').forEach((textarea) => {
                ['click', 'keyup', 'select', 'focus', 'input'].forEach((eventName) => {
                    textarea.addEventListener(eventName, () => saveCaretState(textarea));
                });
            });

            document.querySelectorAll('.js-placeholder-chip').forEach((button) => {
                button.addEventListener('mousedown', function (event) {
                    // Keep focus/caret in the textarea and avoid jumpy selection behavior.
                    event.preventDefault();
                });

                button.addEventListener('dblclick', function () {
                    const placeholder = this.getAttribute('data-placeholder') || '';
                    const targetId = this.getAttribute('data-target') || '';
                    const textarea = document.getElementById(targetId);

                    if (textarea) {
                        const isActive = document.activeElement === textarea;
                        const savedStart = Number(textarea.dataset.caretStart ?? textarea.value.length);
                        const savedEnd = Number(textarea.dataset.caretEnd ?? textarea.value.length);
                        const start = isActive ? (textarea.selectionStart ?? textarea.value.length) : savedStart;
                        const end = isActive ? (textarea.selectionEnd ?? textarea.value.length) : savedEnd;
                        const currentScrollTop = Number(textarea.dataset.lastScrollTop ?? textarea.scrollTop ?? 0);

                        textarea.value = textarea.value.slice(0, start) + placeholder + textarea.value.slice(end);
                        textarea.focus({ preventScroll: true });
                        const nextPos = start + placeholder.length;
                        textarea.setSelectionRange(nextPos, nextPos);
                        saveCaretState(textarea);

                        // Restore scroll position to keep user at the same place.
                        textarea.scrollTop = currentScrollTop;
                        requestAnimationFrame(() => {
                            textarea.scrollTop = currentScrollTop;
                        });
                    }

                    setCopiedState(this);
                });
            });

            document.querySelectorAll('form[action="{{ route('admin.settings.whatsapp-templates.update') }}"]').forEach((form) => {
                form.addEventListener('submit', function (event) {
                    const textarea = this.querySelector('textarea[name^="templates["]');
                    if (!textarea) {
                        return;
                    }

                    const allowed = JSON.parse(textarea.getAttribute('data-allowed-placeholders') || '[]');
                    const found = extractPlaceholders(textarea.value || '');

                    const invalid = found.filter((token) => !allowed.includes(token));
                    const missing = allowed.filter((token) => !found.includes(token));

                    if (invalid.length || missing.length) {
                        event.preventDefault();
                        const ar = document.documentElement.lang === 'ar';
                        const msg = ar
                            ? (
                                (invalid.length ? ('متغيرات غير مسموح بها: ' + invalid.join(' ') + '\n') : '') +
                                (missing.length ? ('متغيرات أساسية ناقصة: ' + missing.join(' ')) : '')
                            )
                            : (
                                (invalid.length ? ('Unsupported placeholders: ' + invalid.join(' ') + '\n') : '') +
                                (missing.length ? ('Missing required placeholders: ' + missing.join(' ')) : '')
                            );
                        alert(msg);
                    }
                });
            });
        })();
    </script>
@endsection

@push('styles')
<style>
    .template-label {
        color: #1f2937;
        font-size: 0.95rem;
        letter-spacing: 0.01em;
    }

    .template-hint {
        color: #2563eb;
        font-weight: 500;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 0.45rem 0.65rem;
    }

    .template-subhint {
        color: #6b7280;
    }

    .placeholder-chips {
        padding: 0.2rem 0;
    }

    .placeholder-chip {
        border: 1px solid #bfdbfe;
        border-radius: 999px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        color: #1d4ed8;
        font-weight: 600;
        padding: 0.45rem 0.8rem;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.06);
        transition: all 0.18s ease;
    }

    .placeholder-chip:hover {
        border-color: #60a5fa;
        color: #1e40af;
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.16);
    }

    .placeholder-chip.is-selected {
        background: #dbeafe;
        border-color: #93c5fd;
        color: #1d4ed8;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.14);
    }

    .whatsapp-template-card {
        border: 1px solid #dbe5f4;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        border-radius: 16px;
        overflow: hidden;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .whatsapp-template-card .card-header {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%) !important;
        border-bottom: 1px solid #e5eef9;
    }

    .whatsapp-template-card .card-footer {
        background: #fff !important;
        border-top: 1px solid #e5eef9;
    }

    .template-textarea {
        border-radius: 12px;
        border-color: #dbe5f4;
        min-height: 180px;
        font-size: 0.95rem;
        line-height: 1.75;
        background-color: #fcfdff;
    }

    .template-textarea:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.16);
        background-color: #fff;
    }
</style>
@endpush
