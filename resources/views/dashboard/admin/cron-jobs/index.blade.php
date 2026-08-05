@extends('layouts.admin')

@section('title', __('admin-dashboard.cron_jobs'))

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $text = fn (string $english, string $arabic) => $isArabic ? $arabic : $english;
    @endphp

    <style data-page-style="cron-jobs-monitor">
        :root {
            --cron-bg: #f6f8fb;
            --cron-surface: #ffffff;
            --cron-border: #e5e7eb;
            --cron-text: #0f172a;
            --cron-muted: #64748b;
            --cron-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }

        .cron-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #155e75 100%);
            border-radius: 22px;
            padding: 1.4rem 1.6rem;
            color: #fff;
            box-shadow: var(--cron-shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .cron-hero h1 {
            font-size: 1.35rem;
            font-weight: 800;
            margin: 0 0 .2rem;
        }

        .cron-hero p {
            margin: 0;
            color: rgba(255, 255, 255, .75);
            font-size: .88rem;
        }

        .cron-overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin: 1.25rem 0;
        }

        .cron-overview-card {
            background: var(--cron-surface);
            border: 1px solid var(--cron-border);
            border-radius: 18px;
            padding: 1rem 1.15rem;
            box-shadow: var(--cron-shadow);
        }

        .cron-overview-card .cron-ov-label {
            font-size: .74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--cron-muted);
        }

        .cron-overview-card .cron-ov-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--cron-text);
        }

        .cron-job-card {
            background: var(--cron-surface);
            border: 1px solid var(--cron-border);
            border-radius: 18px;
            box-shadow: var(--cron-shadow);
            padding: 1.15rem 1.3rem;
            margin-bottom: 1rem;
            display: flex;
            gap: 1.1rem;
            align-items: flex-start;
        }

        .cron-job-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .cron-job-body {
            flex: 1;
            min-width: 0;
        }

        .cron-job-title {
            font-weight: 800;
            color: var(--cron-text);
            font-size: .98rem;
        }

        .cron-job-desc {
            color: var(--cron-muted);
            font-size: .84rem;
            margin: .25rem 0 .6rem;
        }

        .cron-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .22rem .6rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
        }

        .cron-stat {
            display: inline-flex;
            flex-direction: column;
            font-size: .74rem;
            color: var(--cron-muted);
        }

        .cron-stat strong {
            color: var(--cron-text);
            font-size: .9rem;
        }

        .cron-progress {
            height: 6px;
            border-radius: 999px;
            background: #eef2f7;
            overflow: hidden;
            min-width: 90px;
        }

        .cron-progress > div {
            height: 100%;
            border-radius: 999px;
        }
    </style>

    <div class="cron-hero mb-1">
        <div>
            <h1>{{ $text('Cron Jobs Monitor', 'مراقبة المهام المجدولة') }}</h1>
            <p>{{ $text('Overview of the five shipping timers, their schedule, health and execution history.', 'نظرة عامة على مؤقتات الشحن الخمسة وجدولها الزمني وصحتها وسجل التنفيذ.') }}</p>
        </div>
        <a href="{{ route('admin.settings.shipping-timers.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-sliders-h me-1"></i> {{ $text('Timer settings', 'إعدادات المؤقتات') }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="cron-overview-grid">
        <div class="cron-overview-card">
            <div class="cron-ov-label">{{ $text('Total jobs', 'إجمالي المهام') }}</div>
            <div class="cron-ov-value">{{ $overview['total_jobs'] }}</div>
        </div>
        <div class="cron-overview-card">
            <div class="cron-ov-label">{{ $text('Runs today', 'تنفيذات اليوم') }}</div>
            <div class="cron-ov-value">{{ $overview['runs_today'] }}</div>
        </div>
        <div class="cron-overview-card">
            <div class="cron-ov-label">{{ $text('Successful today', 'ناجحة اليوم') }}</div>
            <div class="cron-ov-value text-success">{{ $overview['success_today'] }}</div>
        </div>
        <div class="cron-overview-card">
            <div class="cron-ov-label">{{ $text('Failed today', 'فاشلة اليوم') }}</div>
            <div class="cron-ov-value text-danger">{{ $overview['failed_today'] }}</div>
        </div>
        <div class="cron-overview-card">
            <div class="cron-ov-label">{{ $text('Last run', 'آخر تنفيذ') }}</div>
            <div class="cron-ov-value" style="font-size:1rem">
                {{ $overview['last_run']?->started_at?->format('Y-m-d H:i') ?? $text('Never', 'لم يحدث') }}
            </div>
        </div>
        <div class="cron-overview-card">
            <div class="cron-ov-label">{{ $text('Next run', 'التنفيذ القادم') }}</div>
            <div class="cron-ov-value" style="font-size:1rem">
                {{ $overview['next_run']?->format('Y-m-d H:i') ?? $text('Unknown', 'غير معروف') }}
            </div>
        </div>
    </div>

    @foreach ($jobs as $job)
        @php
            $lastRun = $job['last_run'];
            $statusKey = $lastRun ? $lastRun->status : 'idle';
            $statusLabel = [
                'success' => $text('Success', 'ناجحة'),
                'failed' => $text('Failed', 'فاشلة'),
                'running' => $text('Running', 'قيد التنفيذ'),
                'idle' => $text('Idle', 'خاملة'),
            ][$statusKey];
            $badgeClass = [
                'success' => 'bg-success-subtle text-success',
                'failed' => 'bg-danger-subtle text-danger',
                'running' => 'bg-info-subtle text-info',
                'idle' => 'bg-secondary-subtle text-secondary',
            ][$statusKey];
            $iconTone = [
                'success' => 'bg-success-subtle text-success',
                'failed' => 'bg-danger-subtle text-danger',
                'running' => 'bg-info-subtle text-info',
                'idle' => 'bg-light text-secondary',
            ][$statusKey];
            $rate7 = $job['success_rate_7d'];
            $rate30 = $job['success_rate_30d'];
        @endphp

        <div class="cron-job-card">
            <span class="cron-job-icon {{ $iconTone }}"><i class="{{ $job['icon'] }}"></i></span>
            <div class="cron-job-body">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="cron-job-title">{{ $isArabic ? $job['label_ar'] : $job['label_en'] }}</span>
                    <span class="cron-badge {{ $badgeClass }}">
                        <span class="rounded-circle" style="width:7px;height:7px;background:currentColor"></span>
                        {{ $statusLabel }}
                    </span>
                    <span class="ms-auto text-muted small">{{ $isArabic ? $job['frequency_label_ar'] : $job['frequency_label_en'] }}</span>
                </div>
                <div class="cron-job-desc">{{ $isArabic ? $job['description_ar'] : $job['description_en'] }}</div>

                <div class="d-flex flex-wrap gap-4">
                    <div class="cron-stat">
                        <span>{{ $text('Last run', 'آخر تنفيذ') }}</span>
                        <strong>{{ $lastRun?->started_at?->format('Y-m-d H:i') ?? $text('Never', 'لم يحدث') }}</strong>
                    </div>
                    <div class="cron-stat">
                        <span>{{ $text('Next run', 'التنفيذ القادم') }}</span>
                        <strong>{{ $job['next_run']?->format('Y-m-d H:i') ?? '—' }}</strong>
                    </div>
                    <div class="cron-stat">
                        <span>{{ $text('Duration', 'المدة') }}</span>
                        <strong>{{ $lastRun?->duration !== null ? number_format($lastRun->duration, 2) . 's' : '—' }}</strong>
                    </div>
                    <div class="cron-stat">
                        <span>{{ $text('Affected', 'المتأثرة') }}</span>
                        <strong>{{ $lastRun?->records_affected ?? 0 }}</strong>
                    </div>
                    <div class="cron-stat">
                        <span>{{ $text('Runs today', 'تنفيذات اليوم') }}</span>
                        <strong>{{ $job['runs_today'] }}</strong>
                    </div>

                    <div class="cron-stat">
                        <span>{{ $text('Success 7d', 'النجاح 7 أيام') }}</span>
                        <span class="d-flex align-items-center gap-2">
                            <span class="cron-progress">
                                <div class="{{ ($rate7 ?? 0) >= 60 ? 'bg-success' : (($rate7 ?? 0) > 0 ? 'bg-warning' : 'bg-secondary') }}"
                                    style="width:{{ $rate7 ?? 0 }}%"></div>
                            </span>
                            <strong>{{ $rate7 === null ? '—' : $rate7 . '%' }}</strong>
                        </span>
                    </div>

                    <div class="cron-stat">
                        <span>{{ $text('Success 30d', 'النجاح 30 يوم') }}</span>
                        <span class="d-flex align-items-center gap-2">
                            <span class="cron-progress">
                                <div class="{{ ($rate30 ?? 0) >= 60 ? 'bg-success' : (($rate30 ?? 0) > 0 ? 'bg-warning' : 'bg-secondary') }}"
                                    style="width:{{ $rate30 ?? 0 }}%"></div>
                            </span>
                            <strong>{{ $rate30 === null ? '—' : $rate30 . '%' }}</strong>
                        </span>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('admin.cron-jobs.show', $job['key']) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-history me-1"></i> {{ $text('History', 'السجل') }}
                    </a>
                    <form action="{{ route('admin.cron-jobs.run-now', $job['key']) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('{{ $text('Run this cron job now?', 'تشغيل هذه المهمة الآن؟') }}')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-play me-1"></i> {{ $text('Run now', 'تشغيل الآن') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
