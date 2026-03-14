<div class="shop-filter-col">
    <div class="card shop-filter-card">
        <div class="card-body shop-filter-card-body">
            <div class="accordion filter-accordian" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button">
                            Categories
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="filter-category-col">
                                @foreach ($categories as $category)
                                <div>
                                    <div class="form-check d-flex align-items-center justify-content-between">
                                        <a href="{{route('shop', ['slug' => $category->slug])}}">
                                            <div>
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault-{{$category->id}}" @if($slug==$category->slug) checked @endif>
                                                <label class="form-check-label cursor-pointer" for="flexCheckDefault-{{$category->id}}">
                                                    {{$category->name}}
                                                </label>
                                            </div>
                                        </a>

                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button">
                            Filter By Type
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="filter-brands-col">
                                @foreach (Config::get('constants.filter_by_type') as $key => $type)
                                <div>
                                    <div class="form-check d-flex align-items-center justify-content-between">
                                        <div onclick="window.location.href='{{route('shop', ['type' => customUrlEncode($key)])}}'">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="flexCheckDefault-{{$key}}" @if($key==customUrlDecode(request()->get('type'))) checked @endif>
                                            <label class="form-check-label cursor-pointer" for="flexCheckDefault-{{$key}}">
                                                {{$type}}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button">
                            Filter By Price
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="filter-range">
                                <div id="price-filter"></div>
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <span>
                                        <i class="fas fa-indian-rupee-sign"></i>
                                        <span id="lower-value"></span>
                                        <input type="hidden" id="lower-price" value="{{$price['min_price']}}">
                </span>
                <span>
                    <i class="fas fa-indian-rupee-sign"></i>
                    <span id="upper-value"></span>
                    <input type="hidden" id="upper-price" value="{{$price['max_price']}}">
                </span>
            </div>
        </div>
    </div>
</div>
</div> --}}

</div>
</div>
</div>
</div>