@extends('layouts.website_layout')

@section('content')
@include('components.website.page-header')
<section class="cart-section">
    <div class="container">

        <div class="checkout-list-col">
            <div class="row">
                <div class="col-lg-8 px-lg-3 px-1">
                    <div class="cart-product-header">
                        <h5 class="cart-product-title text-uppercase mb-0 fw-bold"><small>Payment Options</small></h5>
                    </div>

                    <div class="checkout-sa-col px-lg-4 px-1 py-lg-4 py-3">
                        <form action="" class="checkout-sa-form" id="checkout-sa-form">
                            <div class="row">
                                <div class=" col-lg-12 col-sm-6 col-12 mb-3">
                                    <label for="sa-payment-option" class="mb-2">Select Payment Option</label>
                                    <select name="sa-payment-option" id="sa-payment-option" class="form-control rounded-0">
                                        <option value="">Select Option</option>
                                        <option value="OFFLINE">Cash On Delivery</option>
                                        <option value="ONLINE">Online</option>
                                    </select>
                                </div>
                                <div class="col-12 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('checkout') }}" class="text-purple fw-bold"><i class="fas fa-arrow-left"></i> Go back to checkout</a>
                                    <button type="button" class="btn btn-purple" id="sa-submit-btn">Proceed <i class="fas fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">

                    <div class="checkout-list-wrapper">
                        <div class="checkout-col mb-3">
                            <div class="row align-items-center">
                                <div class="col-lg-3">
                                    <div class="checkout-product-image-col">
                                        <img src="{{ asset('assets/images/products/2.png') }}" alt="Image" class="checkout-img">
                                        <span class="checkout-product-count">2</span>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="checkout-product-info-wrapper">
                                        <div class="checkout-product-info">
                                            <strong class="checkout-title d-block"><small>Cosmo De Misclene</small></strong>
                                            <small class="d-block checkout-spec text-muted">
                                                <span>S</span> / <span>Material 1</span>
                                            </small>
                                        </div>
                                        <div class="checkout-product-price">
                                            <h6 class="checkout-price my-3 fw-bold text-purple">{{ $siteSettings->currency->symbol }} 999.00</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Cart Product Col --}}

                        <div class="checkout-col mb-3">
                            <div class="row align-items-center">
                                <div class="col-lg-3">
                                    <div class="checkout-product-image-col">
                                        <img src="{{ asset('assets/images/products/2.png') }}" alt="Image" class="checkout-img">
                                        <span class="checkout-product-count">2</span>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="checkout-product-info-wrapper">
                                        <div class="checkout-product-info">
                                            <strong class="checkout-title d-block"><small>Cosmo De Misclene</small></strong>
                                            <small class="d-block checkout-spec text-muted">
                                                <span>S</span> / <span>Material 1</span>
                                            </small>
                                        </div>
                                        <div class="checkout-product-price">
                                            <h6 class="checkout-price my-3 fw-bold text-purple">{{ $siteSettings->currency->symbol }} 999.00</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Cart Product Col --}}

                        <div class="checkout-subtotal-col">
                            <h6 class="d-flex justify-content-between mb-3">
                                <small>Subtotal</small>
                                <strong id="subtotal-price">{{ $siteSettings->currency->symbol }} 1230.00</strong>
                            </h6>
                            <h6 class="d-flex justify-content-between">
                                <small>Shipping</small>
                                <strong id="subtotal-price">{{ $siteSettings->currency->symbol }} 0.00</strong>
                            </h6>
                        </div>
                        <div class="checkout-total-col mt-3">
                            <h6 class="d-flex justify-content-between mb-3">
                                <small>Total</small>
                                <strong id="subtotal-price" class="fs-4">{{ $siteSettings->currency->symbol }} 1230.00</strong>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection