@php
    $title = $title ?? '';
    $description = $description ?? '';
    $actions = $actions ?? [];
    $kicker = $kicker ?? 'محتوى الموقع';
@endphp

@once
    @push('styles')
        <style>
            .settings-module-shell {
                display: grid;
                gap: 18px;
            }

            .settings-module-hero {
                border-radius: 22px;
                padding: 24px 28px;
                background: linear-gradient(135deg, #fff 0%, #fff8f1 100%);
                border: 1px solid rgba(246, 137, 31, 0.14);
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                flex-wrap: wrap;
            }

            .settings-module-kicker {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 10px;
                padding: 8px 14px;
                border-radius: 999px;
                background: #fff3e8;
                color: #f46b1f;
                font-weight: 800;
                font-size: 13px;
            }

            .settings-module-hero h1 {
                margin: 0;
                color: #1f2937;
                font-size: clamp(22px, 2vw, 32px);
                font-weight: 900;
            }

            .settings-module-hero p {
                margin: 10px 0 0;
                color: #6b7280;
                line-height: 1.8;
                max-width: 800px;
            }

            .settings-module-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .settings-content-card {
                background: #fff;
                border-radius: 20px;
                border: 1px solid #edf0f4;
                box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
                overflow: hidden;
            }

            .settings-content-card .card-header {
                background: linear-gradient(180deg, #fff 0%, #fffaf6 100%);
                border-bottom: 1px solid #eef2f7;
            }

            .settings-content-card .card-title {
                margin: 0;
                color: #1f2937;
                font-weight: 800;
            }

            .settings-empty-state {
                padding: 28px;
                text-align: center;
                color: #6b7280;
            }

            @media (max-width: 768px) {
                .settings-module-hero {
                    padding: 20px;
                }

                .settings-module-actions {
                    justify-content: flex-start;
                }
            }
        </style>
    @endpush
@endonce

<div class="settings-module-shell">
    <div class="settings-module-hero">
        <div>
            <div class="settings-module-kicker">{{ $kicker }}</div>
            <h1>{{ $title }}</h1>
            @if($description)
                <p>{{ $description }}</p>
            @endif
        </div>

        @if(!empty($actions))
            <div class="settings-module-actions">
                @foreach($actions as $action)
                    <a href="{{ $action['url'] }}"
                       class="btn {{ $action['class'] ?? 'btn-primary' }}">
                        @if(!empty($action['icon']))
                            <i class="{{ $action['icon'] }} me-1"></i>
                        @endif
                        {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{ $slot ?? '' }}
</div>
