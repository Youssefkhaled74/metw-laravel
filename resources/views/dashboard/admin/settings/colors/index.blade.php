@extends('layouts.admin')

@section('title', __('admin-dashboard.colors'))

@section('page-title', __('admin-dashboard.colors_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.colors.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_color') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_colors') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-palette me-2"></i>
                    {{ $colors->count() }} / {{ $colors->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.colors.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث باسم اللون أو كود HEX...' : 'Search by color name or HEX...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                    </button>
                    <a href="{{ route('admin.settings.colors.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($colors->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $nameDir = request('sort_by') === 'name' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.colors.index', array_merge(request()->except('page'), ['sort_by' => 'name', 'sort_dir' => $nameDir])) }}">
                                        <span>{{ __('admin-dashboard.color_name') }}</span>
                                        <i class="fas {{ request('sort_by') === 'name' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.color_hex') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.colors.index', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($colors as $color)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $color->name }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span style="width: 24px; height: 24px; border-radius: 50%; display: inline-block; border: 1px solid #d1d5db; background-color: {{ $color->hex }};"></span>
                                            <span class="fw-semibold">{{ $color->hex }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $color->is_active ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-dot {{ $color->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                            {{ $color->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                        </span>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $color->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <form action="{{ route('admin.settings.colors.toggle-status', $color->id) }}" method="POST" class="d-inline m-0"
                                                  onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? ($color->is_active ? 'هل تريد إيقاف هذا اللون؟' : 'هل تريد تفعيل هذا اللون؟') : ($color->is_active ? 'Are you sure you want to deactivate this color?' : 'Are you sure you want to activate this color?') }}');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-{{ $color->is_active ? 'warning' : 'success' }} text-white action-icon-btn"
                                                        title="{{ app()->getLocale() === 'ar' ? ($color->is_active ? 'إيقاف' : 'تفعيل') : ($color->is_active ? 'Deactivate' : 'Activate') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-{{ $color->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.settings.colors.edit', $color) }}" class="btn btn-sm btn-primary text-white action-icon-btn"
                                               title="{{ __('admin-dashboard.edit') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.settings.colors.destroy', $color) }}" method="POST" class="d-inline m-0 delete-color-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger text-white action-icon-btn delete-color-btn"
                                                        title="{{ __('admin-dashboard.delete') }}" data-bs-toggle="tooltip" data-bs-placement="top">
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
                    {{ $colors->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-palette empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_colors_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.no_colors_message') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.delete-color-btn').forEach(button => {

        button.addEventListener('click', function () {

            let form = this.closest('form');

            Swal.fire({
                title: "{{ __('admin-dashboard.confirm_delete_color') }}",
                text: "This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });

    });

});
</script>
@endsection
