<li class="dd-item menu-list-item" data-id="{{ $category->id }}">
    <div class="menu-list-item-div">
        <div class="dd-handle">
            <span class="menu-list-item-link d-flex align-items-center gap-2">
                <img src="{{ $category->image_path }}" alt="Image" class="img-thumbnail category-image"> {{ $category->name }} {!! $category->is_published_desc !!}
            </span>
        </div>
        <div class="menu-actions">
            <a href="{{ route('categories.show', $category->id) }}" class="btn btn-primary btn-sm">
                <i class="fa fa-eye"></i>
            </a>
            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                <i class="fa fa-edit"></i>
            </a>
            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @if ($category->children->isNotEmpty())
    <ol class="dd-list">
        @foreach ($category->children as $child)
        @include('partials.secure.categories.menu-item', ['category' => $child])
        @endforeach
    </ol>
    @endif
</li>