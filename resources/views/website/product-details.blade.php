@extends('layouts.website_layout')

@section('content')
<section class="product-details-section py-5">
    <div class="container">

        <div class="row">
            <div class="col-lg-5">
                <div class="pd-image-col">
                    <a href="{{ $product->featured_image_full_path }}" data-fancybox="gallery"
                        class="fancybox">
                        <img src="{{ $product->featured_image_full_path }}"
                            xoriginal="{{ $product->featured_image_full_path }}"
                            alt="{{$product->name}}" class="w-100 xzoom protected-img" id="product-main-image"
                            oncontextmenu="return false;" ondragstart="return false;">
                    </a>
                </div>
                <div class="mt-3">
                    @if ($productAllImages->count() > 0)
                    @foreach ($productAllImages as $image)
                    <a href="{{ $image->image_full_path }}"
                        data-fancybox="gallery" class="fancybox img-protection-wrapper">
                        <div class="img-protection-overlay"></div>
                        <img class="xzoom-gallery img-thumbnail protected-img" width="80"
                            src="{{ $image->image_full_path }}"
                            xpreview="{{ $image->image_full_path }}"
                            oncontextmenu="return false;" ondragstart="return false;">
                    </a>
                    @endforeach
                    <a href="{{ $product->featured_image_full_path }}" data-fancybox="gallery"
                        class="fancybox img-protection-wrapper">
                        <div class="img-protection-overlay"></div>
                        <img class="xzoom-gallery img-thumbnail protected-img" width="80"
                            src="{{ $product->featured_image_full_path }}"
                            xpreview="{{ $product->featured_image_full_path }}"
                            oncontextmenu="return false;" ondragstart="return false;">
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                <h4 class="fw-bold pd-title">{{$product->name}}</h4>
                <p class="pd-availability my-2 fw-bold">
                    <small>
                        @if ($product->stock_availability == 1)
                        <span class="badge bg-success rounded-pill">
                            In Stock
                        </span>

                        @else
                        <span class="badge bg-danger rounded-pill">
                            Out of Stock
                        </span>
                        @endif
                    </small>
                </p>
                <div class="d-flex align-items-center">
                    <p class="fw-bold pd-price mb-1 me-3">{{ $siteSettings->currency->symbol }}
                        {{$product->selling_price }}
                    </p>
                    <p class="pd-mrp my-1 text-muted"> <del>{{ $siteSettings->currency->symbol }} {{ $product->mrp
                            }}</del></p>

                    @if ($product->mrp > $product->selling_price)
                    <div class="ms-2">
                        <p class="text-custom mb-0">{{
                            calculateDiscountPercentage($product->mrp,$product->selling_price) }}% OFF</p>
                    </div>
                    @endif
                </div>

                <div class="my-3">
                    <nav>
                        <div class="nav nav-tabs product-nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Product Description</button>
                        </div>
                    </nav>

                    <div class="tab-content pt-3" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                            <div class="pd-short-desc">
                                {!!$product->description!!}
                            </div>

                            <div class="mt-4">
                                <h5 class="fw-bold mb-3">Specifications</h5>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        @if($product->productType)
                                        <tr>
                                            <th width="30%">Product Type</th>
                                            <td>{{ $product->productType->name }}</td>
                                        </tr>
                                        @endif
                                        @if($product->upc)
                                        <tr>
                                            <th width="30%">UPC</th>
                                            <td>{{ $product->upc }}</td>
                                        </tr>
                                        @endif
                                        @if($product->mpn)
                                        <tr>
                                            <th width="30%">MPN</th>
                                            <td>{{ $product->mpn }}</td>
                                        </tr>
                                        @endif
                                        @if($product->model)
                                        <tr>
                                            <th width="30%">Model</th>
                                            <td>{{ $product->model }}</td>
                                        </tr>
                                        @endif
                                        @if($product->expiration_date)
                                        <tr>
                                            <th width="30%">Expiration Date</th>
                                            <td>{{ $product->expiration_date }}</td>
                                        </tr>
                                        @endif
                                        @if($product->material)
                                        <tr>
                                            <th width="30%">Material</th>
                                            <td>{{ $product->material->name }}</td>
                                        </tr>
                                        @endif
                                        @if($product->intendedUse)
                                        <tr>
                                            <th width="30%">Intended Use</th>
                                            <td>{{ $product->intendedUse->name }}</td>
                                        </tr>
                                        @endif
                                        @if($product->country)
                                        <tr>
                                            <th width="30%">Country Of Origin</th>
                                            <td>{{ $product->country->name }}</td>
                                        </tr>
                                        @endif
                                        @if($product->unit_quantity)
                                        <tr>
                                            <th width="30%">Unit Quantity</th>
                                            <td>{{ $product->unit_quantity }}</td>
                                        </tr>
                                        @endif
                                        @if($product->unitType)
                                        <tr>
                                            <th width="30%">Unit Type</th>
                                            <td>{{ $product->unitType->name }}</td>
                                        </tr>
                                        @endif

                                        @if($product->otherSpecifications && count($product->otherSpecifications) > 0)
                                        @foreach($product->otherSpecifications as $spec)
                                        <tr>
                                            <th width="30%">{{ $spec->label }}</th>
                                            <td>{{ $spec->value }}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="sharethis-inline-share-buttons"></div>
                </div>

                <form action="" id="form_add_to_cart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="product-options-col d-flex align-items-baseline">
                        <!-- Size Selection Removed -->
                        <div class="pd-quantity-col">
                            <small class="d-block pd-spec-header fw-bold">Quantity <span
                                    class="text-danger">*</span></small>
                            <div class="input-group mb-3 pd-quantity-input">
                                <button class="btn btn-outline-secondary pd-quantity-minus-btn" type="button"
                                    id="minus_qty">
                                    <span class="fas fa-minus"></span>
                                </button>
                                <input type="text" class="text-center" id="quantity" name="quantity" placeholder=""
                                    value="1" style="width: 70px !important;">
                                <button class="btn btn-outline-secondary pd-quantity-plus-btn" type="button"
                                    id="plus_qty">
                                    <span class="fas fa-plus"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pd-btn-col mt-2">
                        @if ($product->stock_availability == 1)
                        <button type="button" class="btn btn-warning pd-cart-btn mb-1 me-2" id="btn_buy_now"><i
                                class="fas fa-bolt"></i> Buy Now</button>
                        <button type="submit" class="btn btn-purple pd-cart-btn mb-1 btn-addtocart"><i
                                class="fas fa-cart-plus"></i> Add to cart</button>

                        @else
                        <button type="button" class="btn btn-warning pd-cart-btn mb-1" disabled><i
                                class="fas fa-times"></i> Out of stock</button>
                        @endif

                        @if (Auth::check())
                        <button type="button" class="btn btn-black pd-cart-btn text-light btn_add_wishlist mb-1"
                            data-id="{{$product->id}}"
                            id="{{$product->id}}"><span class="fas fa-heart"></span> Add to
                            wishlist</button>
                        @else
                        <a href="{{route('public.login')}}" class="btn btn-black mb-1"><span class="fas fa-heart"></span> Login to add in wishlist</a>
                        @endif
                    </div>

                    @if($product->return_till_days || $product->return_description)
                    <div class="accordion mt-4" id="returnPolicyAccordion">
                        <div class="accordion-item border rounded">
                            <h2 class="accordion-header" id="returnPolicyHeading">
                                <button class="accordion-button collapsed fw-bold" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#returnPolicyCollapse"
                                    aria-expanded="false"
                                    aria-controls="returnPolicyCollapse">
                                    <div>
                                        <i class="fas fa-undo-alt me-1"></i>
                                        Return Policy
                                        <br>
                                        @if($product->return_till_days)
                                        <span class="ms-4 text-danger fw-normal">
                                            Return Period: {{ $product->return_till_days }} Days
                                        </span>
                                        @endif
                                    </div>
                                </button>
                            </h2>

                            <div id="returnPolicyCollapse"
                                class="accordion-collapse collapse"
                                aria-labelledby="returnPolicyHeading"
                                data-bs-parent="#returnPolicyAccordion">
                                <div class="accordion-body text-muted small">
                                    @if($product->return_description)
                                    {!! $product->return_description !!}
                                    @else
                                    <p class="mb-0">No return description available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </form>

            </div>
        </div>

    </div>
