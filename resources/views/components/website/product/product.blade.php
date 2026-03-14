<div class="card hp-tab-product-card product-card">

    {{-- Discount Percentage - Moved inside --}}
    {{-- Wishlist Icon - Moved inside --}}
    <div class="card-body hp-tab-product-card product-card-body">
        <div class="product-image-col">
            <a href="{{route('product-details', $product->slug)}}" class="img-protection-wrapper">
                <div class="img-protection-overlay"></div>
                <img src="{{ $product->featured_image_full_path }}"
                    alt="{{ $product->name }}"
                    class="hp-tab-product-image product-image protected-img"
                    oncontextmenu="return false;" ondragstart="return false;">
            </a>
        </div>
        <div class="hp-tab-product-info product-info">
            <div>
                <small class="text-muted d-block hp-tab-product-category product-category">
                    {{$product->category->name}}
                </small>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge rounded-pill  {{($product->stock_availability == 1)?'bg-success':'bg-danger'}}">
                        <small class="d-block hp-tab-product-category product-category">{{Config::get('constants.stock_availability')[$product->stock_availability]}}
                        </small>
                    </span>
                    @if ($product->mrp > $product->selling_price)
                    <small class="text-danger fw-bold">{{ calculateDiscountPercentage($product->mrp,$product->selling_price) }}% OFF</small>
                    @endif
                </div>
            </div>
            <a href="{{route('product-details', $product->slug)}}" class="d-block my-2">
                <h4 class="hp-tab-product-title product-title text-truncate">
                    {{ $product->name }}
                </h4>
                <div class="product-price-col">
                    <span class="product-price me-2">{{ $siteSettings->currency->symbol }}
                        {{ $product->selling_price }}</span>
                    <del class="product-price text-muted fw-normal">
                        <small>{{ $siteSettings->currency->symbol }}
                            {{ $product->mrp }}</small>
                    </del>

                </div>
            </a>

            <div class="d-flex">
                @if ($product->stock_availability == 1)
                <button class="btn btn-sm btn-purple w-100 me-1 btn_add_to_cart_card" data-id="{{$product->id}}">
                    <i class="fas fa-cart-plus"></i> Add
                </button>
                @else
                <button class="btn btn-sm btn-secondary w-100 me-1" disabled>
                    <i class="fas fa-times"></i> Out
                </button>
                @endif

                @auth
                <button class="btn btn-sm {{in_array($product->id, $userWishlistIDs) ? 'btn-danger' : 'btn-outline-danger'}} w-100 btn_add_wishlist" data-id="{{$product->id}}">
                    <i class="fas fa-heart"></i>
                </button>
                @else
                <a href="{{route('public.login')}}" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center w-100">
                    <i class="fas fa-heart"></i>
                </a>
                @endauth
            </div>
        </div>
    </div>
</div>