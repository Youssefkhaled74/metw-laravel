<div class="row g-3" id="tasks-list">
    @forelse ($tasks as $task)
        <div class="col-12">
            <div class="card border-0 shadow-sm task-card hover-lift" data-type="{{ $task['type'] }}" data-priority="{{ $task['priority'] }}">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div class="d-flex align-items-start gap-3 flex-grow-1">
                            <div class="task-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0
                                bg-{{ $task['type_color'] }}-subtle text-{{ $task['type_color'] }}"
                                style="width: 48px; height: 48px; font-size: 1.1rem;">
                                <i class="{{ $task['type_icon'] }}"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge bg-{{ $task['type_color'] }}-subtle text-{{ $task['type_color'] }} rounded-pill">
                                        {{ $task['type_label'] }}
                                    </span>
                                    <span class="badge bg-{{ $task['priority'] === 'critical' ? 'danger' : ($task['priority'] === 'high' ? 'warning' : 'secondary') }}-subtle text-{{ $task['priority'] === 'critical' ? 'danger' : ($task['priority'] === 'high' ? 'warning' : 'secondary') }} rounded-pill">
                                        {{ $task['priority_label'] }}
                                    </span>
                                    <span class="badge bg-{{ $task['status_color'] }}-subtle text-{{ $task['status_color'] }} rounded-pill">
                                        {{ $task['status_label'] }}
                                    </span>
                                </div>
                                <h6 class="mb-1 fw-semibold">{{ $task['order_number'] }}</h6>
                                <div class="text-muted small d-flex flex-wrap gap-3">
                                    <span><i class="fas fa-user me-1"></i>{{ $task['customer_name'] }}</span>
                                    @if (!empty($task['product_name']))
                                        <span><i class="fas fa-box me-1"></i>{{ $task['product_name'] }}</span>
                                    @endif
                                    @if (!empty($task['quantity']))
                                        <span><i class="fas fa-times me-1"></i>{{ $task['quantity'] }}</span>
                                    @endif
                                    @if (!empty($task['total']))
                                        <span class="fw-semibold text-success">{{ number_format($task['total'], 2) }} ر.س</span>
                                    @endif
                                </div>
                                @if (!empty($task['reason']))
                                    <div class="mt-1 small text-muted">
                                        <i class="fas fa-comment-dots me-1"></i>{{ Str::limit($task['reason'], 80) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                            <span class="text-muted small">
                                <i class="fas fa-clock me-1"></i>{{ $task['age_text'] }}
                            </span>
                            <div class="d-flex gap-2">
                                <a href="{{ $task['detail_url'] }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i> تفاصيل
                                </a>
                                @if ($task['process_url'])
                                    <button class="btn btn-sm btn-{{ $task['type_color'] }} rounded-pill px-3 process-task-btn"
                                            data-task-type="{{ $task['type'] }}"
                                            data-task-id="{{ $task['id'] }}">
                                        <i class="fas fa-check me-1"></i> معالجة
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            @include('dashboard.vendor.urgent-tasks.partials.empty-state-inner')
        </div>
    @endforelse
</div>
