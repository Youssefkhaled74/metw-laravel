@extends('layouts.admin')

@section('title', __('admin-dashboard.product_sizes_management'))
@section('page-title', __('admin-dashboard.product_sizes_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.product-sizes.create') }}" class="btn btn-primary btn-modern-add">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_product_size') }}
    </a>
@endsection

@section('content')
    <div class="card shadow-sm border-0 product-sizes-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.product_sizes_management') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-ruler-combined me-2"></i>
                    <span id="visibleProductSizesCount">{{ $productSizes->count() }}</span> / {{ $productSizes->count() }}
                </span>
            </div>

            <div class="row g-2 align-items-center">
                <div class="col-lg-4 ms-auto">
                    <select id="productSizeStatusFilter" class="form-select form-select-sm filter-select-modern">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="active">{{ __('admin-dashboard.active') }}</option>
                        <option value="inactive">{{ __('admin-dashboard.inactive') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($productSizes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 product-sizes-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col" role="button" tabindex="0" data-sort-key="id" aria-sort="none">
                                    <span>{{ __('admin-dashboard.id') }}</span>
                                    <i class="fas fa-sort sort-indicator"></i>
                                </th>
                                <th class="text-nowrap sortable-col" role="button" tabindex="0" data-sort-key="title" aria-sort="none">
                                    <span>{{ __('admin-dashboard.title') }}</span>
                                    <i class="fas fa-sort sort-indicator"></i>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="productSizesTableBody">
                            @foreach ($productSizes as $productSize)
                                <tr
                                    data-id="{{ $productSize->id }}"
                                    data-title="{{ strtolower($productSize->title) }}"
                                    data-status="{{ $productSize->is_active ? 'active' : 'inactive' }}"
                                >
                                    <td class="fw-semibold text-muted">{{ $productSize->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="size-avatar"><i class="fas fa-ruler"></i></span>
                                            <span class="fw-semibold text-dark">{{ $productSize->title }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.settings.product-sizes.toggle-status', $productSize) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm status-pill btn-{{ $productSize->is_active ? 'success' : 'danger' }}">
                                                <span class="status-dot"></span>
                                                {{ $productSize->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.settings.product-sizes.edit', $productSize) }}" class="btn btn-sm btn-warning text-white action-icon-btn" title="{{ __('admin-dashboard.edit') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.settings.product-sizes.destroy', $productSize) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger action-icon-btn" onclick="return confirm('{{ __('admin-dashboard.confirm_delete_product_size') }}')" title="{{ __('admin-dashboard.delete') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="empty-row" style="display: none;">
                                <td colspan="4">
                                    <div class="empty-state py-5 text-center">
                                        <i class="fas fa-ruler-combined empty-icon mb-3"></i>
                                        <h5 class="mb-1">{{ __('admin-dashboard.no_product_sizes_found') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $productSizes->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-ruler-combined empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_product_sizes_found') }}</h5>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style data-page-style="product-sizes-index">
        .product-sizes-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }

        .btn-modern-add {
            border-radius: 12px;
            padding-inline: 1rem;
            box-shadow: 0 10px 22px rgba(59, 130, 246, 0.18);
        }

        .table-wrap {
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        }

        .rows-counter-badge {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
        }

        .filter-select-modern {
            border-color: #e5e7eb;
            min-height: 44px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .filter-select-modern:focus {
            box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.12);
            border-color: #93c5fd;
        }

        .product-sizes-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-color: #e5e7eb;
            padding: 0.95rem 1rem;
            box-shadow: inset 0 -1px 0 #e5e7eb;
        }

        .product-sizes-table tbody td {
            padding: 1rem;
            border-color: #edf0f5;
        }

        .product-sizes-table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .product-sizes-table tbody tr:hover {
            background: #f8fafc;
            box-shadow: inset 0 0 0 9999px rgba(248, 250, 252, 0.35);
            transform: translateY(-1px);
        }

        .sortable-col {
            cursor: pointer;
            user-select: none;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sortable-col:hover {
            background: #eef2ff;
            color: #1e3a8a;
        }

        .sortable-col .sort-indicator {
            margin-inline-start: 0.45rem;
            font-size: 0.8rem;
            opacity: 0.75;
        }

        .sortable-col.is-active {
            background: #eef2ff;
            color: #1d4ed8;
        }

        .sortable-col.is-active .sort-indicator {
            opacity: 1;
        }

        .size-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
            color: #4f46e5;
            flex: 0 0 auto;
        }

        .status-pill,
        .action-icon-btn {
            border-radius: 999px;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .status-pill {
            gap: 0.4rem;
            padding-inline: 0.85rem;
            font-weight: 600;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
        }

        .actions-group {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .action-icon-btn {
            width: 36px;
            height: 36px;
            padding: 0;
        }

        .empty-state {
            min-width: min(100%, 420px);
            border: 1px dashed #d0d7e2;
            border-radius: 18px;
            background: #fcfdff;
        }

        .empty-icon {
            font-size: 2.2rem;
            color: #94a3b8;
        }
    </style>

    <script data-page-script="product-sizes-index">
        (function () {
            const tableBody = document.getElementById('productSizesTableBody');
            if (!tableBody) return;

            const sortableHeaders = document.querySelectorAll('.product-sizes-table .sortable-col');
            const statusFilter = document.getElementById('productSizeStatusFilter');
            const visibleCountEl = document.getElementById('visibleProductSizesCount');
            const emptyRow = tableBody.querySelector('.empty-row');

            let currentSort = { key: null, direction: 'asc' };

            const getRows = () => Array.from(tableBody.querySelectorAll('tr')).filter(row => !row.classList.contains('empty-row'));

            const updateCounter = () => {
                const visibleRows = getRows().filter(row => row.style.display !== 'none').length;
                if (visibleCountEl) visibleCountEl.textContent = String(visibleRows);

                if (emptyRow) {
                    emptyRow.style.display = visibleRows === 0 ? '' : 'none';
                }
            };

            const applyFilter = () => {
                const selectedStatus = statusFilter ? statusFilter.value : 'all';

                getRows().forEach(row => {
                    const rowStatus = row.dataset.status || '';
                    const pass = selectedStatus === 'all' || rowStatus === selectedStatus;
                    row.style.display = pass ? '' : 'none';
                });

                updateCounter();
            };

            const setSortIndicators = () => {
                sortableHeaders.forEach(header => {
                    const isActive = header.dataset.sortKey === currentSort.key;
                    header.classList.toggle('is-active', isActive);
                    header.setAttribute('aria-sort', isActive ? (currentSort.direction === 'asc' ? 'ascending' : 'descending') : 'none');

                    const icon = header.querySelector('.sort-indicator');
                    if (!icon) return;
                    icon.className = `fas ${isActive ? (currentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'} sort-indicator`;
                });
            };

            const sortRows = (sortKey) => {
                if (!sortKey) return;

                currentSort.direction = currentSort.key === sortKey && currentSort.direction === 'asc' ? 'desc' : 'asc';
                currentSort.key = sortKey;

                const rows = getRows();
                rows.sort((a, b) => {
                    let aVal = a.dataset[sortKey] ?? '';
                    let bVal = b.dataset[sortKey] ?? '';

                    if (sortKey === 'id') {
                        aVal = Number(aVal);
                        bVal = Number(bVal);
                    } else {
                        aVal = String(aVal).toLowerCase();
                        bVal = String(bVal).toLowerCase();
                    }

                    if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
                    if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
                    return 0;
                });

                rows.forEach(row => tableBody.appendChild(row));
                if (emptyRow) tableBody.appendChild(emptyRow);

                setSortIndicators();
                applyFilter();
            };

            sortableHeaders.forEach(header => {
                const handler = () => sortRows(header.dataset.sortKey);
                header.addEventListener('click', handler);
                header.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        handler();
                    }
                });
            });

            statusFilter?.addEventListener('change', applyFilter);

            sortRows('id');
        })();
    </script>
@endsection
