@extends('layouts.shipment')

@section('title', __('shipment-dashboard.subcategory_prices'))
@section('page-title', __('shipment-dashboard.subcategory_prices'))

@section('page-actions')
    <a href="{{ url()->previous() }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> @lang('shipment-dashboard.back')
    </a>

    <button type="button" class="btn btn-success btn-sm addSubCategoryBtn"
        data-category-price-id="{{ $categoryPrice->id }}">
        <i class="fas fa-plus"></i> @lang('shipment-dashboard.add')
    </button>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            @lang('shipment-dashboard.subcategory_prices_for'):
            <span class="text-primary">{{ $categoryPrice->category->name }}</span>
        </h5>
    </div>

    <div class="card-body">

        @if($subPrices->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>@lang('shipment-dashboard.subcategory')</th>
                        <th>@lang('shipment-dashboard.price_small')</th>
                        <th>@lang('shipment-dashboard.price_medium')</th>
                        <th>@lang('shipment-dashboard.price_large')</th>
                        <th>@lang('shipment-dashboard.created_at')</th>
                        <th class="text-center">@lang('shipment-dashboard.actions')</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($subPrices as $sub)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td><span class="badge bg-primary">{{ $sub->category->name }}</span></td>

                            <td>{{__('admin-dashboard.EGP')}}{{ number_format($sub->price_small, 2) }}</td>
                            <td>{{__('admin-dashboard.EGP')}}{{ number_format($sub->price_medium, 2) }}</td>
                            <td>{{__('admin-dashboard.EGP')}}{{ number_format($sub->price_large, 2) }}</td>

                            <td>{{ $sub->created_at->format('M d, Y H:i') }}</td>

                            <td class="text-center">
                                <button class="btn btn-warning btn-sm editSubCategoryBtn"
                                        data-id="{{ $sub->id }}"
                                        data-category="{{ $sub->category_id }}"
                                        data-small="{{ $sub->price_small }}"
                                        data-medium="{{ $sub->price_medium }}"
                                        data-large="{{ $sub->price_large }}"
                                        data-categoryprice="{{ $categoryPrice->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm deleteSubCategoryBtn"
                                        data-id="{{ $sub->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $subPrices->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">@lang('shipment-dashboard.no_subcategories_found')</h5>
                <p class="text-muted">@lang('shipment-dashboard.no_subcategory_prices_yet')</p>
            </div>
        @endif

    </div>
</div>


{{-- ===================================== --}}
{{--    ADD / EDIT MODAL                   --}}
{{-- ===================================== --}}
<div class="modal fade" id="subCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="subCategoryForm">
                @csrf
                <input type="hidden" id="editId" name="id">
                <input type="hidden" name="shipment_company_category_price_id" id="categoryPriceId">

                <div class="modal-header">
                    <h5 class="modal-title">@lang('shipment-dashboard.add_subcategory_price')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>@lang('shipment-dashboard.select_subcategory')</label>
                        <select class="form-select" id="subCategorySelect" name="category_id" required>
                            <option value="" disabled selected>@lang('shipment-dashboard.select')</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>@lang('shipment-dashboard.price_small')</label>
                        <input type="number" step="0.01" class="form-control" name="price_small" id="priceSmall" required>
                    </div>

                    <div class="mb-3">
                        <label>@lang('shipment-dashboard.price_medium')</label>
                        <input type="number" step="0.01" class="form-control" name="price_medium" id="priceMedium" required>
                    </div>

                    <div class="mb-3">
                        <label>@lang('shipment-dashboard.price_large')</label>
                        <input type="number" step="0.01" class="form-control" name="price_large" id="priceLarge" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">@lang('shipment-dashboard.save')</button>
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">@lang('shipment-dashboard.close')</button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- ===================================== --}}
{{--    DELETE MODAL                       --}}
{{-- ===================================== --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">{{ __('shipment-dashboard.confirm_delete') }}</h5>
            </div>

            <div class="modal-body text-center">
                <p class="fw-bold">{{ __('shipment-dashboard.are_you_sure') }}</p>
                <input type="hidden" id="deleteId">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('shipment-dashboard.cancel') }}</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">{{ __('shipment-dashboard.delete') }}</button>
            </div>

        </div>
    </div>
</div>


{{-- ===================================== --}}
{{--           JAVASCRIPT FIXED           --}}
{{-- ===================================== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    const modal = new bootstrap.Modal(document.getElementById('subCategoryModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    function resetForm() {
        document.getElementById('subCategoryForm').reset();
        document.getElementById('editId').value = "";
        document.querySelector('.modal-title').textContent = "@lang('shipment-dashboard.add_subcategory_price')";
        document.getElementById('subCategorySelect').innerHTML =
            `<option disabled selected>@lang('shipment-dashboard.select')</option>`;
    }

    // ========================================
    //          CREATE NEW
    // ========================================
    document.querySelector('.addSubCategoryBtn').addEventListener('click', function () {
        resetForm();

        const catPriceId = this.dataset.categoryPriceId;
        document.getElementById('categoryPriceId').value = catPriceId;

        loadSubCategories(catPriceId, null);

        modal.show();
    });

    // ========================================
    //          EDIT EXISTING
    // ========================================
    document.querySelectorAll('.editSubCategoryBtn').forEach(btn => {
        btn.addEventListener('click', function () {

            resetForm();

            document.querySelector('.modal-title').textContent = "Edit Subcategory Price";

            let id = this.dataset.id;
            let catId = this.dataset.category;
            let small = this.dataset.small;
            let medium = this.dataset.medium;
            let large = this.dataset.large;
            let catPriceId = this.dataset.categoryprice;

            document.getElementById('editId').value = id;
            document.getElementById('categoryPriceId').value = catPriceId;

            document.getElementById('priceSmall').value = small;
            document.getElementById('priceMedium').value = medium;
            document.getElementById('priceLarge').value = large;

            loadSubCategories(catPriceId, catId);

            modal.show();
        });
    });

    // ========================================
    //           LOAD SUBCATEGORIES
    // ========================================
    function loadSubCategories(categoryPriceId, selectedId = null) {
        fetch(`/shipment/subcategories/${categoryPriceId}`)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('subCategorySelect');
                select.innerHTML = `<option disabled selected>Select</option>`;

                data.forEach(sub => {
                    select.innerHTML += `
                    <option value="${sub.id}" ${selectedId == sub.id ? 'selected' : ''}>
                        ${sub.name}
                    </option>`;
                });
            });
    }

    // ========================================
    //           SUBMIT FORM
    // ========================================
    document.getElementById('subCategoryForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = new FormData(this);
        let id = document.getElementById('editId').value;

        let url = id
            ? `/shipment/subcategories-price/${id}`
            : `/shipment/subcategories-price`;

        if (id) form.append('_method', 'PUT');

        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': form.get('_token') },
            body: form
        })
            .then(res => res.json())
            .then(() => location.reload());
    });


    // ========================================
    //              DELETE
    // ========================================
    document.querySelectorAll('.deleteSubCategoryBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('deleteId').value = btn.dataset.id;
            deleteModal.show();
        })
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
        let id = document.getElementById('deleteId').value;

        fetch(`/shipment/subcategories-price/${id}`, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: new URLSearchParams({ _method: "DELETE" })
        })
            .then(res => res.json())
            .then(() => location.reload());
    });

});
</script>

@endsection
