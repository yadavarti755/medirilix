@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@can('add offer')
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
                                <th>Image</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Related Item</th>
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

@canany(['add offer', 'edit offer'])
{{-- Modal --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="formModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" id="form" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="title">
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description"></textarea>
                    </div>
                    <div class="form-group mb-2">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-control" name="type" id="type">
                            <option value="">Select Type</option>
                            <option value="product">Product</option>
                            <option value="category">Category</option>
                        </select>
                    </div>

                    <div class="form-group mb-2 d-none" id="product_div">
                        <label for="product_id" class="form-label">Select Product</label>
                        <select class="form-control" name="product_id" id="product_id">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-2 d-none" id="category_div">
                        <label for="category_id" class="form-label">Select Category</label>
                        <select class="form-control" name="category_id" id="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="type_id" id="type_id">
                    <div class="form-group">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" id="image" accept=".png,.jpg,.jpeg" />
                        <small class="text-danger">
                            Only png, jpg, jpeg files are allowed. Max size 2MB
                        </small>
                    </div>

                    <div class="form-group mb-2">
                        <label for="is_active" class="form-label">Is Active</label>
                        <select class="form-control" name="is_active" id="is_active">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
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
                url: "{{ route('offers.fetch-for-datatable') }}",
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
                    data: 'image',
                    name: 'image',
                    width: '20%'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'related_item',
                    name: 'related_item',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
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
        });
    });
</script>

@canany(['add offer', 'edit offer'])
<script @cspNonce>
    // Dropdown toggle logic
    function toggleTypeDropdown() {
        let type = $('#type').val();
        $('#product_div').addClass('d-none');
        $('#category_div').addClass('d-none');

        if (type === 'product') {
            $('#product_div').removeClass('d-none');
        } else if (type === 'category') {
            $('#category_div').removeClass('d-none');
        }
    }

    $(document).ready(function() {

        // Bind change event
        $(document).on('change', '#type', function() {
            toggleTypeDropdown();
        });

        // On submitting the form
        $(document).on('submit', '#form', function(event) {
            event.preventDefault();

            // Set type_id based on selection
            let type = $('#type').val();
            if (type === 'product') {
                $('#type_id').val($('#product_id').val());
            } else if (type === 'category') {
                $('#type_id').val($('#category_id').val());
            }

            var formData = new FormData(document.getElementById('form'));
            // Check the operation type
            var url;
            if (operationType == 'EDIT') {
                url = "{{ route('offers.update', ':id') }}".replace(':id', recordId);
            } else if (operationType == 'ADD') {
                url = "{{ route('offers.store') }}";
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

        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            if (id) {
                operationType = "EDIT";
                $.ajax({
                    url: "{{ route('offers.fetch-one', ':id') }}".replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success == true) {
                            var data = response.data;
                            recordId = data.id;
                            $('#title').val(data.title);
                            $('#description').val(data.description);
                            $('#type').val(data.type);

                            // Trigger change to show correct dropdown
                            toggleTypeDropdown();

                            if (data.type === 'product') {
                                $('#product_id').val(data.type_id);
                            } else if (data.type === 'category') {
                                $('#category_id').val(data.type_id);
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
                                "title": "Validation Error",
                                "html": errorMessages,
                                "icon": "error"
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

@can('add offer')
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

@can('edit offer')
<script @cspNonce>
    $(document).ready(function() {
        // Onclick edit button
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            if (id) {
                operationType = "EDIT";
                $.ajax({
                    url: "{{ route('offers.fetch-one', ':id') }}".replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success == true) {
                            var data = response.data;
                            recordId = data.id;
                            $('#title').val(data.title);
                            $('#description').val(data.description);
                            $('#type').val(data.type);

                            // Trigger change to show correct dropdown
                            toggleTypeDropdown();

                            if (data.type === 'product') {
                                $('#product_id').val(data.type_id);
                            } else if (data.type === 'category') {
                                $('#category_id').val(data.type_id);
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

@can('delete offer')
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
                            url: "{{ route('offers.destroy', ':id') }}".replace(':id', id),
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