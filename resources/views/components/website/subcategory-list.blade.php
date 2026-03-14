@props(['subcategories'])

@foreach($subcategories as $subcategory)
<div class="subcategory-item">
    <div class="subcategory-link" data-category-id="{{ $subcategory->id }}">
        <a href="{{ route('shop', ['slug' => $subcategory->slug]) }}" style="flex: 1; color: inherit; text-decoration: none;">
            {{ $subcategory->name }}
        </a>
        @if($subcategory->subcategories && count($subcategory->subcategories) > 0)
        <button class="subcategory-toggle" data-target="sub-{{ $subcategory->id }}">
            <i class="fas fa-chevron-right"></i>
        </button>
        @endif
    </div>

    @if($subcategory->subcategories && count($subcategory->subcategories) > 0)
    <div class="subcategory-list" id="sub-{{ $subcategory->id }}">
        <x-subcategory-list :subcategories="$subcategory->subcategories" />
    </div>
    @endif
</div>
@endforeach