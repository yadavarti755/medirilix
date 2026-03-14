@foreach ($items as $key => $item)
@php
$currentUrl = url($item->url);
$hasChildren = count($item->children) > 0;
$linkId = 'menu-item-' . $item->id;
@endphp

<li class="nav-item {{ $hasChildren ? 'dropdown' : '' }} {{ isset($isChild) ? 'dropdown-submenu main-menu-dropdown-submenu' : '' }} position-relative">

    @if ($hasChildren)
    {{-- Use a button to toggle submenu for parents to avoid redundant links --}}
    <button
        id="{{ $linkId }}"
        class="{{ isset($isChild) ? 'dropdown-item' : 'nav-link dropdown-toggle' }} dropdown-toggle py-2 ripple-effect d-flex justify-content-between align-items-center"
        type="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
        aria-controls="submenu-{{ $item->id }}">
        {{ getLocalizedDataFromObj($item, 'title') }}
        <i class="fas {{ isset($isChild) ? 'fa-angle-right' : 'fa-angle-down' }} ms-2"></i>
    </button>

    {{-- Render submenu --}}
    <ul
        class="dropdown-menu main-menu-dropdown-menu shadow border-0 rounded-3 {{ isset($isChild) ? 'dropdown-submenu main-menu-dropdown-submenu' : 'elevation-3' }}"
        id="submenu-{{ $item->id }}"
        aria-labelledby="{{ $linkId }}">
        @include('components.website.menu-item', ['items' => $item->children, 'isChild' => true])
    </ul>
    @else
    {{-- For leaf nodes, render a single link --}}
    <a
        id="{{ $linkId }}"
        class="{{ isset($isChild) ? 'dropdown-item' : 'nav-link' }} py-2 ripple-effect"
        href="{{ $currentUrl }}"
        tabindex="0">
        {{ getLocalizedDataFromObj($item, 'title') }}
    </a>
    @endif
</li>
@endforeach