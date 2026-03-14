@extends('layouts.user_layout')

@section('content')
<!-- [ Page Header ] start -->
<div class="page-heading mb-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-12">
                <h4 class="m-0 text-dark fw-bold page-title">{{ $pageTitle }}</h4>
                <p class="text-muted small mb-0">Manage your account settings and preferences</p>
            </div>
        </div>
    </div>
</div>
<!-- [ Page Header ] end -->

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Left Column: User Card & Nav -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body text-center p-5">
                        <div class="mb-3 position-relative d-inline-block">
                            <img class="rounded-circle shadow-sm border border-3 border-white"
                                src="{{ generate_file_view_path_for_backend(auth()->user()->profile_image_full_path) }}"
                                alt="User image"
                                style="width: 120px; height: 120px; object-fit: cover;" />
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-muted small mb-3">
                            @foreach(auth()->user()->roles as $role)
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $role->name }}</span>
                            @endforeach
                        </p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="list-group list-group-flush" id="user-set-tab" role="tablist">
                        <a class="list-group-item list-group-item-action p-3 active border-0 text-decoration-none" id="user-tab-1"
                            data-bs-toggle="pill" href="#user-cont-1" role="tab">
                            <i class="fas fa-user-circle me-3 text-muted"></i> <span class="fw-medium">Personal Information</span>
                        </a>
                        <a class="list-group-item list-group-item-action p-3 border-0 text-decoration-none" id="user-tab-3" data-bs-toggle="pill"
                            href="#user-cont-3" role="tab">
                            <i class="fas fa-lock me-3 text-muted"></i> <span class="fw-medium">Change Password</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Content Forms -->
            <div class="col-lg-8">
                <div class="tab-content" id="user-set-tabContent">
                    <!-- Personal Info Tab -->
                    <div class="tab-pane fade show active" id="user-cont-1" role="tabpanel">
                        <form id="profileForm" action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')

                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-header bg-white border-bottom p-4">
                                    <h6 class="fw-bold mb-0">Personal Information</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label class="form-label fw-medium small text-uppercase text-muted">Profile Photo</label>
                                            <input type="file" class="form-control" name="profile_image" accept=".jpg, .png, .jpeg" />
                                            <small class="text-muted d-block mt-2">Allowed: .jpg, .png, .jpeg. Max: 1MB.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium small text-uppercase text-muted">Full Name</label>
                                            <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium small text-uppercase text-muted">Mobile Number</label>
                                            <input type="text" class="form-control" name="mobile_number" value="{{ auth()->user()->mobile_number }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium small text-uppercase text-muted">Email Address</label>
                                            <input type="email" class="form-control bg-light text-muted" name="email" value="{{ auth()->user()->email }}" disabled>
                                            <small class="text-muted">Email cannot be changed.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top p-4 text-end">
                                    <button type="reset" class="btn btn-light rounded-pill px-4 me-2">Reset</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="user-cont-3" role="tabpanel">
                        <form id="changePasswordForm" action="{{ route('user.profile.change-password') }}" method="POST">
                            @csrf
                            @method('POST')

                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-header bg-white border-bottom p-4">
                                    <h6 class="fw-bold mb-0">Change Password</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-lg-7">
                                            <div class="mb-3">
                                                <label class="form-label fw-medium small text-uppercase text-muted">Current Password</label>
                                                <input type="password" class="form-control" name="old_password">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-medium small text-uppercase text-muted">New Password</label>
                                                <input type="password" class="form-control" name="new_password">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-medium small text-uppercase text-muted">Confirm New Password</label>
                                                <input type="password" class="form-control" name="new_password_confirmation">
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="p-3 bg-light rounded-3 h-100">
                                                <h6 class="fw-bold small mb-3 text-dark">Password Requirements:</h6>
                                                <ul class="list-unstyled mb-0 small text-muted">
                                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>At least 8 characters</li>
                                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>One lowercase letter</li>
                                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>One uppercase letter</li>
                                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>One number</li>
                                                    <li><i class="fas fa-check-circle text-success me-2"></i>One special character</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top p-4 text-end">
                                    <button type="reset" class="btn btn-light rounded-pill px-4 me-2">Cancel</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Update Password</button>
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