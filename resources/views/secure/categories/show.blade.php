@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 col-12 mb-3">
                        <label class="form-label fw-bold">Parent Category:</label>
                        <div>
                            {{ $category->parent ? $category->parent->name : 'None' }}
                        </div>
                    </div>

                    <div class="col-md-6 col-12 mb-3">
                        <label class="form-label fw-bold">Order:</label>
                        <div>{{ $category->order ?? '-' }}</div>
                    </div>

                    <div class="col-md-6 col-12 mb-3">
                        <label class="form-label fw-bold">Image:</label>
                        <div>
                            @if($category->image)
                            <img src="{{ $category->image_path }}"
                                alt="Category Image" class="img-thumbnail" width="120">
                            @else
                            <span>No image uploaded</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6 col-12 mb-3">
                        <label class="form-label fw-bold">Name:</label>
                        <div>{{ $category->name }}</div>
                    </div>

                    <div class="col-md-6 col-12 mb-3">
                        <label class="form-label fw-bold">Is Published:</label>
                        <div>
                            @if($category->is_published)
                            <span class="badge bg-success">Published</span>
                            @else
                            <span class="badge bg-danger">Unpublished</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6 col-12 mb-3">
                        <label class="form-label fw-bold">Description:</label>
                        <div>{{ $category->description ?? '-' }}</div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection