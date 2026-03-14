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
                <form id="mediasForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label">File: <span class="text-danger">*</span></label>
                            <input type="file" name="file_name" class="form-control" />
                            <p class="text-danger mb-0">
                                Max allowed size 100MB.
                            </p>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Alt Text/Title (English):</label>
                            <input type="text" name="alt_text" class="form-control" />
                        </div>



                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save</button>
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
        $("#mediasForm").validate({
            rules: {
                file_name: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('medias.store') }}",
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
                                window.location.href = "{{ route('medias.index') }}";
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