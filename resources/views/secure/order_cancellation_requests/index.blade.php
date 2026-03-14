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
                                <th>Order No</th>
                                <th>Order Date</th>
                                <th>Product Name</th>
                                <th>Order Status</th>
                                <th>Cancellation Reason</th>
                                <th>Request Status</th>
                                <th>Action</th>
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
    let dataTable;
    $(document).ready(function() {

        dataTable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            ajax: {
                url: "{{ route('order-cancellation-requests.fetch-data') }}",
                type: "POST",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                }
            },
            columns: [{
                    data: null,
                    name: 'id',
                    width: '5%',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'order_number',
                    name: 'order_number'
                },
                {
                    data: 'order_date',
                    name: 'order_date'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'product_order_status',
                    name: 'product_order_status'
                },
                {
                    data: 'cancellation_reason',
                    name: 'cancellation_reason'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '10%',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });
</script>
@endsection