<li class="dd-item menu-list-item" data-id="{{ $menu->id }}">
    <div class="menu-list-item-div">
        <div class="dd-handle">
            <span class="menu-list-item-link">
                {{ $menu->title }}
            </span>
        </div>
        <div class="menu-actions">
            <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-warning btn-sm">
                <i class="fa fa-edit"></i>
            </a>
            <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @if ($menu->children->isNotEmpty())
    <ol class="dd-list">
        @foreach ($menu->children as $child)
        @include('partials.secure.menus.menu-item', ['menu' => $child])
        @endforeach
    </ol>
    @endif
</li>