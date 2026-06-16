@extends('layouts.vendor')

@section('title', __('vendor-dashboard.products_management'))
@section('page-title', __('vendor-dashboard.products_management'))

@section('page-actions')
    <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('vendor-dashboard.add_new_product') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('vendor-dashboard.all_products') }}</h5>
        </div>
        <div class="card-body">
            @if ($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('vendor-dashboard.id') }}</th>
                                <th>{{ __('vendor-dashboard.product') }}</th>
                                <th>{{ __('vendor-dashboard.category') }}</th>
                                <th>{{ __('vendor-dashboard.price') }}</th>
                                <th>{{ __('vendor-dashboard.sold') }}</th>
                                <th>{{ __('vendor-dashboard.status') }}</th>
                                <th>{{ __('vendor-dashboard.created_at') }}</th>
                                <th>{{ __('vendor-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($product->media->count() > 0)
                                                <img src="{{ asset(  $product->media->first()->url) }}"
                                                    alt="{{ $product->name }}" class="rounded me-3" width="50"
                                                    height="50">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                @if ($product->short_description)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->category->name ?? __('vendor-dashboard.not_available') }}</td>
                                    <td>{{__('admin-dashboard.EGP')}}{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $product->sold_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                            {{ $product->is_active ? __('vendor-dashboard.active') : __('vendor-dashboard.inactive') }}
                                        </span>
                                    </td>
                                    <td>{{ $product->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('vendor.products.edit', $product->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> {{ __('vendor-dashboard.edit') }}
                                            </a>
                                            <form method="POST"
                                                action="{{ route('vendor.products.toggle-status', $product->id) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="btn btn-sm btn-{{ $product->is_active ? 'warning' : 'success' }}"
                                                    onclick="return confirm('{{ __('vendor-dashboard.confirm_toggle') }}')">
                                                    <i class="fas fa-{{ $product->is_active ? 'pause' : 'play' }}"></i>
                                                    {{ $product->is_active ? __('vendor-dashboard.deactivate') : __('vendor-dashboard.activate') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('vendor-dashboard.no_products_found') }}</h5>
                    <p class="text-muted">{{ __('vendor-dashboard.no_products_yet') }}</p>
                    <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('vendor-dashboard.add_first_product') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
