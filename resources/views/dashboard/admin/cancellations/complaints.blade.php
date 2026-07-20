@extends('layouts.admin')

@section('title', 'شكاوى إلغاء طلبات')
@section('page-title', 'قسم شكاوى إلغاء طلبات')

@section('page-actions')
    <a href="{{ route('admin.cancellations.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> العودة لطلبات الإلغاء
    </a>
@endsection

@section('content')
    <div class="cancel-card">
        <div class="cancel-card-header">
            <div>
                <h5><i class="fas fa-exclamation-triangle me-2 text-warning"></i> شكاوى إلغاء طلبات</h5>
                <p class="text-muted mb-0 mt-1" style="font-size:.85rem;">الشكاوى المقدمة من العملاء حول طلبات الإلغاء التي تحتاج متابعة</p>
            </div>
            <span class="badge bg-warning rounded-pill">{{ $cancellations->total() }} شكوى</span>
        </div>

        <div class="cancel-card-body">
            <form method="GET" action="{{ route('admin.cancellations.complaints') }}" class="cancel-filter-form">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-6">
                        <div class="cancel-search">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="ابحث برقم الإلغاء، رقم الطلب، اسم العميل..." class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i> تطبيق
                        </button>
                        <a href="{{ route('admin.cancellations.complaints') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>

            @if ($cancellations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>رقم طلب الإلغاء</th>
                                <th>رقم الطلب</th>
                                <th>رقم العميل</th>
                                <th>رقم البائع</th>
                                <th>تفاصيل الشكوى</th>
                                <th>حالة الطلب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cancellations as $returnRequest)
                                @php
                                    $seller = $returnRequest->items->first()?->orderItem?->product?->vendor;
                                    $statusValue = is_object($returnRequest->status) ? $returnRequest->status->value : (string) $returnRequest->status;
                                    $statusColor = match($statusValue) {
                                        'completed', 'approved' => 'success',
                                        'requested', 'pickup', 'processing' => 'warning',
                                        'rejected', 'cancelled' => 'danger',
                                        default => 'info',
                                    };
                                    $complaints = $returnRequest->complaints;
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $returnRequest->return_number }}</span>
                                    </td>
                                    <td>{{ $returnRequest->order->order_number ?? '-' }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $returnRequest->user->username ?? '-' }}</span>
                                        <br><small class="text-muted">USR-{{ str_pad($returnRequest->user_id, 8, '0', STR_PAD_LEFT) }}</small>
                                    </td>
                                    <td>
                                        @if ($seller)
                                            <span class="fw-semibold">{{ $seller->name ?? '-' }}</span>
                                            <br><small class="text-muted">VDR-{{ str_pad($seller->id, 8, '0', STR_PAD_LEFT) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($complaints->count() > 0)
                                            @foreach ($complaints as $complaint)
                                                <div class="complaint-item mb-1">
                                                    <span class="badge bg-light text-dark border">{{ $complaint->title ?? 'شكوى' }}</span>
                                                    @if ($complaint->description)
                                                        <br><small class="text-muted">{{ Str::limit($complaint->description, 80) }}</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="cancel-status-badge badge bg-{{ $statusColor }}">
                                            {{ $statusValue }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="{{ route('admin.cancellations.show', $returnRequest->id) }}" 
                                               class="btn btn-sm btn-primary" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning text-white" 
                                                    data-bs-toggle="modal" data-bs-target="#reactivateModal{{ $returnRequest->id }}"
                                                    title="إعادة تفعيل طلب الإلغاء">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </div>

                                        <!-- Reactivate Modal -->
                                        <div class="modal fade" id="reactivateModal{{ $returnRequest->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('admin.cancellations.reactivate', $returnRequest->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">إعادة تفعيل طلب الإلغاء</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-info">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                سيتم إعادة تفعيل طلب الإلغاء رقم <strong>{{ $returnRequest->return_number }}</strong>
                                                                وسيتم إرسال إشعار للبائع والعميل.
                                                            </div>
                                                            <p><strong>رقم الطلب:</strong> {{ $returnRequest->order->order_number ?? '-' }}</p>
                                                            <p><strong>العميل:</strong> {{ $returnRequest->user->username ?? '-' }}</p>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">سبب إعادة التفعيل <span class="text-danger">*</span></label>
                                                                <textarea name="reason" class="form-control" rows="3" required 
                                                                          placeholder="أدخل سبب إعادة تفعيل طلب الإلغاء..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-warning text-white">
                                                                <i class="fas fa-redo me-1"></i> إعادة تفعيل
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="cancel-empty">
                                            <i class="fas fa-check-circle"></i>
                                            <p>لا توجد شكاوى إلغاء حالياً</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $cancellations->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="cancel-empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h5>لا توجد شكاوى إلغاء</h5>
                    <p>جميع طلبات الإلغاء حالياً لا تحتوي على شكاوى.</p>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .cancel-card { background: #fff; border-radius: 22px; border: 1px solid #ECE6F2; box-shadow: 0 12px 30px rgba(15,23,42,.05); overflow: hidden; }
        .cancel-card-header { padding: 1.25rem; border-bottom: 1px solid #ECE6F2; display: flex; justify-content: space-between; align-items: flex-start; }
        .cancel-card-header h5 { margin: 0; font-weight: 800; color: #181022; }
        .cancel-card-body { padding: 1.25rem; }
        .cancel-search { position: relative; }
        .cancel-search i { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #7C7285; }
        .cancel-search input { border-radius: 12px; padding-right: 2.5rem; min-height: 44px; border-color: #ECE6F2; }
        .cancel-search input:focus { border-color: #7B00A8; box-shadow: 0 0 0 .2rem rgba(123,0,168,.1); }
        .cancel-status-badge { border-radius: 999px; padding: .35rem .85rem; font-weight: 600; font-size: .78rem; }
        .cancel-empty { padding: 2rem; color: #7C7285; }
        .cancel-empty i { font-size: 2rem; color: #35D36F; margin-bottom: .5rem; display: block; }
        .cancel-empty-state { text-align: center; padding: 3rem; }
        .cancel-empty-state i { font-size: 3rem; color: #35D36F; margin-bottom: 1rem; }
        .cancel-empty-state h5 { color: #181022; font-weight: 800; }
        .cancel-empty-state p { color: #7C7285; }
        .table th { background: #F7F5FA; color: #7C7285; font-weight: 700; font-size: .82rem; border-color: #ECE6F2; }
        .table td { border-color: #F0ECF5; vertical-align: middle; }
        .complaint-item { padding: .35rem .5rem; background: #FFF8E1; border-radius: 8px; }
    </style>
    @endpush
@endsection
