@extends('layouts.app_layout')

@section('content')
<div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page">Customer Reviews</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="d-flex justify-content-between">
                        <div class="page-header-title">
                            <h5 class="mb-0">Customer Reviews</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table_data" class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>User</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        var table = $('#table_data').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('customer-reviews.fetch-for-datatable') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [{
                    data: 'product_name',
                    name: 'product.name'
                },
                {
                    data: 'user_name',
                    name: 'user.name'
                },
                {
                    data: 'rating',
                    name: 'rating'
                },
                {
                    data: 'status',
                    name: 'is_active'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // Delete Review
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('customer-reviews.destroy', 'placeholder') }}".replace('placeholder', id),
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Something went wrong!');
                        }
                    });
                }
            })
        });

        // Toggle Status
        $(document).on('click', '.btn-toggle-status', function() {
            var id = $(this).data('id');
            var status = $(this).data('status'); // 1 or 0

            $.ajax({
                url: "{{ route('customer-reviews.update-status', 'placeholder') }}".replace('placeholder', id),
                type: 'POST',
                data: {
                    status: status
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Something went wrong!');
                }
            });
        });
    });
</script>
@endsection