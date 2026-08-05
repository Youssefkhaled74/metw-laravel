@extends('layouts.admin')

@section('title', __('admin-dashboard.cron_jobs'))

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $text = fn (string $english, string $arabic) => $isArabic ? $arabic : $english;

        $statusBadge = [
            'running' => 'bg-info-subtle text-info',
            'success' => 'bg-success-subtle text-success',
            'failed' => 'bg-danger-subtle text-danger',
        ];
        $statusLabel = [
            'running' => $text('Running', 'قيد التنفيذ'),
            'success' => $text('Success', 'ناجحة'),
            'failed' => $text('Failed', 'فاشلة'),
        ];

        $configLabels = [
            'working_hours_start' => [$text('Working hours start', 'بداية ساعات العمل'), ''],
            'working_hours_end' => [$text('Working hours end', 'نهاية ساعات العمل'), ''],
            'auto_reject_working_hours' => [$text('Auto reject after (X)', 'الرفض التلقائي (X)'), 'working hours'],
            'response_window_working_hours' => [$text('Response window (Y)', 'نافذة الاستجابة (Y)'), 'working hours'],
            'cancel_unpaid_advance_hours' => [$text('Cancel unpaid advance (Z)', 'إلغاء الدفعة (Z)'), 'real hours'],
            'execution_start_hours' => [$text('Execution start (W)', 'بدء التنفيذ (W)'), 'real hours'],
            'aggregate_sub_shipments_days' => [$text('Aggregation every (N)', 'التجميع كل (N)'), 'days'],
            'aggregate_sub_shipments_scope' => [$text('Aggregation scope', 'نطاق التجميع'), ''],
            'aggregate_sub_shipments_target_id' => [$text('Aggregation target', 'الهدف'), ''],
        ];

        $configKeys = [
            'courier-auto-reject' => ['auto_reject_working_hours', 'working_hours_start', 'working_hours_end'],
            'courier-close-response-window' => ['response_window_working_hours', 'working_hours_start', 'working_hours_end'],
            'advance-payments-cancel-unpaid' => ['cancel_unpaid_advance_hours'],
            'sub-shipments-aggregate' => ['aggregate_sub_shipments_days', 'aggregate_sub_shipments_scope', 'aggregate_sub_shipments_target_id'],
            'shipment-requests-mark-execution-start' => ['execution_start_hours'],
        ];
    @endphp

    <style data-page-style="cron-job-detail">
        .cron-detail-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #155e75 100%);
            border-radius: 20px;
            padding: 1.3rem 1.5rem;
            color: #fff;
            display: flex;
            gap: 1rem;
            align-items: center;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        }

        .cron-detail-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .14);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .cron-kv {
            display: flex;
            justify-content: space-between;
            gap: .75rem;
            padding: .5rem 0;
            border-bottom: 1px dashed #e5e7eb;
            font-size: .88rem;
        }

        .cron-kv:last-child {
            border-bottom: none;
        }

        .cron-kv span:first-child {
            color: #64748b;
        }

        .cron-kv strong {
            color: #0f172a;
            text-align: end;
        }
    </style>

    <div class="cron-detail-hero mb-4">
        <span class="cron-detail-icon"><i class="{{ $definition['icon'] }}"></i></span>
        <div class="flex-grow-1">
            <h1 class="h4 mb-1 fw-bold">{{ $isArabic ? $definition['label_ar'] : $definition['label_en'] }}</h1>
            <p class="mb-0" style="color:rgba(255,255,255,.78);font-size:.88rem">
                {{ $isArabic ? $definition['description_ar'] : $definition['description_en'] }}
            </p>
        </div>
        <a href="{{ route('admin.cron-jobs.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{ $text('All jobs', 'كل المهام') }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="fas fa-sliders-h text-primary me-1"></i>
                        {{ $text('Current configuration', 'الإعدادات الحالية') }}
                    </h6>
                </div>
                <div class="card-body py-2">
                    @foreach ($configKeys[$definition['key']] ?? [] as $key)
                        @php
                            $label = $configLabels[$key][0] ?? $key;
                            $unit = $configLabels[$key][1] ?? '';
                            $value = setting($key);
                            if ($key === 'aggregate_sub_shipments_scope') {
                                $value = [
                                    'all' => $text('All warehouses', 'كل المستودعات'),
                                    'warehouse' => $text('One warehouse', 'مستودع واحد'),
                                    'group' => $text('One governorate group', 'مجموعة محافظة واحدة'),
                                ][$value] ?? $value;
                            }
                            if ($key === 'aggregate_sub_shipments_target_id' && $value) {
                                $value = '#' . $value;
                            }
                        @endphp
                        <div class="cron-kv">
                            <span>{{ $label }}</span>
                            <strong>{{ $value }} {{ $unit }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="fas fa-heartbeat text-success me-1"></i>
                        {{ $text('Health', 'الصحة') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="cron-kv">
                        <span>{{ $text('Runs today', 'تنفيذات اليوم') }}</span>
                        <strong>{{ $runsToday }}</strong>
                    </div>
                    <div class="cron-kv">
                        <span>{{ $text('Success rate (7 days)', 'معدل النجاح (7 أيام)') }}</span>
                        <strong>{{ $successRate7d === null ? '—' : $successRate7d . '%' }}</strong>
                    </div>
                    <div class="cron-kv">
                        <span>{{ $text('Success rate (30 days)', 'معدل النجاح (30 يوم)') }}</span>
                        <strong>{{ $successRate30d === null ? '—' : $successRate30d . '%' }}</strong>
                    </div>
                    <div class="cron-kv">
                        <span>{{ $text('Last run', 'آخر تنفيذ') }}</span>
                        <strong>{{ $definition['last_run']?->started_at?->format('Y-m-d H:i') ?? '—' }}</strong>
                    </div>
                    <div class="cron-kv">
                        <span>{{ $text('Next run', 'التنفيذ القادم') }}</span>
                        <strong>{{ $definition['next_run']?->format('Y-m-d H:i') ?? '—' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-between">
                <h6 class="mb-0"><i class="fas fa-history text-info me-1"></i>
                    {{ $text('Execution history', 'سجل التنفيذ') }}
                </h6>

                <form method="GET" action="{{ route('admin.cron-jobs.show', $definition['key']) }}"
                    class="d-flex flex-wrap align-items-center gap-2">
                    <select name="status" class="form-select form-select-sm" style="width:auto">
                        <option value="">{{ $text('All statuses', 'كل الحالات') }}</option>
                        @foreach (['running', 'success', 'failed'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $statusLabel[$status] }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="from" class="form-control form-control-sm" style="width:auto"
                        value="{{ $filters['from'] ?? '' }}">
                    <input type="date" name="to" class="form-control form-control-sm" style="width:auto"
                        value="{{ $filters['to'] ?? '' }}">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-filter me-1"></i> {{ $text('Filter', 'تصفية') }}
                    </button>
                    @if (! empty($filters))
                        <a href="{{ route('admin.cron-jobs.show', $definition['key']) }}" class="btn btn-sm btn-link">
                            {{ $text('Clear', 'مسح') }}
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ $text('Started at', 'البداية') }}</th>
                        <th>{{ $text('Status', 'الحالة') }}</th>
                        <th>{{ $text('Duration', 'المدة') }}</th>
                        <th>{{ $text('Processed', 'تمت معالجتها') }}</th>
                        <th>{{ $text('Affected', 'المتأثرة') }}</th>
                        <th>{{ $text('Error', 'الخطأ') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-nowrap">{{ $log->started_at?->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <span class="badge {{ $statusBadge[$log->status] ?? 'bg-secondary' }}">
                                    {{ $statusLabel[$log->status] ?? $log->status }}
                                </span>
                            </td>
                            <td>{{ $log->duration !== null ? number_format($log->duration, 2) . 's' : '—' }}</td>
                            <td>{{ $log->records_processed }}</td>
                            <td>{{ $log->records_affected }}</td>
                            <td class="text-truncate" style="max-width:220px">
                                @if ($log->error_message)
                                    <span class="text-danger" title="{{ $log->error_message }}">{{ \Illuminate\Support\Str::limit($log->error_message, 60) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if (! empty($log->metadata))
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="collapse"
                                        data-bs-target="#log-meta-{{ $log->id }}" aria-expanded="false">
                                        <i class="fas fa-code me-1"></i> {{ $text('Log', 'التفاصيل') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @if (! empty($log->metadata))
                            <tr class="collapse" id="log-meta-{{ $log->id }}">
                                <td colspan="7" class="bg-light">
                                    <pre class="mb-0" style="font-size:.75rem;white-space:pre-wrap">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                {{ $text('No execution records found.', 'لا توجد سجلات تنفيذ.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="card-footer bg-white">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
