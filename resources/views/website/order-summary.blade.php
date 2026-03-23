@extends('layouts.website_layout')

@section('content')
@include('components.website.page-header')

<section class="cart-section">
    <div class="container">
        <div class="checkout-list-col px-lg-3 px-1">

            <!-- Show error -->
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('payment.initiate') }}" method="POST" id="payment-form">
                @csrf
                <div class="row mt-4">
                    <div class="col-lg-8">
                        <div class="os-summary-col">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="os-contact-info">
                                        <p>
                                            <span class="d-block"> <span id="os-name">You are logged in as
                                                    {{auth()->user()->name}}</span></span>
                                        </p>
                                    </div>

                                </div>

                                <div class="col-lg-12">
                                    <div class="os-address-col">
                                        <strong class="d-block mb-2">Delivery Address:</strong>
                                        <p class="os-address">
                                            <span class="d-block"><span
                                                    id="os-sa-name">{{$address->person_name}}</span></span>
                                            <span class="d-block">
                                                <span><span class="fw-lighter">Phone No.:
                                                    </span>{{$address->person_contact_number}}</span>
                                            </span>
                                            <span class="d-block">
                                                <span><span class="fw-lighter">Alternate Phone No.:
                                                    </span>{{$address->person_alt_contact_number}}</span>
                                            </span>
                                            {{$address->address}},
                                            {{$address->locality}},<br />
                                            Landmark: {{$address->landmark}},<br />
                                            {{$address->city.', '.$address->stateDetail->state_name . ', '. $address->countryDetail->name .'-'. $address->pincode}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="os-info-col mt-4">
                                <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">Select Payment Method</h5>

                                @if(isset($gateways) && count($gateways) > 0)
                                <div class="payment-gateways-list">
                                    @if($address->countryDetail->iso2 == 'IN')
                                    @foreach($gateways as $gateway)
                                    <!-- I want to show only razor pay if country is INDIA -->
                                    @if($gateway->gateway_name == 'RAZORPAY')
                                    <div class="payment-option mb-3">
                                        <label class="d-flex align-items-center p-3 border rounded shadow-sm cursor-pointer payment-label" for="gateway_{{ $gateway->id }}" style="cursor: pointer; transition: all 0.2s;">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="payment_gateway" id="gateway_{{ $gateway->id }}" value="{{ $gateway->id }}" {{ $loop->first ? 'checked' : '' }} style="transform: scale(1.2);">
                                            </div>
                                            <div class="ms-3">
                                                <span class="fw-bold text-dark h6 mb-0">{{ $gateway->gateway_name }}</span>
                                            </div>
                                        </label>
                                    </div>
                                    @endif
                                    @endforeach
                                    @else
                                    <div class="payment-option mb-3">
                                        <label class="d-flex align-items-center p-3 border rounded shadow-sm cursor-pointer payment-label" for="gateway_{{ $gateway->id }}" style="cursor: pointer; transition: all 0.2s;">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="payment_gateway" id="gateway_{{ $gateway->id }}" value="{{ $gateway->id }}" {{ $loop->first ? 'checked' : '' }} style="transform: scale(1.2);">
                                            </div>
                                            <div class="ms-3">
                                                <span class="fw-bold text-dark h6 mb-0">{{ $gateway->gateway_name }}</span>
                                            </div>
                                        </label>
                                    </div>
                                    @endif
                                </div>
                                <style>
                                    .payment-label:hover {
                                        background-color: #f8f9fa;
                                        border-color: #6f42c1 !important;
                                    }

                                    .payment-label:has(input:checked) {
                                        border-color: #6f42c1 !important;
                                        background-color: #f3e5f5;
                                    }
                                </style>
                                @else
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i> No active payment gateways found.
                                </div>
                                @endif

                                <div class="alert alert-light mt-3 border">
                                    <small class="d-block text-muted">
                                        <i class="fas fa-info-circle me-1"></i> After clicking "Place Order", you will be securely redirected to complete your payment.
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-4 mt-lg-0 mt-3">
                        <div class="checkout-list-wrapper">
                            @php
                            $cart = session()->get('cart', []);
                            @endphp

                            @foreach ($cart as $item)
                            @php
                            $product_id = $item['product_id'];
                            $name = $item['name'];
                            $price = $item['price'];
                            $qty = $item['qty'];
                            $image = $item['image'];
                            $attributes = $item['attributes'];
                            $subtotal = $price * $qty;
                            @endphp
                            <div class="checkout-col mb-3">
                                <div class="row align-items-center">
                                    <div class="col-lg-4 col-md-3 col-sm-3 col-4">
                                        <div class="checkout-product-image-col">
                                            <img src="{{ $image }}"
                                                alt="Image" class="checkout-img">
                                            <span class="checkout-product-count">{{ $qty }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-9 col-sm-9 col-8">
                                        <div class="checkout-product-info-wrapper">
                                            <div class="checkout-product-info">
                                                <p class="d-block"><small>{{ $name }}</small></p>
                                                {{-- <small class="d-block checkout-spec text-muted">
                                                <span>{{ $item->options->size }}</span>
                                                </small> --}}
                                            </div>
                                            <div class="checkout-product-price">
                                                <h6 class="checkout-price my-2 text-dark text-price">{!!
                                                    formatPriceWithReadableFormat($subtotal) !!}
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Cart Product Col --}}
                            @endforeach

                            @php
                            $totals = getCartTotals();
                            @endphp

                            {{-- Coupon Section --}}
                            <div class="coupon-section mb-3">
                                @if(session()->has('coupon_code'))
                                <div class="alert alert-success p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-tag"></i> <span>Coupon <strong>{{ session()->get('coupon_code') }}</strong> applied!</span>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-coupon" title="Remove Coupon">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    @if(isset($totals['coupon']))
                                    <small class="d-block mt-1">Discount: {!! formatPriceWithReadableFormat($totals['coupon']['discount_amount']) !!}</small>
                                    @endif
                                </div>
                                @else
                                <div class="input-group">
                                    <input type="text" class="form-control" name="coupon_code" id="coupon_code" placeholder="Enter Coupon Code">
                                    <button class="btn btn-purple btn-apply-coupon" type="button">Apply</button>
                                </div>
                                @endif
                            </div>
                            <hr>
                            {{-- End Coupon Section --}}

                            <div class="checkout-subtotal-col">
                                <h6 class="d-flex justify-content-between mb-3">
                                    <small>Subtotal</small>
                                    <p id="subtotal-price" class="text-price text-dark mb-0"> {!!
                                        formatPriceWithReadableFormat($totals['subtotal']) !!}</strong>
                                </h6>
                                @if($totals['discount'] > 0)
                                <h6 class="d-flex justify-content-between mb-3">
                                    <small>Discount</small>
                                    <p class="text-price text-success mb-0">-{!! formatPriceWithReadableFormat($totals['discount']) !!}</strong>
                                </h6>
                                @endif
                                @if($totals['tax'] > 0)
                                <h6 class="d-flex justify-content-between">
                                    <small>Tax ({{ config('constants.tax_percentage') }}%)</small>
                                    <p class="text-price text-dark mb-0">{!! formatPriceWithReadableFormat($totals['tax']) !!}</strong>
                                </h6>
                                @endif
                                <h6 class="d-flex justify-content-between">
                                    <small>Shipping</small>
                                    <p id="subtotal-price" class="text-price text-dark mb-0"> {!!
                                        formatPriceWithReadableFormat(calculateShippingCharges()) !!}</strong>
                                </h6>
                            </div>
                            <div class="checkout-total-col mt-3">
                                <h6 class="d-flex justify-content-between mb-3">
                                    <small>Total</small>
                                    <strong id="subtotal-price" class="fs-4 text-price text-custom">{!!
                                        formatPriceWithReadableFormat($totals['total'] + calculateShippingCharges()) !!}</strong>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-purple" id="os-confirm-btn">Place The Order <span class="fas fa-check"></span></button>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
@section('pages-scripts')
<script @cspNonce>
    // Apply Coupon
    $(document).on('click', '.btn-apply-coupon', function() {
        let code = $('#coupon_code').val();
        if (!code) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Required',
                text: 'Please enter a coupon code.',
            });
            return;
        }

        $.ajax({
            url: "{{ route('apply-coupon') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                code: code
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again.',
                });
            }
        });
    });

    // Remove Coupon
    $(document).on('click', '.btn-remove-coupon', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to remove the applied coupon?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('remove-coupon') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            }
        })
    });
</script>
@endsection