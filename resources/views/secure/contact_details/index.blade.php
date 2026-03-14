@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$addRoute = route('contact-details.create');
@endphp
@can('add contact-detail')
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
                <div class="table-responsive">
                    <table id="contact-details-datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Address</th>
                                <th>Phone Numbers</th>
                                <th>Email IDs</th>
                                <th>Is Primary</th>
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
        $('#contact-details-datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('contact-details.fetch-for-datatable') }}",
                type: "POST",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                }
            },
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    width: '8%',
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'phone_numbers',
                    name: 'phone_numbers',
                },
                {
                    data: 'email_ids',
                    name: 'email_ids',
                },
                {
                    data: 'is_primary_desc',
                    name: 'is_primary',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    width: '12%',
                }
            ]
        });

        // Delete contact detail
        $(document).on('click', '.delete-contact-detail', function() {
            let contactDetailId = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this contact detail.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('contact-details.destroy', ':id') }}".replace(':id', contactDetailId),
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
                            $('#contact-details-datatable').DataTable().ajax.reload();
                        },
                        error: function() {
                            hideLoader();
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        });
    });
</script>
@endsection