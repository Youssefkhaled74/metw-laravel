@extends('layouts.admin')

@section('title', __('admin-dashboard.category_management'))
@section('page-title', __('admin-dashboard.category_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.categories.create') }}" class="btn btn-primary btn-modern-add">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_category') }}
    </a>
@endsection

@section('content')
    @php
        $currentSort = request('sort', 'id');
        $currentDirection = request('direction', 'desc');
        $currentSearch = request('search', '');
        $currentStatus = request('status', 'all');
        $currentMainCategoryId = request('main_category_id', 'all');

        $sortUrl = function (string $column) use ($currentSort, $currentDirection) {
            $direction = $currentSort === $column && $currentDirection === 'asc' ? 'desc' : 'asc';

            return route('admin.settings.categories.index', array_merge(request()->except('page'), [
                'sort' => $column,
                'direction' => $direction,
            ]));
        };

        $sortIcon = function (string $column) use ($currentSort, $currentDirection) {
            if ($currentSort !== $column) {
                return 'fas fa-sort sort-indicator';
            }

            return $currentDirection === 'asc'
                ? 'fas fa-sort-up sort-indicator'
                : 'fas fa-sort-down sort-indicator';
        };
    @endphp
    <div class="card shadow-sm border-0 categories-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_categories') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-layer-group me-2"></i>
                    <span id="visibleRowsCount">{{ $categories->count() }}</span> / {{ $categories->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.categories.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            name="search"
                            type="text"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم أو الرابط المختصر أو الحالة...' : 'Search by name, slug, or status...' }}"
                            autocomplete="off"
                            value="{{ $currentSearch }}"
                        >
                    </div>
                </div>

                <div class="col-lg-3">
                    <select name="main_category_id" class="form-select form-select-sm filter-select-modern">
                        <option value="all" @selected($currentMainCategoryId === 'all')>{{ app()->getLocale() === 'ar' ? 'كل الأقسام الرئيسية' : 'All main categories' }}</option>
                        @foreach ($mainCategories as $mainCategory)
                            @php
                                $mainCategoryLabel = optional($mainCategory->translation(app()->getLocale()))->name ?? $mainCategory->name;
                            @endphp
                            <option value="{{ $mainCategory->id }}" @selected((string) $currentMainCategoryId === (string) $mainCategory->id)>{{ $mainCategoryLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all" @selected($currentStatus === 'all')>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="active" @selected($currentStatus === 'active')>{{ __('admin-dashboard.active') }}</option>
                        <option value="inactive" @selected($currentStatus === 'inactive')>{{ __('admin-dashboard.inactive') }}</option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <input type="hidden" name="sort" value="{{ $currentSort }}">
                    <input type="hidden" name="direction" value="{{ $currentDirection }}">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    <a href="{{ route('admin.settings.categories.index') }}" class="btn btn-sm btn-outline-secondary">
                        {{ app()->getLocale() === 'ar' ? 'مسح' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 categories-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col {{ $currentSort === 'id' ? 'is-active' : '' }}" aria-sort="{{ $currentSort === 'id' ? ($currentDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                    <a href="{{ $sortUrl('id') }}" class="sortable-link">
                                        <span>{{ __('admin-dashboard.id') }}</span>
                                        <i class="{{ $sortIcon('id') }}"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.image') }}</th>
                                <th class="text-nowrap sortable-col {{ $currentSort === 'name' ? 'is-active' : '' }}" aria-sort="{{ $currentSort === 'name' ? ($currentDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                    <a href="{{ $sortUrl('name') }}" class="sortable-link">
                                        <span>{{ __('admin-dashboard.name') }}</span>
                                        <i class="{{ $sortIcon('name') }}"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col {{ $currentSort === 'slug' ? 'is-active' : '' }}" aria-sort="{{ $currentSort === 'slug' ? ($currentDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                    <a href="{{ $sortUrl('slug') }}" class="sortable-link">
                                        <span>{{ __('admin-dashboard.slug') }}</span>
                                        <i class="{{ $sortIcon('slug') }}"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.main_category') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap sortable-col {{ $currentSort === 'created_at' ? 'is-active' : '' }}" aria-sort="{{ $currentSort === 'created_at' ? ($currentDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                    <a href="{{ $sortUrl('created_at') }}" class="sortable-link">
                                        <span>{{ __('admin-dashboard.created_at') }}</span>
                                        <i class="{{ $sortIcon('created_at') }}"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            @foreach ($categories as $category)
                                @php
                                    $categoryName = optional($category->translation(app()->getLocale()))->name ?? $category->name;
                                    $mainCategoryName = optional($category->mainCategory)->name ?? '-';
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $category->id }}</td>
                                    <td>
                                        @if ($category->image)
                                            <img src="{{ asset($category->image) }}" alt="{{ $categoryName }}" class="rounded-4 border category-image">
                                        @else
                                            <div class="category-image category-image-placeholder" title="No image">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="category-avatar">
                                                <i class="fas fa-folder-open"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark category-name">{{ $categoryName }}</div>
                                                <div class="text-muted small category-subname">{{ optional($category->translation('en'))->name ?? $category->name }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="slug-pill text-truncate" title="{{ $category->slug }}">{{ $category->slug }}</span>
                                    </td>

                                    <td>
                                        <span class="main-category-pill">{{ $mainCategoryName }}</span>
                                    </td>

                                    <td>
                                        <form action="{{ route('admin.settings.categories.toggle-status', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm status-pill btn-{{ $category->is_active ? 'success' : 'danger' }}" title="Toggle status">
                                                <span class="status-dot"></span>
                                                {{ $category->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>

                                    <td>@include('admin.partials.date', ['date' => $category->created_at])</td>

                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.settings.categories.edit', $category->id) }}" class="btn btn-sm btn-warning text-white action-icon-btn" title="{{ __('admin-dashboard.edit') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.settings.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_category') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger action-icon-btn" title="{{ __('admin-dashboard.delete') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-folder-open empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_categories_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.start_adding_category') }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.settings.categories.create') }}" class="btn btn-primary btn-modern-add">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_category') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .categories-card {
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

        .search-shell .input-group-text,
        .search-shell .form-control,
        .filter-select-modern {
            border-color: #e5e7eb;
            min-height: 44px;
        }

        .search-shell .input-group-text {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .search-input-modern {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .search-input-modern:focus,
        .filter-select-modern:focus {
            box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.12);
            border-color: #93c5fd;
        }

        .filter-select-modern {
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .rows-counter-badge {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
        }

        .categories-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-color: #e5e7eb;
            padding: 0.95rem 1rem;
            box-shadow: inset 0 -1px 0 #e5e7eb;
        }

        .categories-table tbody td {
            padding: 1rem;
            border-color: #edf0f5;
        }

        .categories-table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .categories-table tbody tr:hover {
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

        .sortable-link {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.35rem;
            width: 100%;
            color: inherit;
            text-decoration: none;
        }

        .sortable-link:hover {
            color: inherit;
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

        .category-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
            color: #4f46e5;
            flex: 0 0 auto;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .category-image {
            width: 54px;
            height: 54px;
            object-fit: cover;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .category-image-placeholder {
            background: #f8fafc;
            color: #94a3b8;
            border-style: dashed !important;
            font-size: 1.15rem;
        }

        .slug-pill,
        .main-category-pill {
            display: inline-block;
            max-width: 210px;
            padding: 0.38rem 0.75rem;
            border-radius: 999px;
            background: #f8fafc;
            color: #334155;
            font-size: 0.88rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .slug-pill {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
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
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.42);
            flex: 0 0 auto;
        }

        .actions-group {
            display: inline-flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 0.45rem;
        }

        .actions-group form {
            display: inline-flex;
            margin: 0;
        }

        .action-icon-btn {
            width: 38px;
            min-width: 38px;
            height: 38px;
            padding: 0;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        }

        .empty-state {
            background: linear-gradient(180deg, #fafafa 0%, #f8fafc 100%);
            border: 1px dashed #d1d5db;
            border-radius: 18px;
        }

        .empty-icon {
            font-size: 2.25rem;
            color: #94a3b8;
        }

        .category-name {
            line-height: 1.25;
        }

        .category-subname {
            line-height: 1.2;
        }

        @media (max-width: 991.98px) {
            .rows-counter-badge {
                margin-inline-start: auto;
            }
        }

        @media (max-width: 767.98px) {
            .categories-table thead th,
            .categories-table tbody td {
                padding: 0.8rem 0.85rem;
            }

            .status-pill,
            .action-icon-btn {
                width: 100%;
            }

            .actions-group {
                width: 100%;
                flex-wrap: wrap;
            }

            .actions-group .action-icon-btn {
                min-width: 0;
                width: auto;
                flex: 1 1 calc(50% - 0.25rem);
            }

            .slug-pill,
            .main-category-pill {
                max-width: 100%;
            }

            .search-shell .input-group-text,
            .search-shell .form-control,
            .filter-select-modern {
                min-height: 42px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('input[name="search"]');
            const filterForm = searchInput ? searchInput.closest('form') : null;
            const filterSelects = document.querySelectorAll('select[name="main_category_id"], select[name="status"]');
            const visibleRowsCount = document.getElementById('visibleRowsCount');

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            let searchTimer = null;

            searchInput?.addEventListener('input', function () {
                window.clearTimeout(searchTimer);
                searchTimer = window.setTimeout(() => {
                    filterForm?.submit();
                }, 350);
            });

            filterSelects.forEach(function (select) {
                select.addEventListener('change', function () {
                    filterForm?.submit();
                });
            });

            if (visibleRowsCount && !visibleRowsCount.textContent.trim()) {
                visibleRowsCount.textContent = '0';
            }
        });
    </script>
@endsection
