@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$addRoute = route('users.create');
@endphp
@can('add user')
@php
$button = '<a href="'.$addRoute.'" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>';
@endphp
@endcan
<x-page-header title="{{ $pageTitle }}" button="{!! (isset($button))?$button:'' !!}" />

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="users-datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Roles</th>
                                <!-- <th>Permissions</th> -->
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        $('#users-datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: {
                url: "{{ route('users.fetch-for-datatable') }}",
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'mobile_number',
                    name: 'mobile_number'
                },
                {
                    data: 'roles',
                    name: 'roles'
                },
                // {
                //     data: 'permissions',
                //     name: 'permissions'
                // },
                {
                    data: 'action',
                    name: 'action',
                    width: '12%',
                }
            ]
        });

        /**
         * Delete record
         */
        $(document).on('click', '.delete-user', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this record.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('users.destroy', ':id') }}".replace(':id', id),
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            showLoader();
                        },
                        success: function(response) {
                            hideLoader();
                            Swal.fire("Deleted!", response.message, "success");
                            $('#users-datatable').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            hideLoader();
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        });

        $(document).on('click', '.btn-reset-password', function() {
            let id = $(this).data('id');
            if (id) {
                $.ajax({
                    url: "{{ route('users.reset-password', ':id') }}".replace(':id', id),
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "{{ route('users.index') }}";
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
                                text: "Something went wrong. Please try again.",
                                icon: "error"
                            });
                        }
                    }
                });
            }
        })
    });
</script>
@endsection