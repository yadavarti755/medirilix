@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p>All (<span class="text-danger">*</span>) marked fields are required.</p>

                <form id="socialMediaForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Type: <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="">-- Select Platform --</option>
                                @foreach ($socialMediaPlatforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Name: <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required />
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">URL: <span class="text-danger">*</span></label>
                            <input type="url" name="url" class="form-control" required />
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Icon Class:</label>
                            <input type="text" name="icon_class" class="form-control" placeholder="e.g., fa fa-facebook" />
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save
                            </button>
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
        $("#socialMediaForm").validate({
            rules: {
                name: {
                    required: true
                },
                url: {
                    required: true,
                    url: true
                },
                icon_class: {
                    required: false
                },
                type: {
                    required: true
                }
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('social-medias.store') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "{{ route('social-medias.index') }}";
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
                                text: "Something went wrong. Please try again.",
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