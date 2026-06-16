@extends('layouts.admin')

@section('title', __('admin-dashboard.price_per_km_settings'))
@section('page-title', __('admin-dashboard.price_per_km_settings'))

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.global_price_per_km_settings') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.price-per-km.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="price_per_km_min" class="form-label">{{ __('admin-dashboard.minimum_price_per_km') }}</label>
                                    <input type="number"
                                        class="form-control @error('price_per_km_min') is-invalid @enderror"
                                        id="price_per_km_min" name="price_per_km_min"
                                        value="{{ old('price_per_km_min', $pricePerKmMin?->value ?? '0.00') }}"
                                        step="0.01" min="0" required>
                                    @error('price_per_km_min')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="price_per_km_max" class="form-label">{{ __('admin-dashboard.maximum_price_per_km') }}</label>
                                    <input type="number"
                                        class="form-control @error('price_per_km_max') is-invalid @enderror"
                                        id="price_per_km_max" name="price_per_km_max"
                                        value="{{ old('price_per_km_max', $pricePerKmMax?->value ?? '100.00') }}"
                                        step="0.01" min="0" required>
                                    @error('price_per_km_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
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
                    <p class="text-muted">
                        {{ __('admin-dashboard.price_per_km_info') }}
                    </p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>{{ __('admin-dashboard.note') }}:</strong> {{ __('admin-dashboard.price_per_km_note') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('admin-dashboard.shipment_companies_price_per_km') }}</h5>
                    <a href="{{ route('admin.shipment-companies') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-building"></i> {{ __('admin-dashboard.manage_companies') }}
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $companies = \App\Models\ShipmentCompany::withCount(['packages', 'orders'])
                            ->latest()
                            ->paginate(10);
                    @endphp

                    @if (count($companies) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ __('admin-dashboard.company_name') }}</th>
                                        <th>{{ __('admin-dashboard.email') }}</th>
                                        <th>{{ __('admin-dashboard.phone') }}</th>
                                        <th>{{ __('admin-dashboard.price_per_km') }}</th>
                                        <th>{{ __('admin-dashboard.status') }}</th>
                                        <th>{{ __('admin-dashboard.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($companies as $company)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($company->logo)
                                                        <img src="{{ asset($company->logo) }}" alt="{{ $company->name }}"
                                                            class="rounded-circle me-2"
                                                            style="width: 32px; height: 32px; object-fit: cover;">
                                                    @endif
                                                    <span>{{ $company->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $company->email }}</td>
                                            <td>{{ $company->phone }}</td>
                                            <td>
                                                @if ($company->price_per_km)
                                                    <span
                                                        class="badge bg-success">${{ number_format($company->price_per_km, 2) }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('admin-dashboard.not_set') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                                                    {{ $company->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editPriceModal{{ $company->id }}">
                                                    <i class="fas fa-edit"></i> {{ __('admin-dashboard.edit_price') }}
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Edit Price Modal -->
                                        <div class="modal fade" id="editPriceModal{{ $company->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ __('admin-dashboard.edit_price_per_km') }} - {{ $company->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form
                                                        action="{{ route('admin.shipment-companies.update-price-per-km', $company->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-body">
                                                            <div class="form-group mb-3">
                                                                <label for="price_per_km_{{ $company->id }}"
                                                                    class="form-label">{{ __('admin-dashboard.price_per_km') }}</label>
                                                                <input type="number" class="form-control"
                                                                    id="price_per_km_{{ $company->id }}"
                                                                    name="price_per_km"
                                                                    value="{{ old('price_per_km', $company->price_per_km) }}"
                                                                    step="0.01" min="{{ $pricePerKmMin?->value ?? 0 }}"
                                                                    max="{{ $pricePerKmMax?->value ?? 100 }}" required>
                                                                <small class="form-text text-muted">
                                                                    {{ __('admin-dashboard.price_range_note', ['min' => $pricePerKmMin?->value ?? 0, 'max' => $pricePerKmMax?->value ?? 100]) }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __('admin-dashboard.cancel') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.update_price') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $companies->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('admin-dashboard.no_companies_found') }}</h5>
                            <p class="text-muted mb-0">{{ __('admin-dashboard.no_companies_message') }}</p>
                            <div class="mt-3">
                                <a href="{{ route('admin.create-shipment-company') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_first_company') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
