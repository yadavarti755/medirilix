@foreach($subcategories as $subcategory)
    @if($subcategory->subcategories && count($subcategory->subcategories) > 0)
        <li>
            <a href="javascript:void(0)" class="mobile-nav-link has-submenu d-flex justify-content-between align-items-center">
                {{ $subcategory->name }} <i class="fas fa-chevron-down arrow-icon"></i>
            </a>
            <ul class="mobile-submenu ms-3" style="display: none; padding-left: 10px;">
                @include('includes.website.category-dropdown-mobile', ['subcategories' => $subcategory->subcategories])
            </ul>
        </li>
    @else
        <li><a class="mobile-nav-link" href="{{ route('shop', ['slug' => $subcategory->slug]) }}">{{ $subcategory->name }}</a></li>
    @endif
@endforeach
