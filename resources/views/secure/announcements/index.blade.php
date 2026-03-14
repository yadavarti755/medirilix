@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$addRoute = route('announcements.create');
@endphp
@can('add announcement')
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
                    <table id="announcements-datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Type</th>
                                <th>Preview</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Homepage Status</th>
                                <th>Is Approved</th>
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
        $('#announcements-datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: {
                url: "{{ route('announcements.fetch-for-datatable') }}",
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
                    }
                },
                {
                    data: 'type',
                    name: 'file_or_link'
                },
                {
                    data: 'preview',
                    name: 'preview'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: null,
                    name: 'status',
                    width: '14%',
                    render: function(data, type, row) {
                        if (data.status == 1) {
                            return '<span class="badge bg-success">' + data.status_desc + '</span>';
                        } else {
                            return '<span class="badge bg-primary">' + data.status_desc + '</span>';
                        }
                    }
                },
                {
                    data: null,
                    name: 'is_approved',
                    width: '10%',
                    render: function(data, type, row) {
                        if (data.is_approved == 1) {
                            return '<span class="badge bg-success">' + data.is_approved_desc + '</span>';
                        } else if (data.is_approved == 2) {
                            return '<span class="badge bg-danger">' + data.is_approved_desc + '</span>';
                        } else {
                            return '<span class="badge bg-warning">' + data.is_approved_desc + '</span>';
                        }
                    }
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
                    width: '12%'
                }
            ]
        });

        $(document).on('click', '.delete-announcement', function() {
            let id = $(this).data('id');
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
                        url: "{{ route('announcements.destroy', ':id') }}".replace(':id', id),
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
                            $('#announcements-datatable').DataTable().ajax.reload();
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