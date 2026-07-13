@extends('layouts.admin')

@section('title', __('admin-dashboard.return-reasons.all_return_reasons'))
@section('page-title', __('admin-dashboard.return-reasons.return_reasons'))

@section('page-actions')
    <a href="{{ route('admin.settings.return-reasons.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        {{ __('admin-dashboard.return-reasons.add_return_reason') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('admin-dashboard.return-reasons.all_return_reasons') }}</h5>
        </div>

        <div class="card-body">
            @if($returnReasons->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin-dashboard.return-reasons.reason_text') }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th>{{ __('admin-dashboard.created_at') }}</th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($returnReasons as $returnReason)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $returnReason->reason_text }}</td>

                                    <td>
                                        <form
                                            action="{{ route('admin.settings.return-reasons.toggle-status', $returnReason->id) }}"
                                            method="POST"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm btn-{{ $returnReason->is_active ? 'success' : 'danger' }}">
                                                {{ $returnReason->is_active
                                                    ? __('admin-dashboard.active')
                                                    : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        @include('admin.partials.date', ['date' => $returnReason->created_at])
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.settings.return-reasons.edit', $returnReason->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                                {{ __('admin-dashboard.edit') }}
                                            </a>

                                            <form
                                                action="{{ route('admin.settings.return-reasons.destroy', $returnReason->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete') }}');"
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
                    {{ $returnReasons->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-ban fa-3x text-muted mb-3"></i>

                    <h5 class="text-muted">
                        {{ __('admin-dashboard.return-reasons.all_return_reasons') }}
                    </h5>

                    <p class="text-muted mb-0">
                        {{ __('admin-dashboard.return-reasons.add_return_reason') }}
                    </p>

                    <div class="mt-3">
                        <a href="{{ route('admin.settings.return-reasons.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            {{ __('admin-dashboard.return-reasons.add_return_reason') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