</section>


<section class="section py-4 bg-white">
    <div class="container">
        <div class="bg-light p-4 rounded mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Customer Reviews</h4>
                @if($canReview)
                <button class="btn btn-purple" id="btn_write_review">Write a Review</button>
                @endif
            </div>

            @if($reviews->count() > 0)
            <div class="review-list">
                @foreach($reviews as $review)
                <div class="review-item border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold">{{ $review->user->name }}</h6>
                        <div class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-muted' }}"></i>
                                @endfor
                        </div>
                    </div>
                    <p class="text-muted small mb-1">{{ $review->created_at->format('d M, Y') }}</p>
                    <p>{{ $review->message }}</p>
                    @if($review->images->count() > 0)
                    <div class="review-images mt-2">
                        @foreach($review->images as $image)
                        <a href="{{ asset('storage/review_images/' . $image->image_path) }}" data-fancybox="review-gallery-{{ $review->id }}">
                            <img src="{{ asset('storage/review_images/' . $image->image_path) }}" class="img-thumbnail" style="height: 60px;">
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-muted">No reviews yet. Be the first to review this product!</p>
            @endif
        </div>
    </div>
</section>

@if(count($related_products) > 0)
<section class="section py-4">
    <div class="container">
        <div class="section-header text-center">
            <h4 class="section-title">
                <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span>
                Related Products
                <span class="section-title-icon">
                    <i class="fa-regular fa-gem"></i>
                </span>
            </h4>
        </div>

        <div class="related-products-wrapper">
            <div class="row">
                @foreach ($related_products as $product)
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6 hp-best-col product-col">
                    <x-website.product.product :product="$product" :userWishlistIDs="$userWishlistIDs" />
                </div>
                @endforeach

            </div>
        </div>
    </div>
