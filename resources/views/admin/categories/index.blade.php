@extends('admin.layouts.app')

@section('title', __('admin-dashboard.category_management'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.categories') }}</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_category') }}
        </a>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('admin-dashboard.all_categories') }}</h6>
        </div>
        <div class="card-body">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>{{ __('admin-dashboard.image') }}</th>
                                <th>{{ __('admin-dashboard.name') }}</th>
                                <th>{{ __('admin-dashboard.slug') }}</th>
                                <th>{{ __('admin-dashboard.main_category') }}</th>
                                <th>{{ __('admin-dashboard.active') }}</th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td class="text-center">
                                        @if($category->image)
                                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="img-fluid" style="max-height: 50px;">
                                        @else
                                            {{ __('admin-dashboard.no_image') }}
                                        @endif
                                    </td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>
                                        @if($category->parent)
                                            {{ $category->parent->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $category->is_active ? 'success' : 'danger' }}">
                                            {{ $category->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> {{ __('admin-dashboard.edit') }}
                                        </a>
                                        <button class="btn btn-danger btn-sm delete-category"
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}">
                                            <i class="fas fa-trash"></i> {{ __('admin-dashboard.delete') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $categories->links('pagination::bootstrap-5') }}
            @else
                <div class="text-center py-5">
                    <h4>{{ __('admin-dashboard.no_categories_found') }}</h4>
                    <p class="text-muted">{{ __('admin-dashboard.no_categories_message') }}</p>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_category') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">{{ __('admin-dashboard.confirm_delete_category') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ __('admin-dashboard.confirm_delete_category') }}</p>
                <p class="text-danger" id="categoryNameToDelete"></p>
            </div>
            <div class="modal-footer">
                <form id="deleteCategoryForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin-dashboard.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('admin-dashboard.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.delete-category').click(function() {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');

            $('#categoryNameToDelete').text(categoryName);
            $('#deleteCategoryForm').attr('action', `/admin/categories/${categoryId}`);
            $('#deleteCategoryModal').modal('show');
        });
    });
</script>
@endpush
