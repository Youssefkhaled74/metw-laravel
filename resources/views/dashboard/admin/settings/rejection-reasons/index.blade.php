@extends('layouts.admin')

@section('title', __('admin-dashboard.rejection_reasons_management'))
@section('page-title', __('admin-dashboard.rejection_reasons_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.rejection-reasons.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        {{ __('admin-dashboard.add_rejection_reason') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('admin-dashboard.all_rejection_reasons') }}</h5>
        </div>

        <div class="card-body">
            @if($rejectionReasons->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin-dashboard.reason_text') }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th>{{ __('admin-dashboard.created_at') }}</th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($rejectionReasons as $rejectionReason)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $rejectionReason->reason_text }}</td>

                                    <td>
                                        <form
                                            action="{{ route('admin.settings.rejection-reasons.toggle-status', $rejectionReason->id) }}"
                                            method="POST"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm btn-{{ $rejectionReason->is_active ? 'success' : 'danger' }}">
                                                {{ $rejectionReason->is_active
                                                    ? __('admin-dashboard.active')
                                                    : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        @include('admin.partials.date', ['date' => $rejectionReason->created_at])
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.settings.rejection-reasons.edit', $rejectionReason->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                                {{ __('admin-dashboard.edit') }}
                                            </a>

                                            <form
                                                action="{{ route('admin.settings.rejection-reasons.destroy', $rejectionReason->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_rejection_reason') }}');"
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

                <div class="d-flex justify-content-center mt-3">
                    {{ $rejectionReasons->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-ban fa-3x text-muted mb-3"></i>

                    <h5 class="text-muted">
                        {{ __('admin-dashboard.no_rejection_reasons_found') }}
                    </h5>

                    <p class="text-muted mb-0">
                        {{ __('admin-dashboard.start_adding_rejection_reason') }}
                    </p>

                    <div class="mt-3">
                        <a href="{{ route('admin.settings.rejection-reasons.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            {{ __('admin-dashboard.add_new_rejection_reason') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
