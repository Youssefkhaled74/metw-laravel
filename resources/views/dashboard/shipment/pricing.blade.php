@extends('layouts.shipment')

@section('title', __('shipment-dashboard.pricing_management'))
@section('page-title', __('shipment-dashboard.pricing_management'))

@section('content')
<div class="row">

    <div class="card shadow mb-4">
        <div class="card-header bg-secondary text-white">
            <h6 class="m-0">{{__('shipment-dashboard.rules')}}</h6>
        </div>

        <div class="card-body">
            <form action="{{ route('shipment.update-distance-factors') }}" method="POST">
                @csrf
                @method('PATCH')

                <table class="table table-bordered" id="distanceTable">
                    <thead>
                        <tr>
                            <th>{{ __('shipment-dashboard.Max(Km)') }}</th>
                            <th>{{ __('shipment-dashboard.Factor') }}</th>
                            <th>{{ __('shipment-dashboard.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                    @forelse($distanceRules as $i => $rule)
                        <tr>
                            <td>
                                <input type="number" step="1" name="rules[{{ $i }}][max]"
                                    value="{{ $rule['max'] }}" class="form-control" required>
                            </td>

                            <td>
                                <input type="number" step="0.01" name="rules[{{ $i }}][factor]"
                                    value="{{ $rule['factor'] }}" class="form-control" required>
                            </td>

                            <td>
                                <button type="button" class="btn btn-danger removeRule">{{ __('shipment-dashboard.delete') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">{{ __('shipment-dashboard.no-data') }}</td></tr>
                    @endforelse

                    </tbody>
                </table>

                <button type="button" class="btn btn-warning mt-2" id="addRule">
                    + {{ __('shipment-dashboard.add_rule') }}
                </button>

                <hr>

                <h6>{{ __('shipment-dashboard.village_factor') }}</h6>
                <input type="number" step="0.1" class="form-control"
                    name="village_factor" value="{{ $villageFactor }}" required>

                <hr>

                <h6>{{ __('shipment-dashboard.per_page_sizes') }}</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('shipment-dashboard.small') }}</label>
                        <input type="number" step="0.01" class="form-control"
                            name="perPage[small]" value="{{ $perPage['small'] ?? 1 }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('shipment-dashboard.medium') }}</label>
                        <input type="number" step="0.01" class="form-control"
                            name="perPage[medium]" value="{{ $perPage['medium'] ?? 2 }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('shipment-dashboard.large') }}</label>
                        <input type="number" step="0.01" class="form-control"
                            name="perPage[large]" value="{{ $perPage['large'] ?? 3 }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('shipment-dashboard.xlarge') }}</label>
                        <input type="number" step="0.01" class="form-control"
                            name="perPage[xlarge]" value="{{ $perPage['xlarge'] ?? 4 }}" required>
                    </div>
                </div>


                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('shipment-dashboard.save_rules') }}
                    </button>
                </div>

            </form>
        </div>
    </div>



    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    @lang('shipment-dashboard.category_pricing')
                </h6>
                <button type="button" class="btn btn-sm btn-success" id="addCategoryRow">
                    <i class="fas fa-plus"></i> @lang('shipment-dashboard.add_category_pricing')
                </button>
            </div>

            <div class="card-body">
                <form action="{{ route('shipment.update-price-per-km') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <table class="table table-bordered align-middle" id="pricingTable">
                        <thead class="table-light">
                        <tr>
                            <th width="25%">@lang('shipment-dashboard.category')</th>
                            <th width="20%">@lang('shipment-dashboard.pricing_type')</th>
                            <th width="35%">@lang('shipment-dashboard.price_value')</th>
                            <th width="20%">@lang('shipment-dashboard.actions')</th>
                        </tr>
                        </thead>
                        <tbody>

                        {{-- Existing prices --}}
                        @foreach($categoryPrices as $price)
                            @php
                                $category = $price->category;
                                $type = $category->type;
                            @endphp
                            <tr data-type="{{ $type }}" data-price-id="{{ $price->id }}">
                                <td>
                                    <input type="hidden" name="prices[{{ $price->id }}][id]" value="{{ $price->id }}">
                                    <input type="hidden" name="prices[{{ $price->id }}][category_id]" value="{{ $price->category_id }}">

                                    <strong>{{ $category->translation(app()->getLocale())->name ?? $category->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ __('shipment-dashboard.category') }} #{{ $category->id }}</small>
                                </td>

                                <td>
                                    <span class="badge
                                        @if($type === 'piece') bg-info
                                        @elseif($type === 'weight') bg-warning
                                        @else bg-success
                                        @endif">
                                        @if($type === 'piece') @lang('shipment-dashboard.per_piece')
                                        @elseif($type === 'weight') @lang('shipment-dashboard.per_weight')
                                        @else @lang('shipment-dashboard.weight_and_size')
                                        @endif
                                    </span>
                                </td>

                                <td>
                                    <div class="price-input-container" data-type="{{ $type }}">
                                        @if($type === 'piece')
                                            <div class="mb-1">
                                                <div class="input-group mb-1">
                                                    <input type="number" step="0.01" class="form-control"
                                                        value="{{ $perPage['small'] ?? 1 }}" readonly>
                                                    <span class="input-group-text">@lang('shipment-dashboard.small')</span>
                                                </div>

                                                <div class="input-group mb-1">
                                                    <input type="number" step="0.01" class="form-control"
                                                        value="{{ $perPage['medium'] ?? 2 }}" readonly>
                                                    <span class="input-group-text">@lang('shipment-dashboard.medium')</span>
                                                </div>

                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        value="{{ $perPage['large'] ?? 3 }}" readonly>
                                                    <span class="input-group-text">@lang('shipment-dashboard.large')</span>
                                                </div>

                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        value="{{ $perPage['xlarge'] ?? 4 }}" readonly>
                                                    <span class="input-group-text">@lang('shipment-dashboard.xlarge')</span>
                                                </div>
                                            </div>

                                            <small class="text-muted">
                                                {{ __('shipment-dashboard.per_piece_description') }} ({{ __('shipment-dashboard.config_value_used') }})
                                            </small>
                                        @elseif($type === 'weight')
                                            <div class="input-group mb-2">
                                                <input type="number" step="0.01" class="form-control"
                                                       name="prices[{{ $price->id }}][price_per_kg]"
                                                       value="{{ $price->price_per_kg }}" required>
                                                <span class="input-group-text">@lang('shipment-dashboard.currency_per_kg')</span>
                                            </div>
                                            <small class="text-muted">@lang('shipment-dashboard.per_kg_description')</small>
                                        @elseif($type === 'weight_size')
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label class="form-label small">@lang('shipment-dashboard.price_per_size')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control"
                                                               name="prices[{{ $price->id }}][price_per_size]"
                                                               value="{{ $price->price_per_size }}" required>
                                                        <span class="input-group-text">@lang('shipment-dashboard.currency')</span>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small">@lang('shipment-dashboard.price_per_kg')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control"
                                                               name="prices[{{ $price->id }}][price_per_kg]"
                                                               value="{{ $price->price_per_kg }}" required>
                                                        <span class="input-group-text">@lang('shipment-dashboard.currency_per_kg')</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm removeRow" data-id="{{ $price->id }}">
                                        <i class="fas fa-trash"></i> @lang('shipment-dashboard.remove')
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('shipment-dashboard.save_pricing')
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- New row template --}}
        <template id="categoryRowTemplate">
            <tr>
                <td>
                    <select name="new_prices[__INDEX__][category_id]"
                            class="form-select categorySelect" required>
                        <option value="" disabled selected>
                            @lang('shipment-dashboard.select_category')
                        </option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                    data-type="{{ $cat->type }}">
                                {{ $cat->translation(app()->getLocale())->name ?? $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </td>

                <td class="type-cell">
                    <span class="badge bg-secondary">@lang('shipment-dashboard.select_first')</span>
                </td>

                <td class="price-cell">
                    <div class="alert alert-info p-2 mb-0">
                        <small><i class="fas fa-info-circle"></i> @lang('shipment-dashboard.select_category_first')</small>
                    </div>
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        </template>
    </div>

    {{-- Stats --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0">@lang('shipment-dashboard.quick_stats')</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="text-primary">{{ $stats['total_orders'] }}</h4>
                            <small>@lang('shipment-dashboard.total_orders')</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="text-success">{{__('admin-dashboard.EGP')}}{{ number_format($stats['total_revenue'], 2) }}</h4>
                            <small>@lang('shipment-dashboard.total_revenue')</small>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <h6 class="border-bottom pb-2">@lang('shipment-dashboard.pricing_summary')</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('shipment-dashboard.per_piece_categories'):</span>
                            <span class="badge bg-info">{{ $stats['piece_categories'] ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('shipment-dashboard.weight_categories'):</span>
                            <span class="badge bg-warning">{{ $stats['weight_categories'] ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('shipment-dashboard.weight_size_categories'):</span>
                            <span class="badge bg-success">{{ $stats['weight_size_categories'] ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Templates for different pricing types
const priceTemplates = {
    piece: `
        <div class="input-group">
            <input type="number" step="0.01" class="form-control"
                   name="new_prices[__INDEX__][per_piece]" required>
            <span class="input-group-text">{{ __('shipment-dashboard.currency') }}</span>
        </div>
        <small class="text-muted">{{ __('shipment-dashboard.per_piece_description') }}</small>
    `,

    weight: `
        <div class="input-group">
            <input type="number" step="0.01" class="form-control"
                   name="new_prices[__INDEX__][price_per_kg]" required>
            <span class="input-group-text">{{ __('shipment-dashboard.currency_per_kg') }}</span>
        </div>
        <small class="text-muted">{{ __('shipment-dashboard.per_kg_description') }}</small>
    `,

    weight_size: `
        <div class="row g-2">
            <div class="col-6">
                <label class="form-label small">{{ __('shipment-dashboard.price_per_size') }}</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control"
                           name="new_prices[__INDEX__][price_per_size]" required>
                    <span class="input-group-text">{{ __('shipment-dashboard.currency') }}</span>
                </div>
            </div>
            <div class="col-6">
                <label class="form-label small">{{ __('shipment-dashboard.price_per_kg') }}</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control"
                           name="new_prices[__INDEX__][price_per_kg]" required>
                    <span class="input-group-text">{{ __('shipment-dashboard.currency_per_kg') }}</span>
                </div>
            </div>
        </div>
    `
};

// Badge classes for types
const badgeClasses = {
    piece: 'bg-info',
    weight: 'bg-warning',
    weight_size: 'bg-success'
};

// Type labels
const typeLabels = {
    piece: '{{ __("shipment-dashboard.per_piece") }}',
    weight: '{{ __("shipment-dashboard.per_weight") }}',
    weight_size: '{{ __("shipment-dashboard.weight_and_size") }}'
};

function updateRowForType(row, type) {
    const typeCell = row.querySelector('.type-cell');
    const priceCell = row.querySelector('.price-cell');

    // Update badge
    typeCell.innerHTML = `<span class="badge ${badgeClasses[type]}">${typeLabels[type]}</span>`;

    // Update pricing inputs
    const index = row.querySelector('.categorySelect').name.match(/\[(\d+)\]/)[1];
    priceCell.innerHTML = priceTemplates[type].replace(/__INDEX__/g, index);

    // Set data attribute
    row.dataset.type = type;
}

document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addCategoryRow');
    const tbody = document.querySelector('#pricingTable tbody');
    const template = document.getElementById('categoryRowTemplate').innerHTML;
    let index = 0;

    // Add new row
    addBtn.addEventListener('click', () => {
        const newRow = template.replace(/__INDEX__/g, index);
        tbody.insertAdjacentHTML('beforeend', newRow);
        index++;
    });

    // Handle category selection
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('categorySelect')) {
            const row = e.target.closest('tr');
            const type = e.target.options[e.target.selectedIndex].dataset.type;

            if (type) {
                updateRowForType(row, type);
            }
        }
    });

    // Handle delete
    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeRow')) {
            const row = e.target.closest('tr');
            const priceId = e.target.closest('.removeRow').dataset.id;

            if (priceId) {
                // Existing price - add hidden input to delete it
                if (!row.querySelector('input[name*="[deleted]"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `prices[${priceId}][deleted]`;
                    input.value = '1';
                    row.appendChild(input);
                    row.style.display = 'none';
                }
            } else {
                // New row - just remove it
                row.remove();
            }
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let distanceIndex = {{ count($distanceRules) }};

    document.getElementById('addRule').addEventListener('click', function () {
        const row = `
            <tr>
                <td><input type="number" step="1" name="rules[${distanceIndex}][max]" class="form-control" required></td>
                <td><input type="number" step="0.01" name="rules[${distanceIndex}][factor]" class="form-control" required></td>
                <td><button type="button" class="btn btn-danger removeRule">Delete</button></td>
            </tr>
        `;
        document.querySelector('#distanceTable tbody').insertAdjacentHTML('beforeend', row);
        distanceIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRule')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endsection
