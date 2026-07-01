@php
    use App\Enum\ShipmentRequestStatus;
    $statusValue = $shipmentRequest->status?->value ?? 'unknown';
    $statusColor = match ($statusValue) {
        'submitted' => 'warning',
        'draft' => 'secondary',
        'assigned' => 'info',
        'accepted' => 'primary',
        'picked_up' => 'dark',
        'in_transit' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'rejected' => 'danger',
        default => 'secondary',
    };
    $sender = $shipmentRequest->senderContact;
    $receiver = $shipmentRequest->receiverContact;
    $senderAddress = $sender?->primaryAddress;
    $receiverAddress = $receiver?->primaryAddress;
@endphp

@extends('layouts.admin')

@section('title', __('admin-dashboard.shipment_request_details'))
@section('page-title', __('admin-dashboard.shipment_request_details') . ' - ' . ($shipmentRequest->request_number ?? '#'.$shipmentRequest->id))

@section('page-actions')
    <a href="{{ route('admin.shipment-requests.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>
        {{ __('admin-dashboard.back_to_requests') }}
    </a>
@endsection

@section('content')
<div class="row">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 fw-bold">{{ $shipmentRequest->request_number ?? '#' . $shipmentRequest->id }}</h4>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="status-pill btn-{{ $statusColor }} mb-0">
                            <span class="status-dot"></span>
                            {{ __('admin-dashboard.' . $statusValue) !== 'admin-dashboard.' . $statusValue ? __('admin-dashboard.' . $statusValue) : ucfirst(str_replace('_', ' ', $statusValue)) }}
                        </span>
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ $shipmentRequest->created_at?->format('M d, Y H:i') ?? '--' }}
                        </small>
                        @if($shipmentRequest->updated_at && $shipmentRequest->updated_at->ne($shipmentRequest->created_at))
                            <small class="text-muted">
                                <i class="fas fa-edit me-1"></i>
                                {{ $shipmentRequest->updated_at->format('M d, Y H:i') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= REQUEST SUMMARY ================= --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle text-primary me-2"></i>{{ __('admin-dashboard.request_summary') }}</h5>
            </div>
            <div class="card-body pt-3">
                <table class="table table-borderless mb-0 summary-table">
                    <tbody>
                        <tr>
                            <td class="text-muted w-35">{{ __('admin-dashboard.customer') }}</td>
                            <td class="fw-semibold">{{ $shipmentRequest->user->username ?? __('admin-dashboard.guest') }}</td>
                        </tr>
                        @if($shipmentRequest->user)
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.phone') }}</td>
                            <td>{{ $shipmentRequest->user->phone ?? '--' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.email') }}</td>
                            <td>{{ $shipmentRequest->user->email ?? '--' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.status') }}</td>
                            <td><span class="status-pill btn-{{ $statusColor }}" style="min-height:28px;font-size:0.8rem;"><span class="status-dot"></span>{{ __('admin-dashboard.' . $statusValue) !== 'admin-dashboard.' . $statusValue ? __('admin-dashboard.' . $statusValue) : ucfirst(str_replace('_', ' ', $statusValue)) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.packages') }}</td>
                            <td>{{ $shipmentRequest->packages->count() }} {{ __('admin-dashboard.package_unit') }}</td>
                        </tr>
                        @if($shipmentRequest->submitted_at)
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.submitted_at') }}</td>
                            <td>{{ $shipmentRequest->submitted_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= ASSIGNMENT SUMMARY ================= --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-truck text-success me-2"></i>{{ __('admin-dashboard.shipping_assignment') }}</h5>
            </div>
            <div class="card-body pt-3">
                <table class="table table-borderless mb-0 summary-table">
                    <tbody>
                        <tr>
                            <td class="text-muted w-35">{{ __('admin-dashboard.suggested_company') }}</td>
                            <td class="fw-semibold">{{ __('admin-dashboard.not_available_yet') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.assigned_company') }}</td>
                            <td class="fw-semibold">{{ __('admin-dashboard.not_available_yet') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.representative') }}</td>
                            <td>{{ __('admin-dashboard.not_available_yet') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.payment_status') }}</td>
                            <td><span class="badge bg-secondary">{{ __('admin-dashboard.not_available_yet') }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.estimated_price') }}</td>
                            <td>--</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('admin-dashboard.final_price') }}</td>
                            <td>--</td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-info mt-2 mb-0 py-2 small">
                    <i class="fas fa-info-circle me-1"></i>
                    {{ app()->getLocale() === 'ar' ? 'بيانات التعيين ستكون متاحة في التحديثات القادمة للمرحلة الثانية.' : 'Assignment data will be available in upcoming Phase 2 updates.' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ================= SENDER INFORMATION ================= --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-user-circle text-warning me-2"></i>{{ __('admin-dashboard.sender_information') }}</h5>
            </div>
            <div class="card-body pt-3">
                @if ($sender)
                    <table class="table table-borderless mb-0 summary-table">
                        <tbody>
                            <tr><td class="text-muted w-35">{{ __('admin-dashboard.name') }}</td><td class="fw-semibold">{{ $sender->full_name ?? '--' }}</td></tr>
                            <tr><td class="text-muted">{{ __('admin-dashboard.phone') }}</td><td>{{ $sender->primary_mobile ?? '--' }}</td></tr>
                            <tr><td class="text-muted">{{ __('admin-dashboard.secondary_phone') }}</td><td>{{ $sender->secondary_mobile ?? '--' }}</td></tr>
                            @if ($senderAddress)
                                <tr><td class="text-muted">{{ __('admin-dashboard.address') }}</td><td>{{ $senderAddress->address_line_1 ?? '--' }} {{ $senderAddress->address_line_2 ?? '' }}</td></tr>
                                <tr><td class="text-muted">{{ __('admin-dashboard.governorate') }}</td><td>{{ $senderAddress->governorate?->name ?? '--' }}</td></tr>
                                <tr><td class="text-muted">{{ __('admin-dashboard.city') }}</td><td>{{ $senderAddress->city?->name ?? '--' }}</td></tr>
                                <tr><td class="text-muted">{{ __('admin-dashboard.area_zone') }}</td><td>{{ $senderAddress->zone?->name ?? '--' }}</td></tr>
                                <tr><td class="text-muted">{{ __('admin-dashboard.landmark') }}</td><td>{{ $senderAddress->landmark ?? '--' }}</td></tr>
                                @if($senderAddress->latitude && $senderAddress->longitude)
                                <tr><td class="text-muted">{{ __('admin-dashboard.location') }}</td>
                                    <td>
                                        <a href="https://www.google.com/maps?q={{ $senderAddress->latitude }},{{ $senderAddress->longitude }}" target="_blank" class="text-primary">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $senderAddress->latitude }}, {{ $senderAddress->longitude }}
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            @else
                                <tr><td colspan="2" class="text-muted small">{{ __('admin-dashboard.no_address_provided') }}</td></tr>
                            @endif
                        </tbody>
                    </table>
                @else
                    <p class="text-muted small mb-0">{{ __('admin-dashboard.sender_not_provided') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= RECEIVER INFORMATION ================= --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-user-circle text-info me-2"></i>{{ __('admin-dashboard.receiver_information') }}</h5>
            </div>
            <div class="card-body pt-3">
                @if ($receiver)
                    <table class="table table-borderless mb-0 summary-table">
                        <tbody>
                            <tr><td class="text-muted w-35">{{ __('admin-dashboard.name') }}</td><td class="fw-semibold">{{ $receiver->full_name ?? '--' }}</td></tr>
                            <tr><td class="text-muted">{{ __('admin-dashboard.phone') }}</td><td>{{ $receiver->primary_mobile ?? '--' }}</td></tr>
                            <tr><td class="text-muted">{{ __('admin-dashboard.secondary_phone') }}</td><td>{{ $receiver->secondary_mobile ?? '--' }}</td></tr>
                            @if ($receiverAddress)
                                <tr><td class="text-muted">{{ __('admin-dashboard.address') }}</td><td>{{ $receiverAddress->address_line_1 ?? '--' }} {{ $receiverAddress->address_line_2 ?? '' }}</td></tr>
                                <tr><td class="text-muted">{{ __('admin-dashboard.governorate') }}</td><td>{{ $receiverAddress->governorate?->name ?? '--' }}</td></tr>
                                <tr><td class="text-muted">{{ __('admin-dashboard.city') }}</td><td>{{ $receiverAddress->city?->name ?? '--' }}</td></tr>
                                <tr><td class="text-muted">{{ __('admin-dashboard.area_zone') }}</td><td>{{ $receiverAddress->zone?->name ?? '--' }}</td></tr>
                                <tr><td class="text-muted">{{ __('admin-dashboard.landmark') }}</td><td>{{ $receiverAddress->landmark ?? '--' }}</td></tr>
                                @if($receiverAddress->latitude && $receiverAddress->longitude)
                                <tr><td class="text-muted">{{ __('admin-dashboard.location') }}</td>
                                    <td>
                                        <a href="https://www.google.com/maps?q={{ $receiverAddress->latitude }},{{ $receiverAddress->longitude }}" target="_blank" class="text-primary">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $receiverAddress->latitude }}, {{ $receiverAddress->longitude }}
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            @else
                                <tr><td colspan="2" class="text-muted small">{{ __('admin-dashboard.no_address_provided') }}</td></tr>
                            @endif
                        </tbody>
                    </table>
                @else
                    <p class="text-muted small mb-0">{{ __('admin-dashboard.receiver_not_provided') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= PACKAGE INFORMATION ================= --}}
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-box text-secondary me-2"></i>{{ __('admin-dashboard.package_information') }}</h5>
            </div>
            <div class="card-body pt-3">
                @if ($shipmentRequest->packages->count() > 0)
                    <div class="row g-3">
                        @foreach ($shipmentRequest->packages as $pkg)
                            <div class="col-lg-6 col-xl-4">
                                <div class="border rounded-3 p-3 h-100 package-card">
                                    <h6 class="fw-bold mb-2">{{ $pkg->package_name ?? __('admin-dashboard.package') . ' #' . $loop->iteration }}</h6>
                                    <table class="table table-borderless mb-0 small summary-table">
                                        <tbody>
                                            @if($pkg->package_type)
                                            <tr><td class="text-muted w-40">{{ __('admin-dashboard.package_type') }}</td><td>{{ $pkg->package_type }}</td></tr>
                                            @endif
                                            @if($pkg->weight)
                                            <tr><td class="text-muted">{{ __('admin-dashboard.weight') }}</td><td>{{ $pkg->weight }} {{ app()->getLocale() === 'ar' ? 'كجم' : 'kg' }}</td></tr>
                                            @endif
                                            @if($pkg->length || $pkg->width || $pkg->height)
                                            <tr><td class="text-muted">{{ __('admin-dashboard.dimensions') }}</td><td>{{ $pkg->length ?? '--' }} x {{ $pkg->width ?? '--' }} x {{ $pkg->height ?? '--' }} {{ app()->getLocale() === 'ar' ? 'سم' : 'cm' }}</td></tr>
                                            @endif
                                            @if($pkg->quantity)
                                            <tr><td class="text-muted">{{ __('admin-dashboard.quantity') }}</td><td>{{ $pkg->quantity }}</td></tr>
                                            @endif
                                            @if($pkg->declared_value)
                                            <tr><td class="text-muted">{{ __('admin-dashboard.declared_value') }}</td><td>{{ __('admin-dashboard.EGP') }} {{ number_format($pkg->declared_value, 2) }}</td></tr>
                                            @endif
                                            @if($pkg->notes)
                                            <tr><td class="text-muted">{{ __('admin-dashboard.notes') }}</td><td class="text-wrap">{{ $pkg->notes }}</td></tr>
                                            @endif
                                        </tbody>
                                    </table>

                                    {{-- Special handling badges --}}
                                    @php
                                        $meta = $pkg->metadata ?? [];
                                        $badges = [];
                                        if (!empty($meta['is_fragile'])) $badges[] = ['label' => app()->getLocale() === 'ar' ? 'قابل للكسر' : 'Fragile', 'class' => 'bg-danger'];
                                        if (!empty($meta['needs_cooling'])) $badges[] = ['label' => app()->getLocale() === 'ar' ? 'تبريد' : 'Cooling', 'class' => 'bg-info'];
                                        if (!empty($meta['is_valuable'])) $badges[] = ['label' => app()->getLocale() === 'ar' ? 'ثمين' : 'Valuable', 'class' => 'bg-warning text-dark'];
                                        if (!empty($meta['is_documents'])) $badges[] = ['label' => app()->getLocale() === 'ar' ? 'مستندات' : 'Documents', 'class' => 'bg-secondary'];
                                        if (!empty($meta['is_heavy'])) $badges[] = ['label' => app()->getLocale() === 'ar' ? 'ثقيل' : 'Heavy', 'class' => 'bg-dark'];
                                    @endphp
                                    @if(count($badges) > 0)
                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                            @foreach($badges as $badge)
                                                <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Package images --}}
                                    @php
                                        $images = $pkg->mediaFiles ?? collect();
                                    @endphp
                                    @if($images->count() > 0)
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @foreach($images as $img)
                                                <a href="{{ $img->url ?? '#' }}" target="_blank">
                                                    <img src="{{ $img->url ?? '#' }}" alt="{{ __('admin-dashboard.package_image') }}" class="rounded" style="width:60px;height:60px;object-fit:cover;border:1px solid #e2e8f0;">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small mb-0">{{ __('admin-dashboard.no_packages_provided') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= STATUS TIMELINE ================= --}}
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-clock text-primary me-2"></i>{{ __('admin-dashboard.status_timeline') }}</h5>
            </div>
            <div class="card-body pt-3">
                @php
                    $allStatuses = [
                        'draft' => ['label' => __('admin-dashboard.draft'), 'icon' => 'fa-pen'],
                        'submitted' => ['label' => __('admin-dashboard.submitted'), 'icon' => 'fa-paper-plane'],
                        'assigned' => ['label' => __('admin-dashboard.assigned'), 'icon' => 'fa-user-check'],
                        'accepted' => ['label' => __('admin-dashboard.accepted'), 'icon' => 'fa-check-circle'],
                        'picked_up' => ['label' => __('admin-dashboard.picked_up'), 'icon' => 'fa-box-open'],
                        'in_transit' => ['label' => __('admin-dashboard.in_transit'), 'icon' => 'fa-truck'],
                        'delivered' => ['label' => __('admin-dashboard.delivered'), 'icon' => 'fa-house-chimney'],
                    ];
                    $currentIndex = array_search($statusValue, array_keys($allStatuses));
                    $isTerminal = in_array($statusValue, ['cancelled', 'rejected']);
                @endphp

                <div class="timeline-wrapper">
                    @if($isTerminal)
                        <div class="text-center py-4">
                            <span class="badge bg-danger fs-6 px-4 py-2">
                                <i class="fas {{ $statusValue === 'cancelled' ? 'fa-ban' : 'fa-times-circle' }} me-2"></i>
                                {{ __('admin-dashboard.' . $statusValue) !== 'admin-dashboard.' . $statusValue ? __('admin-dashboard.' . $statusValue) : ucfirst($statusValue) }}
                            </span>
                            <p class="text-muted mt-2 small mb-0">{{ app()->getLocale() === 'ar' ? 'هذه نهاية رحلة الطلب.' : 'This is the end of the request journey.' }}</p>
                        </div>
                    @else
                        <div class="timeline-steps">
                            @foreach($allStatuses as $key => $step)
                                @php
                                    $stepIndex = array_search($key, array_keys($allStatuses));
                                    $isCompleted = $stepIndex <= $currentIndex && $currentIndex !== false;
                                    $isCurrent = $stepIndex === $currentIndex;
                                @endphp
                                <div class="timeline-step {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'current' : '' }}">
                                    <div class="timeline-icon {{ $isCompleted ? 'bg-primary text-white' : 'bg-light text-muted' }} {{ $isCurrent ? 'ring-primary' : '' }}">
                                        <i class="fas {{ $step['icon'] }}"></i>
                                    </div>
                                    <div class="timeline-label {{ $isCurrent ? 'fw-bold text-primary' : ($isCompleted ? 'text-dark' : 'text-muted') }}">
                                        {{ $step['label'] }}
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <div class="timeline-connector {{ $isCompleted ? 'active' : '' }}"></div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="alert alert-light mt-3 mb-0 py-2 small">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ app()->getLocale() === 'ar'
                            ? 'هذا جدول زمني مبني على الحالة الحالية. سيتم تفعيل السجل الكامل للتحديثات في تحديث قادم.'
                            : 'This timeline is built from the current status. Full status history will be available in a future update.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= ADMIN NOTES ================= --}}
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-sticky-note text-secondary me-2"></i>{{ __('admin-dashboard.notes_reasons') }}</h5>
            </div>
            <div class="card-body pt-3">
                @if($shipmentRequest->notes)
                    <div class="mb-2">
                        <strong class="text-muted small">{{ __('admin-dashboard.request_notes') }}:</strong>
                        <p class="mb-0 mt-1">{{ $shipmentRequest->notes }}</p>
                    </div>
                @else
                    <p class="text-muted small mb-0">{{ __('admin-dashboard.no_notes_provided') }}</p>
                @endif
            </div>
        </div>
    </div>

</div>

<style>
    .summary-table td { padding: 0.4rem 0; vertical-align: top; }
    .summary-table .w-35 { width: 35%; }
    .summary-table .w-40 { width: 40%; }
    .package-card { background: #fafbfc; border-color: #e5e7eb !important; transition: box-shadow 0.2s ease; }
    .package-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .status-pill {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding-inline: 0.85rem; border-radius: 999px;
        min-height: 36px; font-weight: 600;
        box-shadow: 0 8px 20px rgba(15,23,42,0.06);
    }
    .status-pill.btn-success { background-color: #10b981 !important; color: white !important; }
    .status-pill.btn-warning { background-color: #f59e0b !important; color: white !important; }
    .status-pill.btn-danger { background-color: #ef4444 !important; color: white !important; }
    .status-pill.btn-info { background-color: #3b82f6 !important; color: white !important; }
    .status-pill.btn-primary { background-color: #6366f1 !important; color: white !important; }
    .status-pill.btn-dark { background-color: #1e293b !important; color: white !important; }
    .status-pill.btn-secondary { background-color: #94a3b8 !important; color: white !important; }
    .status-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: currentColor; box-shadow: 0 0 0 3px rgba(255,255,255,0.42);
        flex: 0 0 auto;
    }
    .timeline-wrapper { overflow-x: auto; padding: 0.5rem 0; }
    .timeline-steps { display: flex; align-items: flex-start; min-width: max-content; padding: 1rem 0; }
    .timeline-step { display: flex; flex-direction: column; align-items: center; min-width: 90px; }
    .timeline-icon {
        width: 44px; height: 44px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; transition: all 0.3s ease;
    }
    .timeline-icon.ring-primary { box-shadow: 0 0 0 4px rgba(99,102,241,0.2); }
    .timeline-label { font-size: 0.78rem; text-align: center; margin-top: 0.5rem; white-space: nowrap; }
    .timeline-connector {
        flex: 1; height: 3px; background: #e5e7eb; margin-top: 22px; min-width: 30px;
        transition: background 0.3s ease;
    }
    .timeline-connector.active { background: #6366f1; }
    @media (max-width: 767.98px) {
        .summary-table .w-35, .summary-table .w-40 { width: 45%; }
    }
</style>
