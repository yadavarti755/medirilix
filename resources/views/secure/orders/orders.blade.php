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
            <div class="card-header">
                <h5>Filters</h5>
            </div>
            <div class="card-body">
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Order Number</label>
                            <input type="text" class="form-control" id="filter_order_number" placeholder="Order Number">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Order Status</label>
                            <select class="form-control" id="filter_order_status">
                                <option value="">All Statuses</option>
                                @foreach(config('constants.order_status_text') as $key => $value)
                                @if($key != 'PENDING')
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Order Date</label>
                            <input type="date" class="form-control" id="filter_order_date">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Payment Status</label>
                            <select class="form-control" id="filter_payment_status">
                                <option value="">All Statuses</option>
                                @foreach(config('constants.payment_status_codes') as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-2">
                            <button type="button" class="btn btn-primary" id="btn-filter">Filter</button>
                            <button type="button" class="btn btn-secondary" id="btn-reset">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                    d.order_number = $('#filter_order_number').val();
                    d.order_status = $('#filter_order_status').val();
                    d.order_date = $('#filter_order_date').val();
                    d.payment_status = $('#filter_payment_status').val();
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

        $('#btn-filter').click(function() {
            dataTable.draw();
        });

        $('#btn-reset').click(function() {
            $('#filter-form')[0].reset();
            dataTable.draw();
        });
    });
</script>
@endsection