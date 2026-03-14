@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST" id="menuForm">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="title">Title <span class="text-danger">*</span> </label>
                            <input type="text" name="title" id="title" class="form-control">
                        </div>
                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="url">URL</label>
                            <input type="text" name="url" id="url" class="form-control">
                        </div>
                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="parent_id">Parent Menu <span class="text-danger">*</span></label>
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="">None</option>
                                @php renderMenuOptions($menus) @endphp
                            </select>
                        </div>
                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="order">Order <span class="text-danger">*</span></label>
                            <input type="number" name="order" id="order" class="form-control">
                        </div>
                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="location">Location <span class="text-danger">*</span></label>
                            <select name="location" id="location" class="form-control" required>
                                <option value="">Select Location</option>
                                @foreach ($locations as $location)
                                <option value="{{ $location->location_code }}">
                                    {{ ucfirst($location->location_name) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none col-md-6 col-12">
                            <label class="form-label" for="permission_name">Permission Name</label>
                            <input type="text" name="permission_name" id="permission_name" class="form-control">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save
                        </button>
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
        // Initialize jQuery Validation
        $('#menuForm').validate({
            rules: {
                title: {
                    required: true,
                    maxlength: 255
                },

                order: {
                    required: true,
                    number: true
                },
                location: {
                    required: true
                },
                permission_name: {
                    maxlength: 255
                }
            },
            messages: {
                title: {
                    required: "Please enter a title.",
                    maxlength: "Title cannot exceed 255 characters."
                },
                url: {
                    url: "Please enter a valid URL."
                },
                order: {
                    required: "Please specify the order.",
                    number: "Order must be a number."
                },
                location: {
                    required: "Please select a location."
                },
                permission_name: {
                    maxlength: "Permission name cannot exceed 255 characters."
                }
            },
            submitHandler: function(form) {
                // Show loader
                showLoader();

                // Perform AJAX submission
                $.ajax({
                    url: "{{ route('menus.store') }}",
                    type: "POST",
                    data: $(form).serialize(),
                    dataType: 'json',
                    beforeSend: function() {
                        // Show loader
                        showLoader();
                    },
                    success: function(response) {
                        // Hide loader
                        hideLoader();

                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            // Show error message using Toastr
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        // Hide loader
                        hideLoader();

                        // Parse and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessages = [];
                            $.each(errors, function(key, value) {
                                errorMessages.push(value[0]);
                            });
                            toastr.error(errorMessages.join('<br>'));
                        } else {
                            toastr.error('An unexpected error occurred.');
                        }
                    }
                });
            }
        });
    });
</script>
@endsection