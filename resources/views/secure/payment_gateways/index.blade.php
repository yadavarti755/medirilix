@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@can('add payment gateway')
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
                                <th>Name</th>
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

@canany(['add payment gateway', 'edit payment gateway'])
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
                        <label for="gateway_name" class="form-label">Gateway Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="gateway_name" id="gateway_name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="app_id" class="form-label">App ID <small class="text-muted">(Optional, for PayPal)</small></label>
                        <input type="text" class="form-control" name="app_id" id="app_id">
                    </div>

                    <div class="form-group mb-3">
                        <label for="client_id_or_key" class="form-label">Client ID / Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="client_id_or_key" id="client_id_or_key" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="client_secret" class="form-label">Client Secret <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="client_secret" id="client_secret" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" id="image" accept="image/*">
                        <small class="text-muted">Allowed formats: jpeg, png, jpg, gif, svg. Max size: 2MB.</small>
                    </div>

                    <div class="form-group mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Is Active?</label>
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
                url: "{{ route('payment-gateways.fetch-for-datatable') }}",
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
                    width: '10%',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'gateway_name',
                    name: 'gateway_name'
                },
                {
                    data: 'status',
                    name: 'is_active',
                    width: '10%'
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '12%',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#formModal').on('hidden.bs.modal', function() {
            $('#formModalLabel').html('');
            $('#formSubmitBtn').html('');
            $('#form').trigger('reset');
            $('#is_active').prop('checked', true); // Reset to default checked
        });
    });
</script>

@canany(['add payment gateway', 'edit payment gateway'])
<script @cspNonce>
    $(document).ready(function() {
        // On submitting the form
        $('#form').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(document.getElementById('form'));

            // Handle checkbox value for FormData manually if needed, usually unchecked checkboxes are not sent
            if (!$('#is_active').is(':checked')) {
                formData.set('is_active', '0');
            } else {
                formData.set('is_active', '1');
            }

            // Check the operation type
            var url;
            if (operationType == 'EDIT') {
                url = "{{ route('payment-gateways.update', ':id') }}".replace(':id', recordId);
            } else if (operationType == 'ADD') {
                url = "{{ route('payment-gateways.store') }}";
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

@can('add payment gateway')
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

@can('edit payment gateway')
<script @cspNonce>
    $(document).ready(function() {
        // Onclick edit button
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            if (id) {
                operationType = "EDIT";
                $.ajax({
                    url: "{{ route('payment-gateways.fetch-one', ':id') }}".replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success == true) {
                            var data = response.data;
                            recordId = data.id;
                            $('#gateway_name').val(data.gateway_name);
                            $('#app_id').val(data.app_id);
                            $('#client_id_or_key').val(data.client_id_or_key);
                            $('#client_secret').val(data.client_secret);

                            if (data.is_active == 1) {
                                $('#is_active').prop('checked', true);
                            } else {
                                $('#is_active').prop('checked', false);
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

@can('delete payment gateway')
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
                            url: "{{ route('payment-gateways.destroy', ':id') }}".replace(':id', id),
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