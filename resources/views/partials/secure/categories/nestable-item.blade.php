<li class="dd-item" data-id="{{ $category->id }}">
    <div class="dd-handle">{{ $category->name }}</div>
    @if ($category->children->isNotEmpty())
    <ol class="dd-list">
        @foreach ($category->children as $child)
        @include('partials.secure.categories.nestable-item', ['category' => $child])
        @endforeach
    </ol>
    @endif
</li>