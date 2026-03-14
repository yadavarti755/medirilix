@extends('layouts.website_layout')

@section('content')

@include('components.website.page-header')

<section class="shop-section">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-12">
                <div class="filter-wrapper-col">
                    <div class="filter-btn-col">
                        <button class="btn btn-purple" type="button" id="btn-filter">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                    <div class="filter-col">
                        <button class="btn btn-black" id="btn-filter-close">
                            <i class="fas fa-times"></i>
                        </button>

                        <!-- Filter Component -->
                        <div class="accordion" id="shopFilters">
                            <!-- Categories -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#filterCategory">
                                        Categories
                                    </button>
                                </h2>
                                <div id="filterCategory" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        @foreach($categories as $category)
                                        <div class="form-check">
                                            <input class="form-check-input filter-checkbox" type="checkbox" name="categories[]"
                                                value="{{$category->slug}}" id="cat_{{$category->id}}"
                                                {{ $slug == $category->slug || (request('category_slug') == $category->slug) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cat_{{$category->id}}">
                                                {{$category->name}} <small class="text-muted">({{$category->products_count}})</small>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Brands -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filterBrand">
                                        Brands
                                    </button>
                                </h2>
                                <div id="filterBrand" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        @foreach($brands as $brand)
                                        <div class="form-check">
                                            <input class="form-check-input filter-checkbox" type="checkbox" name="brands[]"
                                                value="{{$brand->id}}" id="brand_{{$brand->id}}">
                                            <label class="form-check-label" for="brand_{{$brand->id}}">
                                                {{$brand->name}}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Type -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filterType">
                                        Product Type
                                    </button>
                                </h2>
                                <div id="filterType" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        @foreach($types as $name => $code)
                                        <div class="form-check">
                                            <input class="form-check-input filter-checkbox" type="checkbox" name="types[]"
                                                value="{{$name}}" id="type_{{$code}}"
                                                @if(request()->get('type') && \customUrlDecode(request()->get('type')) == $name) checked @endif>
                                            <label class="form-check-label" for="type_{{$code}}">
                                                {{ucfirst(strtolower(str_replace('_', ' ', $name)))}}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filterPrice">
                                        Price Range
                                    </button>
                                </h2>
                                <div id="filterPrice" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <div id="price-slider" class="mb-3"></div>
                                        <div class="d-flex justify-content-between text-muted small">
                                            <span id="price-min-display"></span>
                                            <span id="price-max-display"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-12">

                <div class="shop-sorting-col d-flex justify-content-between align-items-center mb-4">
                    <p class="mb-0" id="result-count">
                        Showing {{$all_products->count()}} from {{$all_products->total()}} results
                    </p>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('shop') }}" class="btn btn-sm">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                        <span class="mx-2">
                            |
                        </span>
                        <label class="me-2"><small>Sort by:</small></label>
                        <select name="sorting_filter" id="sorting_filter" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Default Sorting</option>
                            @foreach(Config::get('constants.sort_by_filter') as $key => $filter)
                            <option value="{{$key}}" @if(request('sort') && base64_decode(urldecode(request('sort')))==$key) selected @endif>
                                {{$filter}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="shop-product-wrapper position-relative">
                    <!-- Skeleton Loader -->
                    <div id="product-skeleton" class="d-none">
                        @include('website.partials.shop-skeleton')
                    </div>

                    <!-- Product Grid -->
                    <div id="product-grid">
                        @include('website.partials.shop-product-list')
                    </div>
                    <!-- Overlay Loader -->
                    <div id="product-overlay" class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-50 d-none justify-content-center align-items-center" style="z-index: 10;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('pages-scripts')
<script @cspNonce>
    let debounceTimer;
    let initialLoad = true;

    // State management
    const state = {
        categories: [],
        brands: [],
        types: [],
        min_price: parseInt("{{ $price->min_price }}"),
        max_price: parseInt("{{ $price->max_price }}"),
        sort: null,
        page: 1,
        search: "{{ request('q') ?? request('search') }}",
        category_slug: "{{ $slug ?? '' }}" // Capture initial slug from route
    };

    $(document).ready(function() {

        // --- Initialize Slider ---
        const slider = document.getElementById('price-slider');
        const minPrice = parseInt("{{ $price->min_price }}");
        const maxPrice = parseInt("{{ $price->max_price }}");

        if (slider) {
            noUiSlider.create(slider, {
                start: [state.min_price, state.max_price],
                connect: true,
                range: {
                    'min': minPrice,
                    'max': maxPrice
                },
                step: 1,
                tooltips: false, // Set to true if you want tooltips on handles
            });

            // Update display values on slide
            slider.noUiSlider.on('update', function(values, handle) {
                $('#price-min-display').text(Math.round(values[0]));
                $('#price-max-display').text(Math.round(values[1]));
            });

            // Trigger fetch on change (end of slide)
            slider.noUiSlider.on('change', function(values, handle) {
                state.min_price = Math.round(values[0]);
                state.max_price = Math.round(values[1]);
                state.page = 1; // Reset to page 1 on filter change
                fetchProducts();
            });
        }

        // --- Event Listeners ---

        // Checkboxes (Category, Brand, Type)
        $('.filter-checkbox').on('change', function() {
            updateStateFromDOM();
            state.page = 1;
            fetchProducts();
        });

        // Sorting
        $('#sorting_filter').on('change', function() {
            state.sort = $(this).val() ? btoa($(this).val()) : null;
            state.page = 1;
            fetchProducts();
        });

        // Pagination (Delegate event for dynamic links)
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            let href = $(this).attr('href');
            if (href) {
                let page = href.split('page=')[1];
                state.page = page;
                fetchProducts();
                // Scroll to top of product grid
                $('html, body').animate({
                    scrollTop: $(".shop-product-wrapper").offset().top - 150
                }, 500);
            }
        });

        // Reset Filters
        window.resetFilters = function() {
            $('.filter-checkbox').prop('checked', false);
            $('#sorting_filter').val('');

            // Reset Slider
            if (slider) {
                slider.noUiSlider.set([minPrice, maxPrice]);
            }
            state.min_price = minPrice;
            state.max_price = maxPrice;
            state.page = 1;

            updateStateFromDOM();
            fetchProducts();
        }

        // Initialize state (mostly for checked boxes if page reloaded)
        updateStateFromDOM();
    });

    function updateStateFromDOM() {
        state.categories = $('.filter-checkbox[name="categories[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        state.brands = $('.filter-checkbox[name="brands[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        state.types = $('.filter-checkbox[name="types[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        // Slider values are updated via its event listener directly, but we can double check if needed
        if (!state.sort) {
            let val = $('#sorting_filter').val();
            state.sort = val ? btoa(val) : null;
        }
    }

    function fetchProducts() {
        // Show Loader
        $('#product-grid').addClass('opacity-25');
        $('#product-overlay').removeClass('d-none').addClass('d-flex');

        // Prepare Query Params
        let params = {
            categories: state.categories,
            brands: state.brands,
            types: state.types,
            min_price: state.min_price,
            max_price: state.max_price,
            sort: state.sort,
            page: state.page,
            category_slug: state.category_slug
        };

        // Construct URL for pushState
        let url = new URL(window.location.href);
        url.search = ''; // clear existing params

        if (state.sort) url.searchParams.set('sort', state.sort);
        // if(state.page > 1) url.searchParams.set('page', state.page);
        // We can add more params to URL if we want shareable links for all filters

        // window.history.pushState({}, '', url);


        $.ajax({
            url: "{{ route('shop') }}", // Use route helper
            type: "GET",
            data: params,
            success: function(response) {
                if (response.status) {
                    $('#product-grid').html(response.html);
                    $('#result-count').text(`Showing ${response.count} from ${response.total} results`);
                }
            },
            complete: function() {
                $('#product-grid').removeClass('opacity-25');
                $('#product-overlay').addClass('d-none').removeClass('d-flex');
            },
            error: function() {
                console.error("Failed to fetch products");
                $('#product-grid').removeClass('opacity-25');
                $('#product-overlay').addClass('d-none').removeClass('d-flex');
            }
        });
    }
</script>
@endsection