@extends('layouts.admin')

@section('title', __('admin-dashboard.courier_failure_messages'))
@section('page-title', __('admin-dashboard.courier_failure_messages'))

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.courier_failure_messages') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.courier-failure-messages.update') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="courier_failure_message_shipping" class="form-label">
                                {{ __('admin-dashboard.failure_message_shipping') }}
                            </label>
                            <textarea class="form-control @error('courier_failure_message_shipping') is-invalid @enderror"
                                id="courier_failure_message_shipping" name="courier_failure_message_shipping" rows="4">{{ old('courier_failure_message_shipping', $values['courier_failure_message_shipping'] ?? '') }}</textarea>
                            @error('courier_failure_message_shipping')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">{{ __('admin-dashboard.failure_message_shipping_hint') }}</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="courier_failure_message_delivery" class="form-label">
                                {{ __('admin-dashboard.failure_message_delivery') }}
                            </label>
                            <textarea class="form-control @error('courier_failure_message_delivery') is-invalid @enderror"
                                id="courier_failure_message_delivery" name="courier_failure_message_delivery" rows="4">{{ old('courier_failure_message_delivery', $values['courier_failure_message_delivery'] ?? '') }}</textarea>
                            @error('courier_failure_message_delivery')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">{{ __('admin-dashboard.failure_message_delivery_hint') }}</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('admin-dashboard.update_settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>{{ __('admin-dashboard.note') }}:</strong> {{ __('admin-dashboard.courier_failure_messages_note') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
