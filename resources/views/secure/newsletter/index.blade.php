@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" />

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
                                <th>Email</th>
                                <th>Subscribed At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@can('delete newsletter')
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
                            url: "{{ route('newsletters.destroy', ':id') }}".replace(':id', id),
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

@section('pages-scripts')
<script @cspNonce>
    let dataTable;

    $(document).ready(function() {

        dataTable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            ajax: {
                url: "{{ route('newsletters.fetch-for-datatable') }}",
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
                    data: 'email_id',
                    name: 'email_id'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : '-';
                    }
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
    });
</script>
@endsection