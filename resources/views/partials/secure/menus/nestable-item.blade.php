<li class="dd-item" data-id="{{ $menu->id }}">
    <div class="dd-handle">{{ $menu->title }} ({{ $menu->title_hi }})</div>
    @if ($menu->children->isNotEmpty())
    <ol class="dd-list">
        @foreach ($menu->children as $child)
        @include('partials.secure.menus.nestable-item', ['menu' => $child])
        @endforeach
    </ol>
    @endif
</li>