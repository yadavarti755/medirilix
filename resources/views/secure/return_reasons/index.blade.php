@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@can('add return reason')
@php
$button = '<button type="button" class="btn btn-primary btn-add"><i class="fa fa-plus"></i> Add New</button>';
@endphp
@endcan
<x-page-header title="{{ $pageTitle }}" :button="$button ?? null" />

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

@canany(['add return reason', 'edit return reason'])
{{-- Modal --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="formModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" id="form">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="title" required>
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
                url: "{{ route('return-reasons.fetch.data') }}",
                type: "GET",
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
                    data: 'title',
                    name: 'title'
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
        });
    });
</script>

@canany(['add return reason', 'edit return reason'])
<script @cspNonce>
    $(document).ready(function() {
        // On submitting the form
        $('#form').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(document.getElementById('form'));
            // Check the operation type
            var url;
            if (operationType == 'EDIT') {
                url = "{{ route('return-reasons.update', ':id') }}".replace(':id', recordId);
                formData.append('_method', 'PUT'); // Needed for PUT requests via FormData
            } else if (operationType == 'ADD') {
                url = "{{ route('return-reasons.store') }}";
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

@can('add return reason')
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

@can('edit return reason')
<script @cspNonce>
    $(document).ready(function() {
        // Onclick edit button
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            if (id) {
                operationType = "EDIT";
                $.ajax({
                    url: "{{ route('return-reasons.fetch.one', ':id') }}".replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success == true) {
                            var data = response.data;
                            recordId = data.id;
                            $('#title').val(data.title);

                            $('#formModalLabel').html('<i class="fas fa-edit"></i> Edit Record');
                            $('#formSubmitBtn').html('<i class="fas fa-edit"></i> Update');
                            $('#formModal').modal('show');
                        } else {
                            toastr.error("Error while fetching data");
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Error!",
                            text: "Something went wrong. Please try again.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    })
</script>
@endcan

@can('delete return reason')
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
                            url: "{{ route('return-reasons.delete', ':id') }}".replace(':id', id),
                            type: "POST", // Using POST with _method DELETE if route expects strict DELETE, but often 'POST' to a delete route works if defined as such, checking web.php line 63 it is 'delete' method so we need _method
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
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
                                Swal.fire({
                                    title: "Error!",
                                    text: xhr.responseJSON.message || "Something went wrong. Please try again.",
                                    icon: "error"
                                });
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