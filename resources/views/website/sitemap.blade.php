@extends('layouts.website_layout')

@section('content')
<main>
    @include('components.website.page-header')

    <!-- Main Content -->
    <div class="container py-5" id="main-content" tabindex="-1">
        @include('components.website.sitemap', ['menus' => $menus])
    </div>
</main>
@endsection

@section('pages-scripts')
<script @cspNonce>

</script>
@endsection
