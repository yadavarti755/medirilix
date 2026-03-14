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

                <form id="contactDetailForm" enctype="multipart/form-data">
                    @csrf


                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Address: <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" value="{{ $contactDetail->address }}" required />
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Phone Numbers: <span class="text-danger">*</span></label>
                            <input type="text" name="phone_numbers" class="form-control" value="{{ $contactDetail->phone_numbers }}" required />
                            <small class="form-text text-muted">Enter multiple phone numbers, separated by commas.</small>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Email IDs: <span class="text-danger">*</span></label>
                            <input type="email" name="email_ids" class="form-control" value="{{ $contactDetail->email_ids }}" required />
                            <small class="form-text text-muted">Enter multiple email IDs, separated by commas.</small>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Is Primary: <span class="text-danger">*</span></label>
                            <select name="is_primary" class="form-control" required>
                                <option value="1" {{ $contactDetail->is_primary == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ $contactDetail->is_primary == 0 ? 'selected' : '' }}>No</option>
                            </select>
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
        $("#contactDetailForm").validate({
            rules: {
                address: {
                    required: true
                },
                phone_numbers: {
                    required: false
                },
                email_ids: {
                    required: false
                },
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('contact-details.update', $contactDetail->id) }}",
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
                                window.location.href = "{{ route('contact-details.index') }}";
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