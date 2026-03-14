@php
function renderSitemap($items)
{
echo '<ul>';
    foreach ($items as $item) {
    // Only render top-level menus if this is the full menu list
    if (!isset($item->parent_id) || $item->parent_id == null) {
    echo '<li class="mt-2">';
        echo '<a href="' . url($item->url) . '" class="text-dark">' . getLocalizedDataFromObj($item, 'title') . '</a>';

        // If children exist, render them recursively
        if (!empty($item->children) && count($item->children) > 0) {
        renderSubmenus($item->children);
        }

        echo '</li>';
    }
    }
    echo '</ul>';
}

function renderSubmenus($children)
{
echo '<ul>';
    foreach ($children as $child) {
    echo '<li class="mt-2">';
        echo '<a href="' . url($child->url) . '" class="text-dark">' . getLocalizedDataFromObj($child, 'title') . '</a>';

        if (!empty($child->children) && count($child->children) > 0) {
        renderSubmenus($child->children); // deeper levels
        }

        echo '</li>';
    }
    echo '</ul>';
}
@endphp

{!! renderSitemap($menus) !!}