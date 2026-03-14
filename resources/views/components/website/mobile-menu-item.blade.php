@php
$renderedUrls = [];
@endphp

@foreach ($items as $key => $item)
@php
$hasChildren = count($item->children) > 0;
$title = getLocalizedDataFromObj($item, 'title');
$itemUrl = !empty($item->url) ? url()->to($item->url) : '#';
$itemId = 'menuItem_' . md5($title . $key . ($isMobile ?? 'desktop'));
$isActive = Request::is(ltrim($item->url, '/') . '*') ? 'active text-primary fw-semibold' : '';
@endphp

{{-- Skip redundant links --}}
@if (!$hasChildren && in_array($itemUrl, $renderedUrls))
@continue
@endif

@php
// Store the current URL to prevent future duplication
if (!$hasChildren && $itemUrl !== '#') {
$renderedUrls[] = $itemUrl;
}
@endphp

@if ($hasChildren)
@if (!empty($isMobile))
{{-- Mobile: Collapsible submenu with toggle ONLY --}}
<li class="nav-item">
    <a class="nav-link text-dark py-3 border-bottom d-flex justify-content-between align-items-center ripple-effect"
        data-bs-toggle="collapse"
        href="#{{ $itemId }}"
        role="button"
        aria-expanded="false"
        aria-controls="{{ $itemId }}">
        <span>{{ $title }}</span>
        <i class="fas fa-chevron-down small"></i>
    </a>
    <div class="collapse" id="{{ $itemId }}">
        <ul class="nav flex-column ms-3 ps-2 border-start border-2 border-primary-subtle">
            @include('components.website.mobile-menu-item', [
            'items' => $item->children,
            'isMobile' => true,
            'isChild' => true
            ])
        </ul>
    </div>
</li>
@else
{{-- Desktop: Dropdown submenu --}}
<li class="dropdown-submenu position-relative">
    <a href="#"
        class="{{ isset($isChild) ? 'dropdown-item' : 'nav-link text-dark' }} dropdown-toggle py-2 ripple-effect d-flex justify-content-between align-items-center"
        role="button"
        data-bs-toggle="dropdown"
        aria-expanded="false">
        {{ $title }}
        <i class="fas {{ isset($isChild) ? 'fa-angle-right' : 'fa-angle-down' }} ms-2"></i>
    </a>
    <ul class="dropdown-menu shadow border-0 rounded-3 mt-1 {{ isset($isChild) ? 'dropdown-submenu ms-2' : 'elevation-3' }}">
        @include('components.website.mobile-menu-item', [
        'items' => $item->children,
        'isChild' => true,
        'isMobile' => $isMobile ?? false
        ])
    </ul>
</li>
@endif
@else
{{-- Leaf node (no children): clickable link --}}
<li class="nav-item">
    <a class="{{ isset($isChild) || !empty($isMobile) ? 'nav-link text-dark py-2 ripple-effect ps-4' : 'nav-link text-dark py-2 ripple-effect' }} {{ $isActive }}"
        href="{{ $itemUrl }}">
        {{ $title }}
    </a>
</li>
@endif
@endforeach