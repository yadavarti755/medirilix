@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$addRoute = route('menus.create');
@endphp
@can('add menu')
<x-page-header title="{{ $pageTitle }}" button='<a href="{{ $addRoute }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>' />
@else
<x-page-header title="{{ $pageTitle }}" />
@endcan

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-12">
                        <label for="filter_menu_location" class="form-label">
                            Filter by Location
                        </label>
                        <select name="filter_menu_location" id="filter_menu_location" class="form-control">
                            <option value="">Select Location</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->location_code }}" @if ($firstLocation->location_code == $location->location_code) selected @endif>{{ ucfirst($location->location_name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                @if(count($menus) > 0)
                <div class="dd" id="nestable-menu">
                    <ol class="dd-list menu-list">
                        @foreach ($menus as $menu)
                        <li class="dd-item menu-list-item" data-id="{{ $menu->id }}">
                            <div class="menu-list-item-div">
                                <div class="dd-handle">
                                    <span class="menu-list-item-link">
                                        {{ $menu->title }}
                                    </span>
                                </div>
                                <div class="menu-actions">
                                    @can('edit menu')
                                    <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete menu')
                                    <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                            @if ($menu->children->isNotEmpty())
                            <ol class="dd-list">
                                @foreach ($menu->children as $child)
                                @include('partials.secure.menus.menu-item', ['menu' => $child])
                                @endforeach
                            </ol>
                            @endif
                        </li>
                        @endforeach
                    </ol>
                </div>

                <div class="text-center mt-3">
                    @can('edit menu')
                    <button id="save-order" class="btn btn-success">
                        Save Order
                    </button>
                    @endcan
                </div>
                @else
                <div class="alert alert-danger">
                    <p class="mb-0">
                        No menus are available.
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

        // Filter menus by location
        $('#filter_menu_location').on('change', function() {
            var location = $(this).val();
            if (location) {
                var url = "{{ route('menus.index') }}?location=" + location;
                window.location.href = url;
            } else {
                var url = "{{ route('menus.index') }}";
                window.location.href = url;
            }
        });

        $('#nestable-menu').nestable({
            maxDepth: 10 // Adjust the maximum depth as needed
        });

        $('#save-order').on('click', function() {
            var order = $('#nestable-menu').nestable('serialize');
            $.ajax({
                type: 'POST',
                url: "{{ route('menus.updateOrder') }}",
                data: {
                    order: order,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Display SweetAlert2 success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'Menu order has been updated.',
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
                        text: 'An error occurred while updating the menu order.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

    });
</script>
@endsection