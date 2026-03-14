@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p>
                    All (<span class="text-danger">*</span>) marked fields are required.
                </p>
                <form id="sliderForm" enctype="multipart/form-data">
                    @csrf


                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label">Image File:</label>
                            <input type="file" name="file_name" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp" />
                            <p class="text-danger mb-0">
                                Allowed types jpg, jpeg, png, gif, webp. Max allowed size 2MB.
                            </p>

                            @if ($slider->file_name)
                            <div class="mt-2">
                                <strong>Current Image:</strong><br>
                                <img src="{{ $slider->file_url }}" alt="Slider Image" class="img-fluid" style="max-height: 70px;">
                            </div>
                            @endif
                        </div>

                        <div class="col-md-12 col-12 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-control select2">
                                <option value="">Select Category</option>
                                @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->id == $slider->category_id ? "selected":"" }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 col-12 mb-3">
                            <label class="form-label">Title:</label>
                            <input type="text" name="title" class="form-control" value="{{ $slider->title }}" />
                        </div>

                        <div class="col-md-12 col-12 mb-3">
                            <label class="form-label">Subtitle:</label>
                            <input type="text" name="subtitle" class="form-control" value="{{ $slider->subtitle }}" />
                        </div>

                        <div class="col-md-12 col-12 mb-3">
                            <label class="form-label">Description:</label>
                            <textarea name="description" class="form-control" rows="3">{{ $slider->description }}</textarea>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        $("#sliderForm").validate({
            rules: {
                file_name: {
                    required: false
                }
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('sliders.update', $slider->id) }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Updated!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "{{ route('sliders.index') }}";
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = Object.values(errors).flat().join("<br>");
                            Swal.fire({
                                title: "Validation Error",
                                html: errorMessages,
                                icon: "error"
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: xhr.responseJSON.message || "Something went wrong. Please try again.",
                                icon: "error"
                            });
                        }
                    }
                });
            }
        });
    });
</script>
@endsection