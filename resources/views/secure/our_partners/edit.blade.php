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
                <form id="ourPartnerForm" enctype="multipart/form-data">
                    @csrf


                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label">Image File:</label>
                            <input type="file" name="file_name" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp" />
                            <p class="text-danger mb-0">
                                Allowed types: jpg, jpeg, png, gif, webp. Max allowed size: 2MB.
                            </p>

                            @if ($ourPartner->file_name)
                            <div class="mt-2">
                                <strong>Current Image:</strong><br>
                                <img src="{{ $ourPartner->file_name_full_path }}"
                                    alt="{{ $ourPartner->title }}"
                                    class="img-fluid"
                                    style="max-height: 70px;">
                            </div>
                            @endif
                        </div>

                        <div class="col-md-12 col-12 mb-3">
                            <label class="form-label">Title: <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ $ourPartner->title }}" required />
                        </div>

                        <div class="col-md-12 col-12 mb-3">
                            <label class="form-label">Portal Link: <span class="text-danger">*</span></label>
                            <input type="url" name="link" class="form-control" value="{{ $ourPartner->link }}" required />
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update
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
        $("#ourPartnerForm").validate({
            rules: {
                file_name: {
                    required: false,
                    filesize: 2048000 // 2MB in bytes
                },
                title: {
                    required: true,
                    maxlength: 255
                },
                link: {
                    required: true,
                    url: true,
                    maxlength: 2048
                }
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('our-partners.update', $ourPartner->id) }}",
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
                                window.location.href = "{{ route('our-partners.index') }}";
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

        // Custom validator for file size
        $.validator.addMethod('filesize', function(value, element, param) {
            return this.optional(element) || (element.files[0].size <= param);
        }, 'File size must be less than 2MB.');
    });
</script>
@endsection