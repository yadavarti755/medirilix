@extends('layouts.website_layout')
@section('content')
@include('components.website.page-header')
<section class="cart-section">
    <div class="container">
        <div class="cart-product-list-col">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cart-product-list-wrapper">
                        <div class="cart-product-col">
                            @php
                            $cart = session()->get('cart', []);
                            @endphp
                            @if (count($cart) > 0)
                            <div class="cart-product">
                                <table class="table custom-table" id="cart-table">
                                    <thead>
                                        <tr>
                                            <th width="10%"></th>
                                            <th>Product</th>
                                            <th width="10%" class="text-center">Price</th>
                                            <th width="20%" class="text-center">Qty.</th>
                                            <th width="10%" class="text-center">Subtotal</th>
                                            <th width="10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cart as $unique_id => $item)
                                        @php
                                        $product_id = $item['product_id'];
                                        $name = $item['name'];
                                        $price = $item['price'];
                                        $qty = $item['qty'];
                                        $image = $item['image'];
                                        $attributes = $item['attributes'];
                                        $subtotal = $price * $qty;
                                        @endphp
                                        <tr>
                                            <td class="cart-table-image-td">
                                                <a href="{{ route('product-details', ['slug' => $attributes['product_slug']]) }}"
                                                    class="me-3">
                                                    <img src="{{ $image }}"
                                                        alt="Image" class="cart-product-img">
                                                </a>
                                            </td>
                                            <td class="cart-table-name-td">
                                                <p class="cart-product-title d-block mb-0">{{$name}}</p>
                                                <small class="cart-product-category d-block text-muted">{{$attributes['category_name']}}</small>
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
                                            </td>
                                            <td class="text-center cart-table-mrp-td">
                                                <p class="cart-product-price my-3 fw-bold text-dark text-price">
                                                    {!! formatPriceWithReadableFormat($price) !!}
                                                </p>
                                            </td>
                                            <td class="text-center cart-table-qty-td">
                                                <div class="pd-quantity-col">
                                                    <div class="input-group pd-quantity-input justify-content-center">
                                                        <button
                                                            class="btn btn-outline-secondary pd-quantity-minus-btn cart-qty-btn"
                                                            type="button"
                                                            data-unique-id="{{ $unique_id }}">
                                                            <span class="fas fa-minus"></span>
                                                        </button>
                                                        <input type="number" class="text-center cart-qty-input"
                                                            id="cart-qty-input-{{ $unique_id }}"
                                                            value="{{$qty}}" readonly>
                                                        <button
                                                            class="btn btn-outline-secondary pd-quantity-plus-btn cart-qty-btn"
                                                            type="button"
                                                            data-unique-id="{{ $unique_id }}">
                                                            <span class="fas fa-plus"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center cart-table-subtotal-td">
                                                <p class="cart-product-price my-3 fw-bold text-dark text-price">
                                                    {!!formatPriceWithReadableFormat($subtotal)!!}
                                                </p>
                                            </td>
                                            <td class="text-center cart-table-remove-td">
                                                <div class="remove-col">
                                                    <span class="cart-product-remove-btn" data-unique-id="{{ $unique_id }}">
                                                        <span class="fas fa-trash"></span>
                                                        <span class="mobile-text">Remove</span>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="cart-destroy-col">
                                <button class="btn btn-black" type="button" id="btn_empty_cart">
                                    <i class="fas fa-trash"></i> Empty Cart
                                </button>
                            </div>
                            @else
                            <div class="text-center">
                                <p class="mb-3">No product is available in cart. Please add some products into cart.</p>
                                <a href="{{ route('shop') }}" class="btn btn-purple fw-bold"><i
                                        class="fas fa-arrow-left"></i> Go to shop</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if (count($cart) > 0)
                <div class="col-lg-4 offset-lg-8">
                    <div class="card-right-col">
                        <div class="cart-product-header">
                            <h5 class="cart-product-title text-uppercase mb-0 fw-bold">Cart Total</h5>
                        </div>
                        <div class="cart-product-checkout-col">
                            @php
                            $totals = getCartTotals();
                            @endphp

                            {{-- Coupon Section --}}
                            <div class="coupon-section mb-3">
                                @if(session()->has('coupon_code'))
                                <div class="alert alert-success p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-tag"></i>
                                            Coupon <strong>{{ session()->get('coupon_code') }}</strong> applied!
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

                            <div class="d-flex justify-content-between mt-sm-4 mt-2 mb-sm-3 mb-2">
                                <p class="mb-0">Subtotal</p>
                                <p class="mb-0 text-price text-dark">{!! formatPriceWithReadableFormat($totals['subtotal']) !!}</p>
                            </div>
                            @if($totals['discount'] > 0)
                            <div class="d-flex justify-content-between mt-sm-4 mt-2 mb-sm-3 mb-2">
                                <p class="mb-0">Discount</p>
                                <p class="mb-0 text-price text-success">-{!! formatPriceWithReadableFormat($totals['discount']) !!}</p>
                            </div>
                            @endif
                            @if($totals['tax'] > 0)
                            <div class="d-flex justify-content-between mt-sm-4 mt-2 mb-sm-3 mb-2">
                                <p class="mb-0">Tax ({{ config('constants.tax_percentage') }}%)</p>
                                <p class="mb-0 text-price text-dark">{!! formatPriceWithReadableFormat($totals['tax']) !!}</p>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mt-sm-4 mt-2 mb-sm-3 mb-2">
                                <p class="mb-0">Shipping</p>
                                <p class="mb-0 text-price text-dark">{!! formatPriceWithReadableFormat($totals['shipping']) !!}</p>
                            </div>
                            <div class="d-flex justify-content-between mt-sm-4 mt-2 mb-sm-3 mb-2">
                                <p class="mb-0">Total</p>
                                <h5 class="mb-0 text-price text-custom">
                                    {!! formatPriceWithReadableFormat($totals['total']) !!}</h5>
                            </div>
                            <div class="cart-product-additional-col">
                                <a href="{{route('checkout')}}"
                                    class="btn btn-purple d-block text-transform w-100">Proceed to checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
