@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$addRoute = route('categories.create');
@endphp
<x-page-header title="{{ $pageTitle }}" button='<a href="{{ $addRoute }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>' />

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if(count($categories) > 0)
                <div class="dd" id="nestable-category">
                    <ol class="dd-list menu-list">
                        @foreach ($categories as $category)
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
                        @endforeach
                    </ol>
                </div>

                <div class="text-center mt-3">
                    <button id="save-order" class="btn btn-success">
                        Save Order
                    </button>
                </div>
                @else
                <div class="alert alert-danger">
                    <p class="mb-0">
                        No categories are available.
                    </p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {

        // Filter categories by location
        $('#filter_menu_location').on('change', function() {
            var location = $(this).val();
            if (location) {
                var url = "{{ route('categories.index') }}?location=" + location;
                window.location.href = url;
            } else {
                var url = "{{ route('categories.index') }}";
                window.location.href = url;
            }
        });

        $('#nestable-category').nestable({
            maxDepth: 10 // Adjust the maximum depth as needed
        });

        $('#save-order').on('click', function() {
            var order = $('#nestable-category').nestable('serialize');
            $.ajax({
                type: 'POST',
                url: "{{ route('categories.update-order') }}",
                data: {
                    order: order,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Display SweetAlert2 success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'Category order has been updated.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Reload the page after user clicks 'OK'
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    // Display SweetAlert2 error message
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while updating the category order.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

    });
</script>
@endsection