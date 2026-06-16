@extends('layouts.admin')

@section('title', __('admin-dashboard.banner_management'))
@section('page-title', __('admin-dashboard.banner_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.banners.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_banner') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">{{ __('admin-dashboard.all_banners') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-images me-2"></i>
                    {{ $banners->count() }} / {{ $banners->total() }}
                </span>
            </div>
        </div>

        <div class="card-body p-0 table-wrap">
            @if($banners->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap">{{ __('admin-dashboard.image') }}</th>
                                <th class="text-nowrap mobile-hide">{{ __('admin-dashboard.link') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap mobile-hide">{{ __('admin-dashboard.created_at') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banners as $banner)
                                <tr>
                                    <td>
                                        @if($banner->image)
                                            <img src="{{ asset($banner->image) }}"
                                                 alt="Banner"
                                                 class="entity-logo rounded"
                                                 width="92"
                                                 height="42">
                                        @else
                                            <div class="entity-avatar">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="mobile-hide">
                                        @if($banner->link)
                                            <a href="{{ $banner->link }}" target="_blank" class="text-decoration-none">
                                                {{ \Illuminate\Support\Str::limit($banner->link, 45) }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('admin-dashboard.no_link') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $banner->is_active ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-dot {{ $banner->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                            {{ $banner->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                        </span>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $banner->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <form action="{{ route('admin.settings.banners.toggle-status', $banner->id) }}"
                                                  method="POST"
                                                  class="d-inline m-0"
                                                                                                    onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? ($banner->is_active ? 'هل تريد إيقاف هذا البانر؟' : 'هل تريد تفعيل هذا البانر؟') : ($banner->is_active ? 'Are you sure you want to deactivate this banner?' : 'Are you sure you want to activate this banner?') }}');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="btn btn-sm btn-{{ $banner->is_active ? 'warning' : 'success' }} text-white action-icon-btn"
                                                                                                                title="{{ app()->getLocale() === 'ar' ? ($banner->is_active ? 'إيقاف' : 'تفعيل') : ($banner->is_active ? 'Deactivate' : 'Activate') }}"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top">
                                                    <i class="fas fa-{{ $banner->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.settings.banners.edit', $banner->id) }}"
                                               class="btn btn-sm btn-primary text-white action-icon-btn"
                                               title="{{ __('admin-dashboard.edit') }}"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.settings.banners.destroy', $banner->id) }}"
                                                  method="POST"
                                                  class="d-inline m-0"
                                                  onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_banner') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger text-white action-icon-btn"
                                                        title="{{ __('admin-dashboard.delete') }}"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top">
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
                    {{ $banners->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-images empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_banners_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.no_banners_message') }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.settings.banners.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_banner') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
