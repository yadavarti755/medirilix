@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$addRoute = route('medias.create');
@endphp
@can('add user')
@php
$button = '<a href="'.$addRoute.'" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>';
@endphp
@endcan
<x-page-header title="{{ $pageTitle }}" button="{!! (isset($button))?$button:'' !!}" />

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ request()->url() }}" class="d-flex align-items-center">
                    <div class="input-group">
                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by file name, original name, alt text, or mime type..."
                            value="{{ request()->get('search') }}"
                            autocomplete="off">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i> Search
                        </button>
                        @if(request()->get('search'))
                        <a href="{{ request()->url() }}" class="btn btn-secondary d-flex align-items-center gap-2">
                            <i class="fa fa-times"></i> Clear
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($medias->count() > 0)
        <!-- Search Results Info -->
        @if(request()->get('search'))
        <div class="alert alert-info">
            <strong>Search Results:</strong> Found {{ $medias->total() }} media(s) for "{{ request()->get('search') }}"
        </div>
        @endif

        <div class="row">
            @foreach ($medias as $media)
            <div class="col-md-3 col-sm-6 col-12 mb-3">
                <div class="card shadow">
                    <a href="{{ asset('storage/'. Config::get('file_paths')['MEDIA_IMAGE_PATH'] . '/' . $media->file_name) }}" target="_blank" class="cursor-pointer">
                        @if ($media->mime_type == 'video/mp4')
                        <img src="{{ asset('assets/images/video.png') }}" alt="{{ $media->alt_text ?? $media->alt_text_hi }}" title="{{ $media->alt_text ?? $media->alt_text_hi }}" class="card-img-top img-thumbnail media-preview-img">
                        @elseif (preg_match('/^image\//', $media->mime_type))
                        <img src="{{ $media->media_public_url }}" alt="{{ $media->alt_text ?? $media->alt_text_hi }}" title="{{ $media->alt_text ?? $media->alt_text_hi }}" class="card-img-top img-thumbnail media-preview-img">
                        @else
                        <img src="{{ asset('assets/images/file.png') }}" alt="{{ $media->alt_text ?? $media->alt_text_hi }}" title="{{ $media->alt_text ?? $media->alt_text_hi }}" class="card-img-top img-thumbnail media-preview-img">
                        @endif
                    </a>
                    <div class="card-body py-2 mb-0">
                        <a href="{{ $media->media_public_url }}" target="_blank" class="cursor-pointer">
                            <h5 class="card-title text-truncate">{{ $media->original_name }}</h5>
                        </a>
                        <!-- Size -->
                        <p class="text-dark mb-0"><strong>Size:</strong> {{ formatBytes($media->size) }}</p>
                        <!-- Mime type -->
                        <p class="text-dark mb-0 text-truncate" title="{{ $media->mime_type }}"><strong>Mime type:</strong> {{ $media->mime_type }}</p>
                        <!-- Url -->
                        <hr class="my-2">
                        <p class="text-dark mb-2">
                            <strong>Public Url:</strong>
                            <span class="copyPublicUrl cursor-pointer" title="Click to copy">{{ $media->media_public_url }}</span>
                        </p>
                    </div>
                    <button type="button" class="btn btn-danger delete-media" data-id="{{ $media->id }}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $medias->links('pagination::bootstrap-5') }}
        </div>
        @else
        <div class="alert alert-danger">
            <strong>Ooops!</strong>
            @if(request()->get('search'))
            No media found for your search "{{ request()->get('search') }}"
            @else
            No media available
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {

        /**
         * Delete record
         */
        $(document).on('click', '.delete-media', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete the record.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('medias.destroy', ':id') }}".replace(':id', id),
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            showLoader();
                        },
                        success: function(response) {
                            hideLoader();
                            Swal.fire("Deleted!", response.message, "success").then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            hideLoader();
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        });

        $('.copyPublicUrl').on('click', function() {
            const urlText = $(this).text();
            if (urlText) {
                navigator.clipboard.writeText(urlText).then(function() {
                    toastr.success('URL copied to clipboard')
                }).catch(function(err) {
                    console.error('Failed to copy: ', err);
                    toastr.error('Failed to copy the URL');
                });
            }
        })
    });
</script>
@endsection