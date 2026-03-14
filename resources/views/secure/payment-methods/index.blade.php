@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@can('add payment method')
@php
$button = '<button type="button" class="btn btn-primary btn-add"><i class="fa fa-plus"></i> Add New</button>';
@endphp
@endcan
<x-page-header title="{{ $pageTitle }}" :button="$button ?? ''" />

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
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@canany(['add payment method', 'edit payment method'])
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
                    <div class="form-group mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" id="image" accept=".png,.jpg,.jpeg,.webp" />
                        <small class="text-muted">
                            Allowed types: png, jpg, jpeg, webp. Max size 2MB.
                        </small>
                        <div id="imagePreview" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">Save changes</button>
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
                url: "{{ route('payment-methods.fetch-for-datatable') }}",
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
                        return meta.row + 1;
                    }
                },
                {
                    data: 'file',
                    name: 'image',
                    width: '20%'
                },
                {
                    data: 'title',
                    name: 'title'
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
            $('#formSubmitBtn').html('Save changes');
            $('#form').trigger('reset');
            $('#imagePreview').html('');
        });
    });
</script>

@canany(['add payment method', 'edit payment method'])
<script @cspNonce>
    $(document).ready(function() {
        $('#form').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            var url;
            if (operationType == 'EDIT') {
                url = "{{ route('payment-methods.update', ':id') }}".replace(':id', recordId);
            } else {
                url = "{{ route('payment-methods.store') }}";
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
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
                    hideLoader();
                    handleAjaxError(xhr);
                }
            });
        });
    });
</script>
@endcan

@can('add payment method')
<script @cspNonce>
    $(document).ready(function() {
        $('.btn-add').on('click', function() {
            operationType = "ADD";
            $('#formModalLabel').html('<i class="fas fa-plus"></i> Add New Payment Method');
            $('#formSubmitBtn').html('<i class="fas fa-plus"></i> Submit');
            $('#formModal').modal('show');
        });
    });
</script>
@endcan

@can('edit payment method')
<script @cspNonce>
    $(document).ready(function() {
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            if (id) {
                operationType = "EDIT";
                $.ajax({
                    url: "{{ route('payment-methods.fetch-one', ':id') }}".replace(':id', id),
                    type: 'GET',
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.success) {
                            var data = response.data;
                            recordId = data.id;
                            $('#title').val(data.title);
                            if (data.file_url) {
                                $('#imagePreview').html('<img src="' + data.file_url + '" class="img-fluid" style="max-height: 100px;">');
                            }

                            $('#formModalLabel').html('<i class="fas fa-edit"></i> Edit Payment Method');
                            $('#formSubmitBtn').html('<i class="fas fa-edit"></i> Update');
                            $('#formModal').modal('show');
                        } else {
                            toastr.error("Error while fetching data");
                        }
                    },
                    error: function(xhr) {
                        hideLoader();
                        handleAjaxError(xhr);
                    }
                });
            }
        });
    });
</script>
@endcan

@can('delete payment method')
<script @cspNonce>
    $(document).ready(function() {
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
                            url: "{{ route('payment-methods.destroy', ':id') }}".replace(':id', id),
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            beforeSend: function() {
                                showLoader();
                            },
                            success: function(response) {
                                hideLoader();
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
                                hideLoader();
                                handleAjaxError(xhr);
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endcan
@endsection