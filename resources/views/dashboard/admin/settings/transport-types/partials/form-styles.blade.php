<style>
    .ttf-page {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .ttf-hero-card,
    .ttf-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
    }

    .ttf-hero-card {
        padding: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .ttf-hero-main {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        min-width: 0;
    }

    .ttf-hero-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .ttf-chip {
        display: inline-flex;
        width: fit-content;
        padding: .28rem .7rem;
        margin-bottom: .45rem;
        border-radius: 999px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        color: #2563eb;
        font-size: .78rem;
        font-weight: 900;
    }

    .ttf-hero-text h3 {
        margin: 0;
        color: #111827;
        font-size: 1.45rem;
        font-weight: 900;
        letter-spacing: -.02em;
    }

    .ttf-hero-text p {
        margin: .45rem 0 0;
        max-width: 760px;
        color: #64748b;
        font-size: .95rem;
        line-height: 1.8;
    }

    .ttf-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        margin-top: .7rem;
    }

    .ttf-meta-row span {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .38rem .75rem;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        color: #475569;
        font-size: .78rem;
        font-weight: 900;
    }

    .ttf-meta-row .is-active {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }

    .ttf-meta-row .is-inactive {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .ttf-card {
        padding: 1.25rem;
        margin-bottom: 1rem;
    }

    .ttf-sticky {
        position: sticky;
        top: 1rem;
    }

    .ttf-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .ttf-section-head h5 {
        margin: 0;
        color: #111827;
        font-size: 1rem;
        font-weight: 900;
    }

    .ttf-section-head p {
        margin: .25rem 0 0;
        color: #64748b;
        font-size: .86rem;
        line-height: 1.6;
    }

    .ttf-label {
        display: block;
        margin-bottom: .4rem;
        color: #475569;
        font-size: .8rem;
        font-weight: 900;
    }

    .ttf-label span {
        color: #dc2626;
    }

    .ttf-control {
        min-height: 44px;
        border-radius: 13px;
        border-color: #dbe3ea;
        color: #111827;
        font-size: .9rem;
        box-shadow: none;
    }

    textarea.ttf-control {
        min-height: 110px;
    }

    .ttf-control:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
    }

    .ttf-control:disabled {
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .ttf-switch-box {
        min-height: 76px;
        padding: .9rem;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .ttf-switch-box strong {
        display: block;
        color: #111827;
        font-size: .9rem;
        font-weight: 900;
        margin-bottom: .2rem;
    }

    .ttf-switch-box small {
        display: block;
        color: #64748b;
        font-size: .78rem;
        line-height: 1.5;
    }

    .ttf-switch-box .form-check-input {
        width: 2.8rem;
        height: 1.45rem;
        cursor: pointer;
    }

    .ttf-preview {
        padding: 1rem;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        text-align: center;
    }

    .ttf-preview-icon {
        width: 58px;
        height: 58px;
        margin: 0 auto .75rem;
        border-radius: 20px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .ttf-preview-code {
        display: inline-flex;
        width: fit-content;
        margin-bottom: .6rem;
        padding: .25rem .65rem;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #1d4ed8;
        font-size: .78rem;
        font-weight: 900;
    }

    .ttf-preview h4 {
        margin: 0;
        color: #111827;
        font-size: 1.1rem;
        font-weight: 900;
    }

    .ttf-preview small {
        display: block;
        margin-top: .25rem;
        color: #64748b;
        font-size: .8rem;
    }

    .ttf-preview p {
        margin: .8rem 0 0;
        color: #475569;
        font-size: .86rem;
        line-height: 1.7;
    }

    .ttf-preview-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: .5rem;
        margin-top: 1rem;
    }

    .ttf-preview-meta span {
        display: inline-flex;
        padding: .35rem .7rem;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #475569;
        font-size: .76rem;
        font-weight: 900;
    }

    .ttf-preview-meta .is-active {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }

    .ttf-preview-meta .is-inactive {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .ttf-actions {
        display: grid;
        gap: .65rem;
        margin-top: 1rem;
    }

    .ttf-back-btn,
    .ttf-save-btn {
        min-height: 42px;
        border-radius: 999px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .ttf-back-btn {
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #475569;
    }

    .ttf-back-btn:hover {
        background: #f8fafc;
        color: #111827;
    }

    .ttf-save-btn {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
        box-shadow: 0 10px 18px rgba(37, 99, 235, .14);
    }

    .ttf-save-btn:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }

    @media (max-width: 1199.98px) {
        .ttf-sticky {
            position: static;
        }

        .ttf-hero-card {
            align-items: stretch;
            flex-direction: column;
        }
    }

    @media (max-width: 767.98px) {
        .ttf-hero-card,
        .ttf-card {
            padding: 1rem;
            border-radius: 18px;
        }

        .ttf-hero-main {
            flex-direction: column;
        }

        .ttf-hero-text h3 {
            font-size: 1.2rem;
        }

        .ttf-section-head {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const unlimited = document.getElementById('unlimited_capacity');
        const active = document.getElementById('is_active');
        const capacityInputs = document.querySelectorAll('[data-capacity-input]');

        const previewCode = document.getElementById('previewCode');
        const previewName = document.getElementById('previewName');
        const previewArabicName = document.getElementById('previewArabicName');
        const previewDescription = document.getElementById('previewDescription');
        const previewCapacity = document.getElementById('previewCapacity');
        const previewStatus = document.getElementById('previewStatus');

        const labels = {
            code: @json(app()->getLocale() === 'ar' ? 'الكود' : 'CODE'),
            name: @json(app()->getLocale() === 'ar' ? 'اسم نوع النقل' : 'Transport name'),
            arabicName: @json(app()->getLocale() === 'ar' ? 'سيظهر الاسم العربي هنا' : 'Arabic name will appear here'),
            description: @json(app()->getLocale() === 'ar' ? 'ستظهر معاينة الوصف هنا.' : 'Description preview will appear here.'),
            unlimited: @json(app()->getLocale() === 'ar' ? 'سعة غير محدودة' : 'Unlimited capacity'),
            limits: @json(app()->getLocale() === 'ar' ? 'حدود السعة' : 'Capacity limits'),
            active: @json(__('admin-dashboard.active')),
            inactive: @json(__('admin-dashboard.inactive')),
            kg: @json('kg'),
            volume: @json('m³')
        };

        function getInput(name) {
            return document.querySelector(`[name="${name}"]`);
        }

        function updateCapacityState() {
            if (!unlimited) {
                return;
            }

            capacityInputs.forEach(function (input) {
                input.disabled = unlimited.checked;
            });
        }

        function updatePreview() {
            const code = getInput('code')?.value?.trim();
            const nameEn = getInput('name_en')?.value?.trim();
            const nameAr = getInput('name_ar')?.value?.trim();
            const description = getInput('description')?.value?.trim();
            const maxWeight = getInput('max_weight')?.value?.trim();
            const maxVolume = getInput('max_volume')?.value?.trim();

            if (previewCode) {
                previewCode.textContent = code || labels.code;
            }

            if (previewName) {
                previewName.textContent = nameEn || labels.name;
            }

            if (previewArabicName) {
                previewArabicName.textContent = nameAr || labels.arabicName;
            }

            if (previewDescription) {
                previewDescription.textContent = description || labels.description;
            }

            if (previewCapacity) {
                if (unlimited?.checked) {
                    previewCapacity.textContent = labels.unlimited;
                } else {
                    const weightText = maxWeight ? `${maxWeight} ${labels.kg}` : '--';
                    const volumeText = maxVolume ? `${maxVolume} ${labels.volume}` : '--';
                    previewCapacity.textContent = `${weightText} / ${volumeText}`;
                }
            }

            if (previewStatus && active) {
                previewStatus.textContent = active.checked ? labels.active : labels.inactive;
                previewStatus.classList.toggle('is-active', active.checked);
                previewStatus.classList.toggle('is-inactive', !active.checked);
            }
        }

        document.querySelectorAll('[data-preview], [data-capacity-input]').forEach(function (input) {
            input.addEventListener('input', updatePreview);
            input.addEventListener('change', function () {
                updateCapacityState();
                updatePreview();
            });
        });

        updateCapacityState();
        updatePreview();
    });
</script>