@props(['categories'])

<li class="nav-item mega-menu-wrapper position-unset">
    <a class="nav-link text-light" href="{{ route('shop') }}">
        Our Categories
    </a>

    <div class="mega-menu">
        <div class="mega-menu-columns">
            @php
            $columnCount = 3;
            $topCategories = $categories->where('parent_id', null);
            $chunkedCategories = $topCategories->chunk(ceil($topCategories->count() / $columnCount));
            @endphp

            @foreach($chunkedCategories as $columnCategories)
            <div class="mega-menu-column">
                @foreach($columnCategories as $category)
                <div class="category-item">
                    <div class="category-link" data-category-id="{{ $category->id }}">
                        <a href="{{ route('shop', ['slug' => $category->slug]) }}" style="flex: 1; color: inherit; text-decoration: none;">
                            {{ $category->name }}
                        </a>
                        @if($category->subcategories && count($category->subcategories) > 0)
                        <button class="subcategory-toggle" data-target="sub-{{ $category->id }}">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        @endif
                    </div>

                    @if($category->subcategories && count($category->subcategories) > 0)
                    <div class="subcategory-list" id="sub-{{ $category->id }}">
                        <x-website.subcategory-list :subcategories="$category->subcategories" />
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</li>