</section>
@endif

@if($canReview)
<!-- Review Modal -->
<div class="modal fade" id="write_review_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.review.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-control" required>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Review</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photos (Max 5)</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-purple">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('pages-scripts')
<script @cspNonce>
    $('#btn_write_review').on('click', function() {
        $('#write_review_modal').modal('show');
    })

    // Buy Now Button
    $('#btn_buy_now').on('click', function(event) {
        event.preventDefault();
        var formData = new FormData(document.getElementById('form_add_to_cart'));
        // Send Ajax Request
        $.ajax({
            url: base_url + '/add-to-cart',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status == true) {
                    window.location.href = base_url + '/checkout';
                } else if (response.status == 'validation_error') {
                    var errorString = '<ul class="list-unstyled">';
                    $.each(response.message, function(key, value) {
                        errorString += '<li>' + value + '</li>';
                    });
                    errorString += '</ul>';

                    $.dialog({
                        title: 'Validation Error',
                        type: 'red',
                        content: errorString,
                        backgroundDismiss: true,
                        buttons: {
                            confirm: function() {
                                window.location.reload();
                            }
                        }
                    });
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
    })

    // On submitting the add to cart form
    $('#form_add_to_cart').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(document.getElementById('form_add_to_cart'));
        // Send Ajax Request
        $.ajax({
            url: base_url + '/add-to-cart',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status == true) {
                    $.confirm({
                        title: 'Product Added',
                        type: 'green',
                        content: response.message,
                        buttons: {
                            shop: {
                                text: 'Shop More',
                                btnClass: 'btn-black',
                                action: function() {
                                    window.location.reload();
                                }
                            },
                            cart: {
                                text: 'Go to cart',
                                btnClass: 'btn-purple',
                                keys: ['enter'],
                                action: function() {
                                    window.location.href = base_url + '/cart';
                                }
                            }
                        }
                    });
                } else if (response.status == 'validation_error') {
                    var errorString = '<ul class="list-unstyled">';
                    $.each(response.message, function(key, value) {
                        errorString += '<li>' + value + '</li>';
                    });
                    errorString += '</ul>';

                    $.dialog({
                        title: 'Validation Error',
                        type: 'red',
                        content: errorString,
                        backgroundDismiss: true,
                        buttons: {
                            confirm: function() {
                                window.location.reload();
                            }
                        }
                    });
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
    })


    // Quantity
    $('#minus_qty').on('click', function() {
        var quantity = $('#quantity').val();
        if (quantity > 1) {
            $('#quantity').val(quantity - 1);
        }
    })
    $('#plus_qty').on('click', function() {
        var quantity = parseInt($('#quantity').val());
        $('#quantity').val(quantity + 1);
    })
</script>
@endsection