@extends('layouts.website_layout')

@section('content')
@include('components.website.page-header')
<section class="cart-section">
    <div class="container">

        <div class="checkout-list-col">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-2">
                        <h5 class="checkout-title mb-0">{{(Auth::check())?'My Account':'Login'}}</h5>
                    </div>
                    <div class="checkout-sa-col mb-2">
                        @if (Auth::check())
                        <p class="mb-0 text-size-custom"><small>
                                You are logged in as <strong>{{ Auth::user()->name }}</strong>. Do you want to change
                                account? <a href="{{ route('logout') }}" class="btn btn-purple" id="btn-logout">Logout
                                    <i class="fas fa-power-off"></i></a></small></p>
                        @else
                        <div class="card bg-white mt-3 border-0">
                            <div class="card-body px-md-5 px-3">
                                <div class="mb-3">
                                    <h6 class="mb-1">Returning customer?</h6>
                                    <p class="text-muted small">Login to your account to proceed with checkout.</p>
                                </div>

                                <div class="w-100 text-center py-3">
                                    <button type="button" class="btn btn-custom px-4 py-2 rounded-4" data-bs-toggle="modal" data-bs-target="#modalLoginForm">
                                        <i class="fa fa-sign-in-alt me-2"></i> Login to Checkout
                                    </button>
                                </div>

                                <div class="mt-4 text-center">
                                    <p class="mb-0 text-muted small">Don't have an account?</p>
                                    <a href="{{ route('public.register') }}" class="btn btn-outline-purple btn-sm mt-2">Create New Account</a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if (Auth::check())
                    <form action="" class="checkout-sa-form floating-form" id="checkout-sa-form">
                        @csrf

                        <div class="checkout-header">
                            <h5 class="checkout-title mb-0">Payment Method</h5>
                        </div>

                        <div class="checkout-sa-col mb-2">
                            <div class="row">
                                <div class=" col-lg-12 col-sm-12 col-12">
                                    <div class="form-floating">
                                        <select name="payment_method" id="payment_method"
                                            class="form-control rounded-4">
                                            @foreach (Config::get('constants.payment_options') as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                        <label for="payment_method">Select Payment Method</label>
                                    </div>
                                </div>
                                @if(Config::get('constants.cod_payment_options') == false)
                                <div class="col-12">
                                    <small class="text-muted">
                                        Currently, COD is not available.
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="checkout-header mt-3">
                            <h5 class="checkout-title mb-0">Shipping Address</h5>
                        </div>

                        <div class="checkout-sa-col">
                            <div class="address_list">
                                @if(count($userAddress) > 0)
                                <small class="mb-2 d-block">Click on address box or small circle to select address</small>
                                @foreach ($userAddress as $address)
                                <div class="address_col" id="{{$address->id}}">
                                    <div class="address_content">
                                        <p class="mb-1 fw-bold">{{$address->person_name}}</p>
                                        <p class="mb-1">{{$address->person_contact_number}}</p>
                                        <p class="mb-1">{{$address->person_alt_contact_number}}</p>
                                        <p class="mb-1">{{$address->address.', '.$address->locality}}</p>
                                        <p class="mb-1">Landmark: {{$address->landmark}}</p>
                                        <p class="mb-1">{{$address->city.', '.$address->state_name.'-'.$address->pincode}}
                                        </p>
                                    </div>
                                    <div class="address_checked_col">
                                        {{-- <input type="radio" name="address_checked" id="address_checked">
                                                 --}}
                                        <label class="address_checked_label">
                                            <input type="radio" name="address_checked"
                                                id="address_checked_{{$address->id}}" class="address_checked" value="{{$address->id}}" />
                                            <div>
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                                @endif

                                <button class="btn btn-black" type="button" id="add_new_address">
                                    <i class="fas fa-plus"></i> Add New Address
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('cart') }}" class="text-purple fw-bold"><i
                                        class="fas fa-arrow-left"></i> Go back to cart</a>
                                <button type="submit" class="btn btn-purple" id="sa-submit-btn">Proceed <i
                                        class="fas fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-danger">
                        <p class="mb-0">Please login to proceed the payment process.</p>
                    </div>
                    @endif
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
                                            <p class="d-block"><small>{{ $name }}</small></strong>
                                                @if(!empty($attributes['size']))
                                                <small class="d-block cart-product-spec text-muted">
                                                    <span>Size: {{ $attributes['size'] }}</span>
                                                </small>
                                                @endif
                                                @if(!empty($attributes['color']))
                                                <small class="d-block cart-product-spec text-muted">
                                                    <span>Color: {{ $attributes['color'] }}</span>
                                                </small>
                                                @endif
                                                <small class="cart-product-category d-block text-muted">{{$attributes['category_name']}}</small>
                                            </p>
                                        </div>
                                        <div class="checkout-product-price">
                                            <h6 class="checkout-price fw-bold text-muted text-price">
                                                {!! formatPriceWithReadableFormat($price) !!}
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
                                <p id="subtotal-price" class="text-price text-dark mb-0">{!! formatPriceWithReadableFormat($totals['subtotal']) !!}</strong>
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
                            <h6 class="d-flex justify-content-between mb-3">
                                <small>Shipping</small>
                                <p class="text-price text-dark mb-0">{!! formatPriceWithReadableFormat($totals['shipping']) !!}</strong>
                            </h6>
                        </div>
                        <div class="checkout-total-col mt-3">
                            <h6 class="d-flex justify-content-between mb-3">
                                <small>Total</small>
                                {{-- Total = CartController Total + Shipping --}}
                                <strong id="subtotal-price" class="fs-5 fs-sm-4 text-price text-custom"> {!! formatPriceWithReadableFormat($totals['total'] + calculateShippingCharges()) !!}</strong>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

@include('includes.modals.address-modal')

@endsection

@section('pages-scripts')

<!-- Address js -->
<script @cspNonce src="{{asset('website_assets/js/address.js')}}"></script>

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
                    if (response.message.includes('login')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Login Required',
                            text: response.message,
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Login Now'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modalLoginForm').modal('show');
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
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

<script @cspNonce>
    $('.address_col').on('click', function() {
        let id = $(this).attr('id');
        if (id) {
            $('#address_checked_' + id).prop('checked', true)
        }
    })

    $('#add_new_address').on('click', function() {
        $('.new_address_col').slideToggle();
    })


    $("#checkout-sa-form").validate({
        errorClass: 'text-danger validation-error',
        rules: {
            payment_method: {
                required: true
            }
        },
        submitHandler: function(form, event) {
            event.preventDefault();
            if (!$('.address_checked').is(':checked')) {
                toastr.error('Please select address.');
                return;
            }

            var formData = new FormData(document.getElementById("checkout-sa-form"));
            $.ajax({
                url: base_url + "/proceed-checkout",
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    if (response.status == true) {
                        window.location.href = response.redirect_to
                    } else if (response.status === 'validation_error') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: response.message,
                            confirmButtonText: 'OK'
                        });
                    } else if (response.status === false) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: response.message,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }

                }
            });
        }
    });
</script>
@endsection