@extends('layouts.auth_layout')

@section('content')

<div class="auth-main">
    <div class="auth-wrapper v3">
        <div class="auth-form">
            <div id="loginCard">
                <div>
                    <div>
                        @if(session('error'))
                        <!-- Error Card -->
                        <div class="card border-0 elevation-4 rounded-3 overflow-hidden">
                            <div class="card-header bg-danger text-white py-3">
                                <h3 class="h6 mb-0 text-center text-uppercase">
                                    <i class="fa fa-exclamation-triangle"></i> Error
                                </h3>
                            </div>
                            <div class="card-body p-4 text-center">
                                <div class="alert alert-danger">
                                    <i class="fa fa-times-circle"></i>
                                    <strong>{{ session('error') }}</strong>
                                </div>
                                <a href="{{ route('employee-forget-password.index') }}" class="btn btn-primary py-3 fw-semibold rounded-pill">
                                    <i class="fa fa-arrow-left"></i> Back to Forget Password
                                </a>
                            </div>
                        </div>
                        @else
                        <!-- Reset Password Form -->
                        <div class="card border-0 elevation-4 rounded-3 overflow-hidden" id="resetPasswordCard">
                            <div class="card-header bg-success text-white py-3">
                                <h3 class="h5 mb-0 text-center text-uppercase">
                                    <i class="fa fa-lock"></i> Reset Password
                                </h3>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        Please enter your new password below.
                                    </div>
                                    <div class="text-muted">
                                        <small>Password must be at least 8 characters long and contain uppercase, lowercase, numbers, and special characters.</small>
                                    </div>
                                </div>

                                <form action="{{ route('backend-reset-password.update') }}" method="POST" id="resetPasswordForm">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="form-group mb-3">
                                        <label class="form-label">New Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control" placeholder="Enter New Password" id="password">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fa fa-eye" id="eyeIcon"></i>
                                            </button>
                                        </div>
                                        <div class="password-strength mt-2" id="passwordStrength"></div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm New Password" id="passwordConfirmation">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                                <i class="fa fa-eye" id="eyeIconConfirmation"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <div>
                                            <div class="captcha mb-2" id="captcha">
                                                <span>{!! captcha_img() !!}</span>
                                                <button type="button" class="btn btn-secondary btn-refresh"><i class="fa fa-sync"></i></button>
                                            </div>
                                        </div>
                                        <label class="form-label">Captcha</label>
                                        <input type="text" class="form-control" name="captcha" placeholder="Enter Captcha">
                                    </div>

                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-success py-3 fw-semibold rounded-pill">
                                            <i class="fa fa-save"></i> Update Password
                                        </button>
                                        <a href="{{ route('employee-corner.login') }}" class="btn btn-outline-secondary py-2 rounded-pill">
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
</div>

@endsection

@section('pages-scripts')
<script @cspNonce>
    var encryptionKey = "{{ session()->get('encryption_key') }}";

    $(document).ready(function() {
        // Validate the reset password form
        $("#resetPasswordForm").validate({
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
                element.closest('.form-group').append(error);
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