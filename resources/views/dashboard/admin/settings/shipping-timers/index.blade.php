@extends('layouts.admin')

@section('title', __('admin-dashboard.shipping_timers'))

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $text = fn (string $english, string $arabic) => $isArabic ? $arabic : $english;
        $hasError = fn (string $field) => $errors->has($field) ? ' is-invalid' : '';
        $oldOr = fn (string $field, $fallback) => old($field, $fallback ?? '');
    @endphp

    <div class="row">
        <div class="col-lg-8">
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

            <form action="{{ route('admin.settings.shipping-timers.update') }}" method="POST">
                @csrf
                @method('PATCH')

                {{-- Working hours --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-business-time text-primary me-1"></i>
                            {{ $text('Working Hours', 'ساعات العمل') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label" for="working_hours_start">
                                    {{ $text('Start time', 'بداية ساعات العمل') }}
                                </label>
                                <input type="time" class="form-control{{ $hasError('working_hours_start') }}"
                                    id="working_hours_start" name="working_hours_start"
                                    value="{{ $oldOr('working_hours_start', $values['working_hours_start'] ?? '10:00') }}">
                                @error('working_hours_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    {{ $text('Used by jobs 1 & 2 to count only working hours.', 'تستخدمها المهمتان 1 و2 لاحتساب ساعات العمل فقط.') }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="working_hours_end">
                                    {{ $text('End time', 'نهاية ساعات العمل') }}
                                </label>
                                <input type="time" class="form-control{{ $hasError('working_hours_end') }}"
                                    id="working_hours_end" name="working_hours_end"
                                    value="{{ $oldOr('working_hours_end', $values['working_hours_end'] ?? '22:00') }}">
                                @error('working_hours_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Timer values --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-hourglass-half text-warning me-1"></i>
                            {{ $text('Automatic Procedures', 'الإجراءات التلقائية') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="auto_reject_working_hours">
                                    {{ $text('Auto reject after (X) – working hours', 'الرفض التلقائي بعد (X) – ساعة عمل') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" min="1" class="form-control{{ $hasError('auto_reject_working_hours') }}"
                                        id="auto_reject_working_hours" name="auto_reject_working_hours"
                                        value="{{ $oldOr('auto_reject_working_hours', $values['auto_reject_working_hours'] ?? 7) }}">
                                    <span class="input-group-text">{{ $text('hours', 'ساعة') }}</span>
                                </div>
                                @error('auto_reject_working_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="response_window_working_hours">
                                    {{ $text('Response window (Y) – working hours', 'نافذة الاستجابة (Y) – ساعة عمل') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" min="1" class="form-control{{ $hasError('response_window_working_hours') }}"
                                        id="response_window_working_hours" name="response_window_working_hours"
                                        value="{{ $oldOr('response_window_working_hours', $values['response_window_working_hours'] ?? 3) }}">
                                    <span class="input-group-text">{{ $text('hours', 'ساعة') }}</span>
                                </div>
                                @error('response_window_working_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="cancel_unpaid_advance_hours">
                                    {{ $text('Cancel unpaid advance (Z) – real hours', 'إلغاء الدفعة المقدمة (Z) – ساعة فعلية') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" min="1" class="form-control{{ $hasError('cancel_unpaid_advance_hours') }}"
                                        id="cancel_unpaid_advance_hours" name="cancel_unpaid_advance_hours"
                                        value="{{ $oldOr('cancel_unpaid_advance_hours', $values['cancel_unpaid_advance_hours'] ?? 12) }}">
                                    <span class="input-group-text">{{ $text('hours', 'ساعة') }}</span>
                                </div>
                                @error('cancel_unpaid_advance_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="execution_start_hours">
                                    {{ $text('Execution start (W) – real hours', 'بدء التنفيذ (W) – ساعة فعلية') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" min="1" class="form-control{{ $hasError('execution_start_hours') }}"
                                        id="execution_start_hours" name="execution_start_hours"
                                        value="{{ $oldOr('execution_start_hours', $values['execution_start_hours'] ?? 24) }}">
                                    <span class="input-group-text">{{ $text('hours', 'ساعة') }}</span>
                                </div>
                                @error('execution_start_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="aggregate_sub_shipments_days">
                                    {{ $text('Aggregate sub-shipments (N) – days', 'تجميع الشحنات الفرعية (N) – يوم') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" min="1" class="form-control{{ $hasError('aggregate_sub_shipments_days') }}"
                                        id="aggregate_sub_shipments_days" name="aggregate_sub_shipments_days"
                                        value="{{ $oldOr('aggregate_sub_shipments_days', $values['aggregate_sub_shipments_days'] ?? 3) }}">
                                    <span class="input-group-text">{{ $text('days', 'يوم') }}</span>
                                </div>
                                @error('aggregate_sub_shipments_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="aggregate_sub_shipments_scope">
                                    {{ $text('Aggregation scope', 'نطاق التجميع') }}
                                </label>
                                <select class="form-select{{ $hasError('aggregate_sub_shipments_scope') }}"
                                    id="aggregate_sub_shipments_scope" name="aggregate_sub_shipments_scope">
                                    <option value="all" @selected(($values['aggregate_sub_shipments_scope'] ?? 'all') === 'all')>
                                        {{ $text('All warehouses', 'كل المستودعات') }}
                                    </option>
                                    <option value="warehouse" @selected(($values['aggregate_sub_shipments_scope'] ?? '') === 'warehouse')>
                                        {{ $text('One warehouse', 'مستودع واحد') }}
                                    </option>
                                    <option value="group" @selected(($values['aggregate_sub_shipments_scope'] ?? '') === 'group')>
                                        {{ $text('One governorate group', 'مجموعة محافظة واحدة') }}
                                    </option>
                                </select>
                                @error('aggregate_sub_shipments_scope')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 d-none" id="targetWarehouseField">
                                <label class="form-label" for="aggregate_sub_shipments_target_id">
                                    {{ $text('Warehouse', 'المستودع') }}
                                </label>
                                <select class="form-select{{ $hasError('aggregate_sub_shipments_target_id') }}"
                                    id="aggregate_sub_shipments_target_id" name="aggregate_sub_shipments_target_id">
                                    <option value="">-- {{ $text('Select warehouse', 'اختر المستودع') }} --</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected((int) old('aggregate_sub_shipments_target_id', $values['aggregate_sub_shipments_target_id'] ?? 0) === (int) $warehouse->id)>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('aggregate_sub_shipments_target_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 d-none" id="targetGroupField">
                                <label class="form-label" for="aggregate_sub_shipments_group_id">
                                    {{ $text('Governorate', 'المحافظة') }}
                                </label>
                                <select class="form-select{{ $hasError('aggregate_sub_shipments_target_id') }}"
                                    id="aggregate_sub_shipments_group_id" name="aggregate_sub_shipments_target_id">
                                    <option value="">-- {{ $text('Select governorate', 'اختر المحافظة') }} --</option>
                                    @foreach ($governorates as $governorate)
                                        <option value="{{ $governorate->id }}" @selected((int) old('aggregate_sub_shipments_target_id', $values['aggregate_sub_shipments_target_id'] ?? 0) === (int) $governorate->id)>
                                            {{ $governorate->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('aggregate_sub_shipments_target_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> {{ $text('Save timers', 'حفظ الإعدادات') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#resetModal">
                        <i class="fas fa-undo me-1"></i> {{ $text('Reset to defaults', 'استعادة القيم الافتراضية') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="fas fa-info-circle text-info me-1"></i>
                        {{ $text('Last update', 'آخر تحديث') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if ($lastUpdated)
                        <p class="mb-1"><strong>{{ $text('By:', 'بواسطة:') }}</strong>
                            {{ $lastUpdatedName ?? '#' . $lastUpdated->updated_by }}</p>
                        <p class="mb-0 text-muted">{{ $lastUpdated->updated_at->format('Y-m-d H:i') }}</p>
                    @else
                        <p class="text-muted mb-0">{{ $text('No manual update yet.', 'لا يوجد تحديث يدوي بعد.') }}</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="fas fa-list-check text-success me-1"></i>
                        {{ $text('What these timers control', 'ماذا تتحكم هذه القيم') }}
                    </h6>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item small">
                        <strong>X = {{ $values['auto_reject_working_hours'] ?? 7 }}</strong> –
                        {{ $text('auto reject offers without a response (working hours).', 'رفض العروض دون استجابة (ساعات عمل).') }}
                    </li>
                    <li class="list-group-item small">
                        <strong>Y = {{ $values['response_window_working_hours'] ?? 3 }}</strong> –
                        {{ $text('close the response window after the first acceptance (working hours).', 'إغلاق نافذة الاستجابة بعد أول قبول (ساعات عمل).') }}
                    </li>
                    <li class="list-group-item small">
                        <strong>Z = {{ $values['cancel_unpaid_advance_hours'] ?? 12 }}</strong> –
                        {{ $text('cancel User requests with an unpaid advance (real hours).', 'إلغاء طلبات المستخدمين ذات الدفعة المقدمة غير المسددة (ساعات فعلية).') }}
                    </li>
                    <li class="list-group-item small">
                        <strong>N = {{ $values['aggregate_sub_shipments_days'] ?? 3 }}</strong> –
                        {{ $text('aggregate open sub-shipments by receiver warehouse (days).', 'تجميع الشحنات الفرعية المفتوحة حسب مستودع المستلم (أيام).') }}
                    </li>
                    <li class="list-group-item small">
                        <strong>W = {{ $values['execution_start_hours'] ?? 24 }}</strong> –
                        {{ $text('start execution of confirmed inter-governorate requests (real hours).', 'بدء تنفيذ الطلبات المؤكدة بين المحافظات (ساعات فعلية).') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Reset confirmation modal --}}
    <div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $text('Reset timers?', 'استعادة القيم الافتراضية؟') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ $text('This will restore the default timer values. The change will be recorded with your account.', 'سيؤدي هذا إلى استعادة قيم المؤقتات الافتراضية. سيتم تسجيل التغيير باسم حسابك.') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $text('Cancel', 'إلغاء') }}</button>
                    <form action="{{ route('admin.settings.shipping-timers.reset') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">{{ $text('Reset', 'استعادة') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var scope = document.getElementById('aggregate_sub_shipments_scope');
            var warehouseField = document.getElementById('targetWarehouseField');
            var groupField = document.getElementById('targetGroupField');

            function sync() {
                if (!scope) return;
                var value = scope.value;
                warehouseField && warehouseField.classList.toggle('d-none', value !== 'warehouse');
                groupField && groupField.classList.toggle('d-none', value !== 'group');
            }

            scope && scope.addEventListener('change', sync);
            sync();
        })();
    </script>
@endsection
