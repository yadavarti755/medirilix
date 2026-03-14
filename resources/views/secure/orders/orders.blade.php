@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$button = '';
@endphp
<x-page-header title="{{ $pageTitle }}" :button="$button" />
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
                                <th>Total</th>
                                <th>Payment Type</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
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
    let dataTable;

    $(document).ready(function() {

        dataTable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            ajax: {
                url: "{{ route('orders.fetch-for-admin-datatable') }}",
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
                    data: 'total_price',
                    name: 'total_price'
                },
                {
                    data: 'payment_type',
                    name: 'payment_type'
                },
                {
                    data: 'order_status_desc',
                    name: 'order_status'
                },
                {
                    data: 'payment_status',
                    name: 'payment_status'
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '10%',
                }
            ]
        });
    });
</script>
@endsection