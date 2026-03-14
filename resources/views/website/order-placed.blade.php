@extends('layouts.website_layout')

@section('content')
@include('components.website.page-header')
<section class="cart-section">
    <div class="container">

        <div class="checkout-list-col">
            <div class="row">
                <div class="col-lg-12 px-lg-3 px-1">
                    <div class="row">
                        <div class="col-md-6 col-12 mx-auto">
                            <div class="card card-body border-0 checkout-list-wrapper">
                                <span class="text-center mb-4 d-block">
                                    <img src="{{asset('assets/images/icons/success.png')}}" alt="Success" width="50" height="50">
                                </span>
                                <h4 class="text-center">Thank You For Your Order</h4>
                                <p class="mb-1 text-center">
                                    Your order placed successfully.
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
                                @if (Auth::check())
                                <div class="mt-2 text-center">
                                    <a href="{{route('user.orders')}}" class="btn btn-purple">
                                        View Order <i class="fas fa-long-arrow-right"></i>
                                    </a>
                                </div>
                                @else
                                <p class="mb-2 mt-2">Login to view the order details</p>
                                <a href="{{route('my-account')}}" class="btn btn-purple">
                                    Login <i class="fas fa-long-arrow-right"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection