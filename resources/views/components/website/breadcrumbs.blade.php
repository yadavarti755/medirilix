<nav aria-label="breadcrumb" class="mt-3 mb-4">
    <ol class="breadcrumb bg-transparent p-0 m-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>

        @if(!empty($breadcrumbs) && is_array($breadcrumbs))
            @foreach($breadcrumbs as $label => $url)
                @if ($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $url }}">{{ $label }}</a></li>
                @endif
            @endforeach
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle ?? 'Current Page' }}</li>
        @endif
    </ol>
</nav>
