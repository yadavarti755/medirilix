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
                    <div class="col-12 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Image File:</strong></label>
                        <div>
                            @if ($slider->file_name)
                            <img src="{{ $slider->file_url }}"
                                alt="Slider Image"
                                class="img-fluid border"
                                style="max-height: 150px;">
                            @else
                            <p class="text-muted mb-0">No image uploaded.</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Category:</strong></label>
                        <p class="mb-0">{{ $slider->category_id &&  $slider->category && $slider->category->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Title:</strong></label>
                        <p class="mb-0">{{ $slider->title ?? '—' }}</p>
                    </div>
                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Subtitle:</strong></label>
                        <p class="mb-0">{{ $slider->subtitle ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Description:</strong></label>
                        <p class="mb-0">{{ $slider->description ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="card card-body">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="row">
                        <div class="col-12 card py-2 bg-light mb-3">
                            <label class="form-label"><strong>Publish Status:</strong></label>
                            <p class="mb-0">
                                {!! $slider->is_published
                                ? '<span class="badge bg-primary">Published</span>'
                                : '<span class="badge bg-warning text-dark">Not Published</span>' !!}
                            </p>
                        </div>
                    </div>
                </div>
                @can('publish slider')
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fa fa-paper-plane"></i> Publish Form
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form id="publishForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Publish Status <span class="text-danger">*</span></label>
                                            <select name="is_published" class="form-control" required>
                                                <option value="">-- Select Option --</option>
                                                <option value="1">Publish</option>
                                                <option value="0">Unpublish</option>
                                            </select>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-upload"></i> Submit
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>

    </div>
</div>
@endsection


@section('pages-scripts')
@can('publish slider')
<script @cspNonce>
    $(document).ready(function() {
        // Publish Form Validation
        $("#publishForm").validate({
            rules: {
                is_published: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('sliders.publish', $slider->id) }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire("Success!", response.message, "success").then(() => {
                            window.location.href = "{{ route('sliders.index') }}";
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = Object.values(errors).flat().join("<br>");
                            Swal.fire("Validation Error", errorMsg, "error");
                        } else {
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    }
                });

                return false;
            }
        });
    });
</script>
@endcan
@endsection