@section('pages-scripts')
<script @cspNonce>
    function updateQuantity(uniqueId, operation) {
        $.ajax({
            url: base_url + '/update-cart-quantity',
            type: 'POST',
            data: {
                _token: $('meta[name=csrf-token]').attr('content'),
                unique_id: uniqueId,
                operation: operation
            },
            success: function(response) {
                if (response.status == true) {
                    toastr.success(response.message);
                    window.location.reload();
                } else if (response.status == false) {
                    toastr.error(response.message);
                } else {
                    toastr.error('Something went wrong. Please try again.');
                }
            },
            error: function(error) {
                toastr.error('Something went wrong. Please try again.')
            }
        });
    }

    // Update the quantity
    $('.pd-quantity-plus-btn').on('click', function() {
        let uniqueId = $(this).data('unique-id');
        if (uniqueId) {
            updateQuantity(uniqueId, 'PLUS')
        }
    })

    $('.pd-quantity-minus-btn').on('click', function() {
        let uniqueId = $(this).data('unique-id');
        if (uniqueId) {
            updateQuantity(uniqueId, 'MINUS')
        }
    })

    // Remove an item from cart
    $(document).on('click', '.cart-product-remove-btn', function() {
        var uniqueId = $(this).data('unique-id');
        if (uniqueId) {
            Swal.fire({
                    icon: 'question',
                    title: 'Are you sure?',
                    text: 'Do you want to remove this item from cart?',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#555',
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                })
                .then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: base_url + '/remove-cart-item',
                            type: 'POST',
                            data: {
                                _token: $('meta[name=csrf-token]').attr('content'),
                                unique_id: uniqueId
                            },
                            success: function(response) {
                                if (response.status == true) {
                                    toastr.success(response.message);
                                    window.location.reload();
                                } else if (response.status == false) {
                                    toastr.error(response.message);
                                } else {
                                    toastr.error('Something went wrong. Please try again.');
                                }
                            },
                            error: function(error) {
                                toastr.error('Something went wrong. Please try again.')
                            }
                        });
                    }
                });
        }
    })

    // Empty the cart
    $(document).on('click', '#btn_empty_cart', function() {
        Swal.fire({
                icon: 'question',
                title: 'Are you sure?',
                text: 'It will remove all items from your cart.',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#555',
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
            })
            .then((result) => {
                if (result.value) {
                    $.ajax({
                        url: base_url + '/destroy-cart',
                        type: 'POST',
                        data: {
                            _token: $('meta[name=csrf-token]').attr('content'),
                        },
                        success: function(response) {
                            if (response.status == true) {
                                toastr.success(response.message);
                                window.location.reload();
                            } else if (response.status == false) {
                                toastr.error(response.message);
                            } else {
                                toastr.error('Something went wrong. Please try again.');
                            }
                        },
                        error: function(error) {
                            toastr.error('Something went wrong. Please try again.')
                        }
                    });
                }
            });
    })

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
                            if (result.isConfirmed || result.value) {
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
@endsection