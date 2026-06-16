@extends('layouts.admin')

@section('title', $vendor->name . ' - Products')
@section('page-title', $vendor->name . ' - Products')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.vendors') }}">Vendors</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.vendors.show', $vendor->id) }}">{{ $vendor->name }}</a></li>
    <li class="breadcrumb-item active">Products</li>
@endsection

@section('page-actions')
    {{-- <a href="{{ route('admin.products.create', ['vendor_id' => $vendor->id]) }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a> --}}
    <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Vendor
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Products ({{ $products->total() }})</h5>
                <div class="ms-3">
                    <form action="{{ route('admin.vendors.products', $vendor->id) }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search products..."
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.vendors.products', $vendor->id) }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        @if($product->images->isNotEmpty())
                                            <img src="{{ asset($product->images->first()->url) }}"
                                                 alt="{{ $product->name }}"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.show', ['product' => $product->id, 'from' => 'admin.vendors.products']) }}" class="text-dark">
                                            {{ Str::limit($product->name, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $product->sku ?? 'N/A' }}</td>
                                    <td>
                                        @if($product->category)
                                            <span class="badge bg-info">{{ $product->category->name }}</span>
                                        @else
                                            <span class="text-muted">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($product->price, 2) }} {{__('admin-dashboard.EGP')}}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                                            {{ $product->stock }} in stock
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.products.toggle-status', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-{{ $product->is_active ? 'success' : 'secondary' }} btn-sm"
                                                    onclick="return confirm('Are you sure you want to {{ $product->is_active ? 'deactivate' : 'activate' }} this product?')">
                                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $product->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.products.show', ['product' => $product->id, 'from' => 'admin.vendors.products']) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- <a href="{{ route('admin.products.edit', $product->id) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a> --}}
                                            {{-- <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form> --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-box-open fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No Products Found</h5>
                    <p class="text-muted mb-4">This vendor hasn't added any products yet.</p>
                    {{-- <a href="{{ route('admin.products.create', ['vendor_id' => $vendor->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add Your First Product
                    </a> --}}
                </div>
            @endif
        </div>
    </div>

    <!-- Add some custom styles -->
    @push('styles')
    <style>
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-top: none;
        }
        .table td {
            vertical-align: middle;
        }
        .table img {
            border-radius: 4px;
            border: 1px solid #eee;
        }
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
    </style>
    @endpush
@endsection
