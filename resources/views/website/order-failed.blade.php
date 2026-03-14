@extends('layouts.website_layout')

@section('content')
<x-page-header pageTitle={{$pageTitle}} />
<section class="cart-section">
    <div class="container">

        <div class="checkout-list-col">
            <div class="row">
                <div class="col-lg-12 px-lg-3 px-1">
                    <div class="row">
                        <div class="col-md-6 col-12 mx-auto">
                            <div class="card card-body border-0 checkout-list-wrapper">
                                <span class="text-center mb-4 d-block">
                                    <img src="{{asset('assets/images/icons/failed.png')}}" alt="Failed" style="width: 60px;">
                                </span>
                                <h4 class="text-center">Order Failed</h4>
                                <p class="mb-1 text-center">
                                    Your order processing is failed. We are sorry for the inconvienence. If any money is deducted from your account contact us. <a href="{{ route('contact-us') }}"></a>
                                    Below are the details of your order:
                                </p>
                                <table class="table w-100 table-bordered mt-3">
                                    <thead class="bg-white">
                                        <tr>
                                            <th width="40%">Order Number</th>
                                            <th>{{$orderNumber}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Order Date</td>
                                            <td>{{ readableFormatDateTime($order->order_date) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Subtotal</td>
                                            <td>{!! formatPriceWithReadableFormat($order->subtotal_price) !!}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-white">
                                        <tr>
                                            <th>Total</th>
                                            <th>{!! formatPriceWithReadableFormat($order->total_price) !!}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <div class="mt-4 text-center">
                                    <p class="text-muted small">
                                        Note: If payment detected then the order status will be changed in 2 hours.
                                    </p>
                                    <a href="{{ route('checkout') }}" class="btn btn-danger">
                                        <i class="fas fa-redo me-2"></i> Try Payment Again
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection