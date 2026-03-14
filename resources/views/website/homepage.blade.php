@extends('layouts.website_layout')

@section('content')

{{-- Banner --}}
<section class="hp-slider-section">
    <div id="carouselExampleCaptions" class="carousel slide carousel-fade" data-bs-ride="carousel">

        <ol class="carousel-indicators">
            @if($sliders)
            @foreach($sliders as $slider)
            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="{{ $loop->index }}"
                {{ $loop->iteration == 1 ? 'class=active' : '' }}>
            </li>
            @endforeach
            @endif
        </ol>

        <div class="carousel-inner">
            @if($sliders)
            @foreach($sliders as $slider)
            <div class="carousel-item {{ $loop->iteration == 1 ? 'active' : '' }}">

                <a href="{{ route('shop', ['slug' => $slider->category?->slug ?? '' ]) }}">
                    <img src="{{ $slider->file_url }}" class="d-block w-100"
                        alt="{{ $slider->title ?? 'Slider Image' }}">
                </a>

                {{-- Caption Section --}}
                <div class="carousel-caption text-center">
                    <div class="carousel-caption-content-col">
                        @if($slider->title)
                        <h5 class="fw-bold carousel-caption-title">{{ $slider->title }}</h5>
                        @endif
                        @if($slider->subtitle)
                        <h6 class="mb-2 carousel-caption-subtitle">{{ $slider->subtitle }}</h6>
                        @endif
                        @if($slider->description)
                        <p class="carousel-caption-description mb-0">{{ $slider->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
        <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
        </a>

    </div>
</section>


{{-- Recent Product Section --}}
<section class="section" style="padding-bottom: 15px;">
    <div class="container">
        <div class="section-header text-center">
            <h4 class="section-title">
                <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span> Recent Products <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span>
            </h4>
            <a href="{{route('shop', ['type' => customUrlEncode(Config::get('constants.filter_by_type_text')[4])])}}"
                class="btn btn-custom">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="main_slider">

            @foreach ($recentProducts as $product)
            <div class="product-col">
                <x-website.product.product :product="$product" :userWishlistIDs="$userWishlistIDs" />
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- Sale Products Section --}}
@if($saleProducts->count() > 0)
<section class="section" style="padding-bottom: 15px;">
    <div class="container">
        <div class="section-header text-center">
            <h4 class="section-title">
                <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span> Products In <span class="text-danger ms-1">Sale</span> <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span>
            </h4>
            <a href="{{route('shop', ['type' => customUrlEncode(Config::get('constants.filter_by_type_text')[4])])}}"
                class="btn btn-custom">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="main_slider">

            @foreach ($saleProducts as $product)
            <div class="product-col">
                <x-website.product.product :product="$product" :userWishlistIDs="$userWishlistIDs" />
            </div>
            @endforeach

        </div>
    </div>
</section>
@endif

{{-- Offer Section --}}
@if($offers && count($offers) > 0)
<section class="section offer-section" style="padding-bottom: 15px;">
    <div class="container">
        <div class="row">
            @foreach($offers as $offer)
            <div class="col-md-6 mb-3">
                @php
                $link = '#';
                if ($offer->type == 'product' && $offer->product) {
                $link = route('product-details', $offer->product->slug);
                } elseif ($offer->type == 'category' && $offer->category) {
                $link = route('shop', $offer->category->slug);
                }
                @endphp

                <a href="{{ $link }}">
                    <div class="dis-offer-card">
                        <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" class="w-100 img-fluid rounded">
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Category Section --}}
<section class="hp-product-section section custom-section-padding">
    <div class="container">
        <div class="hp-tab-product-header section-header text-center">
            <h4 class="hp-tab-product-title section-title">
                <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span>
                Shop By Categories
                <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span>
            </h4>
        </div>
        <div class="category_slider">
            @foreach ($categories as $category)
            <div class="category-col">
                <a href="{{route('shop', $category->slug)}}">
                    <div class="card category-card">
                        <div class="card-body category-card-body">
                            <div class="category-image-col">
                                <img src="{{ $category->image_path }}"
                                    alt="{{ $category->name }}" class="category-image">
                            </div>
                            <div class="category-info">
                                <h4 class="category-title text-truncate">
                                    {{ $category->name }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Trending Products Section --}}
@if($trendingProducts->count() > 0)
<section class="section custom-section-padding">
    <div class="container">
        <div class="section-header text-center">
            <h4 class="section-title">
                <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span> Trending <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span>
            </h4>
            <a href="{{route('shop', ['type' => customUrlEncode(Config::get('constants.filter_by_type_text')[1])])}}"
                class="btn btn-custom">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="main_slider">

            @foreach ($trendingProducts as $product)
            <div class="product-col">
                <x-website.product.product :product="$product" :userWishlistIDs="$userWishlistIDs" />
            </div>
            @endforeach

        </div>
    </div>
</section>
@endif

{{-- shipping Section --}}
<section class="hp-shipping-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-sm-6 col-12 hp-shipping-col">
                <div class="card hp-shipping-card">
                    <div class="card-body hp-shipping-card-body">
                        <div class="hp-shipping-icon-col">
                            <span class="fas fa-shipping-fast"></span>
                        </div>
                        <div class="hp-shipping-content">
                            <h4 class="hp-shipping-title">Fast Delivery</h4>
                            <p class="mb-0 hp-shipping-subtitle">Within 4-7 business days</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12 hp-shipping-col">
                <div class="card hp-shipping-card">
                    <div class="card-body hp-shipping-card-body">
                        <div class="hp-shipping-icon-col">
                            <span class="fas fa-money-bill-alt"></span>
                        </div>
                        <div class="hp-shipping-content">
                            <h4 class="hp-shipping-title">Return & Refund</h4>
                            <p class="mb-0 hp-shipping-subtitle">30 days return policy</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12 hp-shipping-col">
                <div class="card hp-shipping-card">
                    <div class="card-body hp-shipping-card-body">
                        <div class="hp-shipping-icon-col">
                            <span class="far fa-lock"></span>
                        </div>
                        <div class="hp-shipping-content">
                            <h4 class="hp-shipping-title">Safe Shopping</h4>
                            <p class="mb-0 hp-shipping-subtitle">
                                100% secure payment
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Subscribe Section --}}
<section class="hp-subscribe-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="hp-subscribe-col">
                    <div class="hp-subscribe-title-col">
                        <h3 class="hp-subscribe-title">
                            Join Our Newsletter
                        </h3>
                        <p class="hp-subscribe-subtitle">
                            Get the latest offers in your mail id
                        </p>
                    </div>

                    <div class="hp-subscribe-form-col">
                        <div class="hp-subscribe-form-wrapper">
                            <form action="" id="subscribe_newsletter_form" method="post">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="newsletter_email_id"
                                        class="form-control hp-subscribe-input" placeholder="Enter your email..."
                                        aria-label="Enter your email..." aria-describedby="button-addon2">
                                    <button class="btn btn-outline-secondary hp-subscribe-btn" type="submit"
                                        id="button-addon2">Subscribe</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection