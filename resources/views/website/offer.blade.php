@extends('layouts.website_layout')

@section('content')

@include('components.website.page-header')

<section class="shop-section">
    <div class="container">

        <div class="row">

            <div class="col-lg-12 col-12">

                <div class="shop-sorting-col">
                    <p class="mb-0">
                        Showing {{$all_products->count()}} from {{$all_products->total()}} results
                    </p>
                    <select name="sorting_filter" id="sorting_filter" class="custom_select">
                        <option value="">Default Sorting</option>
                        @foreach(Config::get('constants.sort_by_filter') as $key => $filter)
                        <option value="{{$key}}" @if($sort_filter && $sort_filter==$key) {{'selected'}} @endif>
                            {{$filter}}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="shop-product-wrapper">
                    <div class="row">
                        @if (count($all_products) > 0)
                        @foreach ($all_products as $product)

                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-4 col-6 product-col">
                            <x-website.product.product :product="$product" :userWishlistIDs="$userWishlistIDs" />
                        </div>

                        @endforeach
                        <div class="pagination-wrapper d-flex align-items-center justify-content-center">
                            {{ $all_products->onEachSide(1)->appends($_GET)->links() }}
                        </div>
                        @else
                        <p class="mb-0 text-center"><i class="fas fa-times"></i> No products are available right now. We
                            are sorry for your inconvenience</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $('#sorting_filter').on('change', function() {
        var filterValue = $(this).val();
        var url = new URL(window.location.href);

        if (filterValue) {
            filterValue = encodeURI(btoa(filterValue));
            url.searchParams.set('sort', filterValue);
            window.location.href = url.href;
        } else {
            url.searchParams.delete('sort');
            window.location.href = url.href;
        }
    })
</script>
@endsection