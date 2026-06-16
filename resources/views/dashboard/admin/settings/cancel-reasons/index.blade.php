@extends('layouts.admin')

@section('title', __('admin-dashboard.cancel_reasons_management'))
@section('page-title', __('admin-dashboard.cancel_reasons_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.cancel-reasons.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        {{ __('admin-dashboard.add_cancel_reason') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('admin-dashboard.all_cancel_reasons') }}</h5>
        </div>

        <div class="card-body">
            @if($cancelReasons->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin-dashboard.name_en') }}</th>
                                <th>{{ __('admin-dashboard.name_ar') }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th>{{ __('admin-dashboard.created_at') }}</th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($cancelReasons as $cancelReason)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $cancelReason->name_en }}</td>
                                    <td>{{ $cancelReason->name_ar }}</td>

                                    <td>
                                        <form
                                            action="{{ route('admin.settings.cancel-reasons.toggle-status', $cancelReason->id) }}"
                                            method="POST"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm btn-{{ $cancelReason->is_active ? 'success' : 'danger' }}">
                                                {{ $cancelReason->is_active
                                                    ? __('admin-dashboard.active')
                                                    : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        @include('admin.partials.date', ['date' => $cancelReason->created_at])
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.settings.cancel-reasons.edit', $cancelReason->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                                {{ __('admin-dashboard.edit') }}
                                            </a>

                                            <form
                                                action="{{ route('admin.settings.cancel-reasons.destroy', $cancelReason->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_cancel_reason') }}');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                    {{ __('admin-dashboard.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $cancelReasons->links('pagination::bootstrap-5') }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-5">
                    <i class="fas fa-ban fa-3x text-muted mb-3"></i>

                    <h5 class="text-muted">
                        {{ __('admin-dashboard.no_cancel_reasons_found') }}
                    </h5>

                    <p class="text-muted mb-0">
                        {{ __('admin-dashboard.start_adding_cancel_reason') }}
                    </p>

                    <div class="mt-3">
                        <a href="{{ route('admin.settings.cancel-reasons.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            {{ __('admin-dashboard.add_new_cancel_reason') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
