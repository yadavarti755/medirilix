@extends('layouts.app_layout')

@section('content')

@php
$addRoute = route('products.create');
@endphp

@can('add product')
@php
$button = '<a href="'.$addRoute.'" class="btn btn-primary"><i class="fa fa-plus"></i> Add Product</a>';
@endphp
@endcan

<x-page-header title="{{ $pageTitle }}" button="{!! $button ?? '' !!}" />

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="products-datatable" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>MRP</th>
                                <th>Selling Price</th>
                                <th>Quantity</th>
                                <th>Available Qty</th>
                                <th>Stock</th>
                                <th>Published</th>
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

        $('#products-datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            ajax: {
                url: "{{ route('products.fetch-for-datatable') }}",
                type: "POST",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                }
            },
            columns: [

                // Serial number
                {
                    data: null,
                    name: 'id',
                    width: '6%',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },

                // Featured Image
                {
                    data: 'featured_image_full_path',
                    name: 'featured_image',
                    width: '10%',
                    render: function(data) {
                        return `<img src="${data}" class="img-thumbnail" width="60">`;
                    }
                },

                {
                    data: 'name',
                    name: 'name'
                },

                {
                    data: 'mrp',
                    name: 'mrp'
                },

                {
                    data: 'selling_price',
                    name: 'selling_price'
                },

                {
                    data: 'quantity',
                    name: 'quantity'
                },

                {
                    data: 'available_quantity',
                    name: 'available_quantity'
                },

                {
                    data: 'stock_availability',
                    name: 'stock_availability',
                    width: '10%',
                    render: function(data) {
                        return data == 1 ?
                            '<span class="badge bg-success">In Stock</span>' :
                            '<span class="badge bg-danger">Out of Stock</span>';
                    }
                },

                {
                    data: 'is_published',
                    name: 'is_published',
                    width: '10%',
                    render: function(data) {
                        return data == 1 ?
                            '<span class="badge bg-success">Published</span>' :
                            '<span class="badge bg-warning">Draft</span>';
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
         * Delete Product
         */
        $(document).on('click', '.delete-product', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this product.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Delete",
                cancelButtonText: "Cancel"
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('products.destroy', ':id') }}".replace(':id', id),
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
                            $('#products-datatable').DataTable().ajax.reload();
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