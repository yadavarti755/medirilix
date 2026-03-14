<div class="row">
    @if (count($all_products) > 0)
    @foreach ($all_products as $product)
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6 product-col">
        <x-website.product.product :product="$product" :userWishlistIDs="$userWishlistIDs ?? []" />
    </div>
    @endforeach
    <div class="pagination-wrapper d-flex align-items-center justify-content-center">
        {{ $all_products->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div class="col-12 text-center py-5">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No products found matching your criteria.</h5>
        <a href="{{ route('shop') }}" class="btn btn-outline-custom mt-3">Reset Filters</a>
    </div>
    @endif
</div>