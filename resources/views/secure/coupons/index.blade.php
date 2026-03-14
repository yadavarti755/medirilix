@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@can('add coupon')
@php
$button = '<button type="button" class="btn btn-primary btn-add"><i class="fa fa-plus"></i> Add New</button>';
@endphp
@endcan
<x-page-header title="{{ $pageTitle }}" :button="$button" />

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="datatable" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@canany(['add coupon', 'edit coupon'])
{{-- Modal --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="formModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" id="form">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" id="code" required style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="discount_type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                            <select class="form-control" name="discount_type" id="discount_type" required>
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="value" id="value" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="min_spend" class="form-label">Min Spend</label>
                            <input type="number" step="0.01" class="form-control" name="min_spend" id="min_spend">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="max_discount" class="form-label">Max Discount Cap</label>
                            <input type="number" step="0.01" class="form-control" name="max_discount" id="max_discount">
                            <small class="text-muted">For percentage discounts only</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-control" name="is_active" id="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" name="end_date" id="end_date">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="usage_limit_per_coupon" class="form-label">Usage Limit (Total)</label>
                            <input type="number" class="form-control" name="usage_limit_per_coupon" id="usage_limit_per_coupon">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="usage_limit_per_user" class="form-label">Usage Limit (Per User)</label>
                            <input type="number" class="form-control" name="usage_limit_per_user" id="usage_limit_per_user">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="product_ids" class="form-label">Applies to Products (Leave empty for all)</label>
                            <select class="form-control select2" name="product_ids[]" id="product_ids" multiple>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="category_ids" class="form-label">Applies to Categories (Leave empty for all)</label>
                            <select class="form-control select2" name="category_ids[]" id="category_ids" multiple>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@section('pages-scripts')
<script @cspNonce>
    let dataTable;
    let operationType = "ADD";
    let recordId;
    $(document).ready(function() {

        dataTable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            ajax: {
                url: "{{ route('coupons.fetch-for-datatable') }}",
                type: "POST",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                }
            },
            columns: [{
                    data: null,
                    name: 'id',
                    width: '8%',
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Serial number (starting from 1)
                    }
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'discount_type',
                    name: 'discount_type'
                },
                {
                    data: 'value',
                    name: 'value'
                },
                {
                    data: 'status',
                    name: 'is_active',
                    render: function(data) {
                        return data; // already rendered as html in controller or can be adjusted here
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '12%',
                }
            ]
        });

        $('#formModal').on('hidden.bs.modal', function() {
            $('#formModalLabel').html('');
            $('#formSubmitBtn').html('');
            $('#form').trigger('reset');
            $('.select2').val(null).trigger('change');
        });

        // Initialize Select2
        // Assuming select2 library is active globally or needs init
        if ($('.select2').length > 0) {
            $('.select2').select2({
                dropdownParent: $('#formModal'), // Important for modal
                width: '100%'
            });
        }
    });
</script>

@canany(['add coupon', 'edit coupon'])
<script @cspNonce>
    $(document).ready(function() {
        // On submitting the form
        $('#form').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(document.getElementById('form'));
            // Check the operation type
            var url;
            if (operationType == 'EDIT') {
                url = "{{ route('coupons.update', ':id') }}".replace(':id', recordId);
            } else if (operationType == 'ADD') {
                url = "{{ route('coupons.store') }}";
            } else {
                return false;
            }

            // Send Ajax Request
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Hide modal and reset form
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            $('#formModal').modal('hide');
                            dataTable.ajax.reload();
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = Object.values(errors).flat().join("<br>");
                        Swal.fire({
                            title: "Validation Error",
                            html: errorMessages,
                            icon: "error"
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: xhr.responseJSON.message || "Something went wrong. Please try again.",
                            icon: "error"
                        });
                    }
                }
            });
        });
    })
</script>
@endcan

@can('add coupon')
<script @cspNonce>
    $(document).ready(function() {
        // On click add button, open the modal
        $('.btn-add').on('click', function() {
            operationType = "ADD";
            $('#formModalLabel').html('<i class="fas fa-plus"></i> Add New');
            $('#formSubmitBtn').html('<i class="fas fa-plus"></i> Submit');
            $('#formModal').modal('show');
        })
    });
</script>
@endcan

@can('edit coupon')
<script @cspNonce>
    $(document).ready(function() {
        // Onclick edit button
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            if (id) {
                operationType = "EDIT";
                $.ajax({
                    url: "{{ route('coupons.fetch-one', ':id') }}".replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success == true) {
                            var data = response.data;
                            recordId = data.id;
                            $('#code').val(data.code);
                            $('#description').val(data.description);
                            $('#discount_type').val(data.discount_type);
                            $('#value').val(data.value);
                            $('#min_spend').val(data.min_spend);
                            $('#max_discount').val(data.max_discount);
                            $('#usage_limit_per_coupon').val(data.usage_limit_per_coupon);
                            $('#usage_limit_per_user').val(data.usage_limit_per_user);
                            $('#start_date').val(data.start_date);
                            $('#end_date').val(data.end_date);
                            $('#is_active').val(data.is_active ? 1 : 0);

                            // Handle Multi-Selects
                            if (data.products) {
                                let productIds = data.products.map(p => p.id);
                                $('#product_ids').val(productIds).trigger('change');
                            } else {
                                $('#product_ids').val([]).trigger('change');
                            }

                            if (data.categories) {
                                let categoryIds = data.categories.map(c => c.id);
                                $('#category_ids').val(categoryIds).trigger('change');
                            } else {
                                $('#category_ids').val([]).trigger('change');
                            }

                            $('#formModalLabel').html('<i class="fas fa-edit"></i> Edit Record');
                            $('#formSubmitBtn').html('<i class="fas fa-edit"></i> Update');
                            $('#formModal').modal('show');
                        } else {
                            toastr.error("Error while fetching data");
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = Object.values(errors).flat().join("<br>");
                            Swal.fire({
                                title: "Validation Error",
                                html: errorMessages,
                                icon: "error"
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: xhr.responseJSON.message || "Something went wrong. Please try again.",
                                icon: "error"
                            });
                        }
                    }
                });
            }
        });
    })
</script>
@endcan

@can('delete coupon')
<script @cspNonce>
    $(document).ready(function() {
        /**
         * Delete record
         */
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            if (id) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You want to delete the record.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('coupons.destroy', ':id') }}".replace(':id', id),
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    dataTable.ajax.reload();
                                });
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    let errorMessages = Object.values(errors).flat().join("<br>");
                                    Swal.fire({
                                        title: "Validation Error",
                                        html: errorMessages,
                                        icon: "error"
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: xhr.responseJSON.message || "Something went wrong. Please try again.",
                                        icon: "error"
                                    });
                                }
                            }
                        });
                    }
                });
            }
        });
    })
</script>
@endcan
@endsection