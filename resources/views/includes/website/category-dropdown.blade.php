@foreach($subcategories as $subcategory)
    @if($subcategory->subcategories && count($subcategory->subcategories) > 0)
        <li class="dropdown-submenu">
            <a class="dropdown-item dropdown-toggle" href="{{ route('shop', ['slug' => $subcategory->slug]) }}">{{ $subcategory->name }}</a>
            <ul class="dropdown-menu">
                @include('includes.website.category-dropdown', ['subcategories' => $subcategory->subcategories])
            </ul>
        </li>
    @else
        <li><a class="dropdown-item" href="{{ route('shop', ['slug' => $subcategory->slug]) }}">{{ $subcategory->name }}</a></li>
    @endif
@endforeach
