@php
$hasChildren = isset($menu->children) && $menu->children->isNotEmpty();
$isActive = request()->url() === url($menu->url);
$menuTitle = trim(getLocalizedDataFromObj($menu, 'title'));
@endphp

<div class="nav-item-wrapper">
    <div class="d-flex justify-content-between align-items-center nav-link border-bottom p-3 {{ $isActive ? 'active text-primary fw-bold' : 'text-dark' }}">
        <a
            href="{{ $hasChildren ? '#' : url()->to($menu->url) }}"
            class="d-flex align-items-center text-decoration-none {{ $isActive ? 'text-primary fw-bold' : 'text-dark' }}"
            @if(empty($menuTitle))
                aria-label="Menu item"
            @endif
        >
            @if (!empty($menu->icon))
                <i class="{{ $menu->icon }} me-2 {{ $isActive ? 'text-primary' : 'text-dark' }}"></i>
            @endif

            <span class="fw-medium">
                {{ $menuTitle ?: __('title.menu_item') }}
            </span>
        </a>

        @if ($hasChildren)
            <button
                class="btn btn-sm toggle-submenu p-0 border-0 bg-transparent"
                aria-label="{{ $isActive ? 'Collapse submenu' : 'Expand submenu' }}"
                aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                aria-controls="submenu-{{ $loop->index }}"
            >
                <i class="fas fa-plus {{ $isActive ? 'text-primary' : 'text-dark' }}"></i>
            </button>
        @endif
    </div>

    @if ($hasChildren)
    <div
        id="submenu-{{ $loop->index }}"
        class="submenu ps-3 {{ $menu->children->contains(fn($child) => request()->url() === url($child->url)) ? '' : 'd-none' }}"
    >
        @foreach ($menu->children as $child)
            @include('components.website.side-nav-item', ['menu' => $child])
        @endforeach
    </div>
    @endif
</div>
