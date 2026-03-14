@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$addRoute = route('social-medias.create');
@endphp
@can('add social-media')
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
                    <table id="social-medias-datatable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Icon Class</th>
                                <th>URL</th>
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
        $('#social-medias-datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: {
                url: "{{ route('social-medias.fetch-for-datatable') }}",
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
                    data: 'social_media_platform.name',
                    name: 'type'
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'icon_class',
                    name: 'icon_class',
                },
                {
                    data: 'url',
                    name: 'url',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Delete contact detail
        $(document).on('click', '.delete-social-media', function() {
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
                        url: "{{ route('social-medias.destroy', ':id') }}".replace(':id', contactDetailId),
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
                            $('#social-medias-datatable').DataTable().ajax.reload();
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