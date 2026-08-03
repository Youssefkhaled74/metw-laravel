@extends('layouts.admin')

@section('title', __('admin-dashboard.advance_payments'))
@section('page-title', __('admin-dashboard.advance_payments'))

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('admin-dashboard.advance_payments') }}</h5>
            <form class="d-flex gap-2" method="GET" action="{{ route('admin.advance-payments.index') }}">
                <select name="status" class="form-select form-select-sm" style="width: auto;">
                    <option value="">{{ __('admin-dashboard.all') }}</option>
                    @foreach (['paid', 'confirmed', 'rejected'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control form-control-sm" style="width: 200px;"
                    placeholder="{{ __('admin-dashboard.search') }}">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-filter"></i>
                </button>
            </form>
        </div>
        <div class="card-body">
            @if ($payments->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin-dashboard.request') }}</th>
                                <th>{{ __('admin-dashboard.path') }}</th>
                                <th>{{ __('admin-dashboard.submitter') }}</th>
                                <th>{{ __('admin-dashboard.amount') }}</th>
                                <th>{{ __('admin-dashboard.method') }}</th>
                                <th>{{ __('admin-dashboard.reference') }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                @php
                                    $paymentStatus = $payment->status?->value ?? $payment->status;
                                    $submitter = $payment->submitter;
                                    $submitterName = $submitter
                                        ? ($submitter->name ?? $submitter->username ?? $submitter->full_name ?? ('#' . $submitter->id))
                                        : ('#' . $payment->submitter_id);
                                @endphp
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>
                                        @if ($payment->requestPath && $payment->requestPath->pathable instanceof \App\Models\ShipmentRequest)
                                            <a href="{{ route('admin.shipment-requests.show', $payment->requestPath->pathable->id) }}">
                                                {{ $payment->requestPath->pathable->request_number }}
                                            </a>
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td>{{ $payment->requestPath ? ($payment->requestPath->type?->value ?? $payment->requestPath->type) : '--' }}</td>
                                    <td>{{ $submitterName }}</td>
                                    <td>{{ (float) $payment->amount }} {{ $payment->currency ?? 'EGP' }}</td>
                                    <td>{{ $payment->payment_method ?? '--' }}</td>
                                    <td>{{ $payment->reference ?? '--' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $paymentStatus === 'confirmed' ? 'success' : ($paymentStatus === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $paymentStatus }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($paymentStatus === 'paid')
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.advance-payments.confirm', $payment->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('{{ __('admin-dashboard.confirm_advance_payment_confirm') }}');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i> {{ __('admin-dashboard.confirm') }}
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectPaymentModal{{ $payment->id }}">
                                                    <i class="fas fa-times"></i> {{ __('admin-dashboard.reject') }}
                                                </button>
                                            </div>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>

                                @if ($paymentStatus === 'paid')
                                    <div class="modal fade" id="rejectPaymentModal{{ $payment->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('admin-dashboard.reject_advance_payment') }} #{{ $payment->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.advance-payments.reject', $payment->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="note_{{ $payment->id }}" class="form-label">
                                                                {{ __('admin-dashboard.rejection_note') }}
                                                            </label>
                                                            <textarea class="form-control" id="note_{{ $payment->id }}" name="note" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            {{ __('admin-dashboard.cancel') }}
                                                        </button>
                                                        <button type="submit" class="btn btn-danger">
                                                            {{ __('admin-dashboard.reject') }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $payments->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-money-check-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('admin-dashboard.no_advance_payments_found') }}</h5>
                </div>
            @endif
        </div>
    </div>
@endsection
