@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body position-relative">
                        <div class="text-center">
                            <div class="chat-avtar d-inline-flex mx-auto">
                                <img class="rounded-circle img-fluid" src="{{ generate_file_view_path_for_backend(auth()->user()->profile_image_full_path) }}"
                                    alt="User image" style="width: 100px; height: 100px;" />
                            </div>
                            <h5 class="mt-3 mb-1">{{ auth()->user()->name }}</h5>
                            <p class="text-muted">
                                @foreach(auth()->user()->roles as $role)
                                {{ $role->name }}
                                @endforeach
                            </p>
                        </div>
                        <div class="nav flex-column nav-pills list-group list-group-flush user-sett-tabs" id="user-set-tab"
                            role="tablist" aria-orientation="vertical">
                            <a class="nav-link list-group-item list-group-item-action active" id="user-tab-1"
                                data-bs-toggle="pill" href="#user-cont-1" role="tab">
                                <span class="f-w-500"><i class="ti ti-user m-r-10"></i>Personal Information</span>
                            </a>
                            <a class="nav-link list-group-item list-group-item-action" id="user-tab-3" data-bs-toggle="pill"
                                href="#user-cont-3" role="tab">
                                <span class="f-w-500"><i class="ti ti-lock m-r-10"></i>Change Password</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="tab-content" id="user-set-tabContent">
                    <div class="tab-pane fade show active" id="user-cont-1" role="tabpanel">
                        <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')

                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5>Personal Information</h5>
                                            <hr class="mb-4">
                                        </div>
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Profile Photo</label>
                                                <input type="file" class="form-control form-control-file" name="profile_image" accept=".jpg, .png, .jpeg" />
                                                <span class="text-danger">Only accept .jpg, .png, .jpeg file types. Max file size 1MB.</span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Mobile Number</label>
                                                <input type="text" class="form-control" name="mobile_number" value="{{ auth()->user()->mobile_number }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end btn-page">
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="user-cont-3" role="tabpanel">
                        <form id="changePasswordForm" action="{{ route('profile.change-password') }}" method="POST">
                            @csrf
                            @method('POST')

                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5>Change Password</h5>
                                            <hr class="mb-4">
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Old Password</label>
                                                <input type="password" class="form-control" name="old_password">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">New Password</label>
                                                <input type="password" class="form-control" name="new_password">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Confirm Password</label>
                                                <input type="password" class="form-control" name="new_password_confirmation">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h5>New password must contain:</h5>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">✔ At least 8 characters</li>
                                                <li class="list-group-item">✔ At least 1 lowercase letter (a-z)</li>
                                                <li class="list-group-item">✔ At least 1 uppercase letter (A-Z)</li>
                                                <li class="list-group-item">✔ At least 1 number (0-9)</li>
                                                <li class="list-group-item">✔ At least 1 special character (!@#$%^&*)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end btn-page">
                                    <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $('#profileForm').validate({
        rules: {
            name: {
                required: true
            },
            mobile_number: {
                required: false
            },
            // profile_image: {
            //     extension: "jpg|jpeg|png",
            //     filesize: 1048576 // 1MB
            // }
        },
        messages: {
            photo: {
                extension: "Only .jpg, .jpeg, .png files allowed.",
                filesize: "File size must be less than 1MB."
            }
        },
        submitHandler: function(form) {
            let formData = new FormData(form);
            $.ajax({
                url: $(form).attr('action'),
                method: $(form).attr('method'),
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => location.reload());
                },
                error: function(xhr) {
                    let msg = 'An error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg,
                    });
                }
            });
        }
    });

    // Custom validator for file size
    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    });

    // ======================================================
    // ======================================================
    var encryptionKey = "{{ session()->get('encryption_key') }}";

    $('#changePasswordForm').validate({
        rules: {
            old_password: {
                required: true
            },
            new_password: {
                required: true,
                minlength: 8,
                pwcheck: true
            },
            new_password_confirmation: {
                required: true,
                equalTo: '[name="new_password"]'
            }
        },
        messages: {
            new_password: {
                minlength: "Password must be at least 8 characters long.",
                pwcheck: "Password must contain uppercase, lowercase, number, and special character."
            },
            new_password_confirmation: {
                equalTo: "Passwords do not match."
            }
        },
        submitHandler: function(form) {
            let formData = new FormData(form);

            const oldPassword = encryptPassword(formData.get('old_password'), encryptionKey);
            const newPassword = encryptPassword(formData.get('new_password'), encryptionKey);
            const newPasswordConfirmation = encryptPassword(formData.get('new_password_confirmation'), encryptionKey);

            formData.set('old_password', oldPassword);
            formData.set('new_password', newPassword);
            formData.set('new_password_confirmation', newPasswordConfirmation);

            $.ajax({
                url: $(form).attr('action'),
                method: $(form).attr('method'),
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    encryptionKey = response.key;
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        html: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = response.redirect_url
                    });
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

                    encryptionKey = xhr.responseJSON.key;
                }
            });
        }
    });

    // Custom validator for strong password
    $.validator.addMethod("pwcheck", function(value) {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#]).{8,}$/.test(value);
    });
</script>
@endsection