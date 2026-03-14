@extends('layouts.website_layout')

@section('content')
@include('components.website.page-header')

<section class="cart-section">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-12" id="main-content" tabindex="-1">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-5">

                        @if(session('error'))
                        <!-- Error Card -->
                        <div class="card border-0 rounded-3 shadow-sm">
                            <div class="card-header py-3 border-0 bg-danger text-white">
                                <h3 class="h5 mb-0 text-center text-uppercase fw-bold">
                                    <i class="fa fa-exclamation-triangle"></i> Error
                                </h3>
                            </div>
                            <div class="card-body p-4 text-center">
                                <div class="alert alert-danger">
                                    <i class="fa fa-times-circle"></i>
                                    <strong>{{ session('error') }}</strong>
                                </div>
                                <a href="{{ route('public.login.forget-password.index') }}" class="btn btn-custom py-3 fw-semibold rounded-pill">
                                    <i class="fa fa-arrow-left"></i> Back to Forget Password
                                </a>
                            </div>
                        </div>
                        @else
                        <!-- Reset Password Form -->
                        <div class="card border-0 rounded-3 shadow-sm" id="resetPasswordCard">
                            <div class="card-header py-3 border-0 bg-transparent">
                                <h3 class="h5 mb-0 text-center text-uppercase fw-bold">
                                    <i class="fa fa-lock"></i> Reset Password
                                </h3>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="alert alert-info py-2">
                                        <i class="fa fa-info-circle"></i>
                                        <small>Please enter your new password below.</small>
                                    </div>
                                    <div class="text-muted">
                                        <small>Password must be at least 8 characters long and contain uppercase, lowercase, numbers, and special characters.</small>
                                    </div>
                                </div>

                                <form action="{{ route('public.login.reset-password.update') }}" method="POST" id="resetPasswordForm" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="form-floating mb-3 position-relative">
                                        <input type="password" name="password" class="form-control rounded-4" placeholder="Enter New Password" id="password">
                                        <label for="password">New Password</label>
                                        <span id="togglePassword" class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;">
                                            <i class="fa fa-eye" id="eyeIcon"></i>
                                        </span>
                                    </div>
                                    <div class="password-strength mb-3" id="passwordStrength"></div>

                                    <div class="form-floating mb-3 position-relative">
                                        <input type="password" name="password_confirmation" class="form-control rounded-4" placeholder="Confirm New Password" id="passwordConfirmation">
                                        <label for="passwordConfirmation">Confirm Password</label>
                                        <span id="togglePasswordConfirmation" class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;">
                                            <i class="fa fa-eye" id="eyeIconConfirmation"></i>
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <div class="captcha mb-2 d-flex align-items-center gap-2" id="captcha">
                                            <span>{!! captcha_img() !!}</span>
                                            <button type="button" class="btn btn-secondary rounded-4 btn-refresh" aria-label="Refresh Captcha">
                                                <i class="fa fa-sync"></i>
                                            </button>
                                        </div>
                                        <div class="form-floating">
                                            <input type="text" class="form-control rounded-4" name="captcha" placeholder="Enter Captcha" id="input-captcha">
                                            <label for="input-captcha">Captcha</label>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-custom py-4">
                                            <i class="fa fa-save"></i> Update Password
                                        </button>
                                    </div>

                                    <div class="text-center mt-3">
                                        <a href="{{ route('public.login') }}" class="text-decoration-none">
                                            <i class="fa fa-arrow-left"></i> Back to Login
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('pages-scripts')
<script @cspNonce>
    var encryptionKey = "{{ session()->get('encryption_key') }}";

    $(document).ready(function() {
        // Validate the reset password form
        $("#resetPasswordForm").validate({
            errorClass: 'text-danger validation-error',
            errorElement: 'span',
            rules: {
                password: {
                    required: true,
                    minlength: 8,
                    pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
                },
                password_confirmation: {
                    required: true,
                    equalTo: "#password"
                },
                captcha: {
                    required: true,
                }
            },
            messages: {
                password: {
                    required: "Please enter a new password",
                    minlength: "Password must be at least 8 characters long",
                    pattern: "Password must contain uppercase, lowercase, numbers, and special characters"
                },
                password_confirmation: {
                    required: "Please confirm your password",
                    equalTo: "Passwords do not match"
                },
                captcha: {
                    required: "Please enter the captcha code"
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                // Adjust error placement for floating labels
                if (element.parent().hasClass('form-floating')) {
                    error.insertAfter(element.parent());
                } else {
                    element.closest('.form-group').append(error);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                const password = encryptPassword(formData.get('password'), encryptionKey);
                const passwordConfirmation = encryptPassword(formData.get('password_confirmation'), encryptionKey);

                formData.set('password', password);
                formData.set('password_confirmation', passwordConfirmation);

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
                        let msg = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg,
                        });

                        encryptionKey = xhr.responseJSON.key;
                        refreshCaptcha();
                    }
                });
            }
        });

        $.validator.addMethod("pattern", function(value, element, param) {
            if (this.optional(element)) return true;
            if (typeof param === "string") {
                param = new RegExp(param);
            }
            return param.test(value);
        }, "Invalid format.");

        // Toggle password visibility
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const eyeIcon = $('#eyeIcon');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Toggle password confirmation visibility
        $('#togglePasswordConfirmation').click(function() {
            const passwordConfirmationField = $('#passwordConfirmation');
            const eyeIconConfirmation = $('#eyeIconConfirmation');

            if (passwordConfirmationField.attr('type') === 'password') {
                passwordConfirmationField.attr('type', 'text');
                eyeIconConfirmation.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordConfirmationField.attr('type', 'password');
                eyeIconConfirmation.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Password strength indicator
        $('#password').on('input', function() {
            const password = $(this).val();
            const strengthIndicator = $('#passwordStrength');

            if (password.length === 0) {
                strengthIndicator.html('');
                return;
            }

            let strength = 0;
            let strengthText = '';
            let strengthClass = '';

            // Check password criteria
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[@$!%*?&]/.test(password)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    strengthText = 'Very Weak';
                    strengthClass = 'text-danger';
                    break;
                case 2:
                    strengthText = 'Weak';
                    strengthClass = 'text-warning';
                    break;
                case 3:
                    strengthText = 'Fair';
                    strengthClass = 'text-info';
                    break;
                case 4:
                    strengthText = 'Good';
                    strengthClass = 'text-primary';
                    break;
                case 5:
                    strengthText = 'Strong';
                    strengthClass = 'text-success';
                    break;
            }

            const progressWidth = (strength / 5) * 100;
            strengthIndicator.html(`
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar ${strengthClass.replace('text-', 'bg-')}" style="width: ${progressWidth}%"></div>
                </div>
                <small class="${strengthClass}">Password Strength: ${strengthText}</small>
            `);
        });

        // Refresh captcha on button click
        $('.btn-refresh').click(function() {
            refreshCaptcha();
        });
    });

    function refreshCaptcha() {
        const formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: "{{ route('captcha.refresh') }}",
            type: "POST",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.site-loader').show();
            },
            complete: function() {
                $('.site-loader').hide();
            },
            success: function(data) {
                $('#captcha span').html(data.captcha);
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to refresh captcha. Please try again."
                });
            }
        });
    }
</script>
@endsection