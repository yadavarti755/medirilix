@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <x-backend.category-form :categories="$categories" />
            </div>
        </div>
    </div>
</div>
@endsection

@section('pages-scripts')
@yield('category_form_script')
@endsection