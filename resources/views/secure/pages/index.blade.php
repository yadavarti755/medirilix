@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$addRoute = route('pages.create');
@endphp
@can('add page')
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
                    <table id="pages-datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Page Title</th>
                                <th>Page URL</th>
                                <th>Menu Name</th>

                                <th>Is Published</th>
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
        $('#pages-datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: {
                url: "{{ route('pages.fetch-for-datatable') }}",
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
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'slug',
                    name: 'slug'
                },
                {
                    data: 'menu.title',
                    name: 'menu.title',
                    width: '20%',
                },

                {
                    data: null,
                    name: 'is_published',
                    width: '10%',
                    render: function(data, type, row) {
                        if (data.is_published == 1) {
                            return '<span class="badge bg-success">' + data.is_published_desc + '</span>';
                        } else {
                            return '<span class="badge bg-warning">' + data.is_published_desc + '</span>';
                        }
                    }
                },
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
        $(document).on('click', '.delete-page', function() {
            let roleId = $(this).data('id');

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
                        url: "{{ route('pages.destroy', ':id') }}".replace(':id', roleId),
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
                            $('#pages-datatable').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
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