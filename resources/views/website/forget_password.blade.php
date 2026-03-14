@extends('layouts.website_layout')

@section('content')

@include('components.website.page-header')

<section class="cart-section">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-12" id="main-content" tabindex="-1">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-5">

                        <!-- Forget Password Form -->
                        <div class="card border-0 rounded-3 shadow-sm" id="forgetPasswordCard">
                            <div class="card-header py-3 border-0 bg-transparent">
                                <h3 class="h5 mb-0 text-center text-uppercase fw-bold">
                                    <i class="fa fa-key"></i> Forget Password
                                </h3>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="alert alert-info py-2">
                                        <i class="fa fa-info-circle"></i>
                                        <small>Enter your email or mobile number to reset your password</small>
                                    </div>
                                </div>
                                <div id="aria-alert" class="sr-only" role="alert" aria-live="assertive"></div>
                                <form action="" id="forgetPasswordForm" autocomplete="off">
                                    @csrf
                                    <div class="form-floating mb-3">
                                        <input id="input-email" type="text" name="login_field" class="form-control rounded-4" placeholder="Enter Email or Mobile">
                                        <label for="input-email">Email or Mobile <span class="text-danger">*</span></label>
                                    </div>

                                    <div class="mb-3">
                                        <div class="captcha mb-2 d-flex align-items-center gap-2" id="captcha">
                                            <img src="{{ captcha_src() }}" alt="Captcha Image" class="rounded" style="height: 40px;">
                                            <button type="button" id="refresh-captcha" class="btn btn-secondary rounded-4" aria-label="Refresh Captcha">
                                                <i class="fa fa-sync"></i>
                                            </button>
                                        </div>

                                        <div class="form-floating">
                                            <input type="text" id="input-captcha" class="form-control rounded-4" name="captcha" placeholder="Captcha">
                                            <label for="input-captcha">Captcha <span class="text-danger">*</span></label>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-custom py-4">
                                            <i class="fa fa-paper-plane"></i> Send OTP
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

                        <!-- OTP Verification Form -->
                        <div class="card border-0 rounded-3 shadow-sm" id="otpVerificationCard" style="display: none;">
                            <div class="card-header py-3 border-0 bg-transparent">
                                <h3 class="h5 mb-0 text-center text-uppercase fw-bold">
                                    <i class="fa fa-shield-alt"></i> Verify OTP
                                </h3>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="alert alert-info py-2">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Security Check</strong><br>
                                        <small>A 6-digit OTP has been sent to your registered email/mobile.</small>
                                    </div>
                                    <div id="contactInfo" class="small text-muted mb-3"></div>
                                </div>

                                <form action="" id="otpVerificationForm" autocomplete="off">
                                    @csrf
                                    <fieldset class="form-group mb-4">
                                        <legend class="form-label text-center d-block h6">Enter OTP</legend>
                                        <div class="otp-input-container d-flex justify-content-center gap-2 mb-3">
                                            <style>
                                                .otp-input {
                                                    width: 50px;
                                                    height: 50px;
                                                    font-size: 1.5rem;
                                                    font-weight: bold;
                                                    border: 1px solid #dee2e6;
                                                    border-radius: 8px;
                                                }

                                                .otp-input:focus {
                                                    border-color: #0d6efd;
                                                    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
                                                }

                                                .otp-input.filled {
                                                    border-color: #198754;
                                                    background-color: #f8f9fa;
                                                }
                                            </style>
                                            <input type="text" class="form-control otp-input text-center" maxlength="1" data-index="0" aria-label="Digit 0">
                                            <input type="text" class="form-control otp-input text-center" maxlength="1" data-index="1" aria-label="Digit 1">
                                            <input type="text" class="form-control otp-input text-center" maxlength="1" data-index="2" aria-label="Digit 2">
                                            <input type="text" class="form-control otp-input text-center" maxlength="1" data-index="3" aria-label="Digit 3">
                                            <input type="text" class="form-control otp-input text-center" maxlength="1" data-index="4" aria-label="Digit 4">
                                            <input type="text" class="form-control otp-input text-center" maxlength="1" data-index="5" aria-label="Digit 5">
                                        </div>
                                        <input type="hidden" name="otp" id="otpValue">
                                    </fieldset>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success py-3 fw-semibold rounded-pill">
                                            <i class="fa fa-check"></i> Verify OTP
                                        </button>
                                        <button type="button" class="btn btn-outline-primary py-2 rounded-pill" id="resendOtpBtn">
                                            <i class="fa fa-refresh"></i> Resend OTP
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary py-2 rounded-pill" id="backToForgetPasswordBtn">
                                            <i class="fa fa-arrow-left"></i> Back
                                        </button>
                                    </div>
                                </form>

                                <!-- OTP Timer -->
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        OTP Expires in <span id="otpTimer" class="fw-bold text-danger">10:00</span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Success Message Card -->
                        <div class="card border-0 rounded-3 shadow-sm" id="successCard" style="display: none;">
                            <div class="card-header py-3 border-0 bg-transparent">
                                <h3 class="h5 mb-0 text-center text-uppercase fw-bold text-success">
                                    <i class="fa fa-check-circle"></i> Email Sent
                                </h3>
                            </div>
                            <div class="card-body p-4 text-center">
                                <div class="alert alert-success">
                                    <i class="fa fa-envelope"></i>
                                    <strong>Check Your Email</strong><br>
                                    Password reset link has been sent to your email.
                                </div>
                                <div class="text-muted mb-4">
                                    Please check your email for the password reset link.
                                </div>
                                <a href="{{ route('public.login') }}" class="btn btn-primary py-3 fw-semibold rounded-pill">
                                    <i class="fa fa-arrow-left"></i> Back to Login
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('pages-scripts')
<script @cspNonce>
    let otpTimer;
    let resendTimer;
    let otpTimeRemaining = 600;
    let resendTimeRemaining = 0;
    const RESEND_COOLDOWN = 120;

    $(document).ready(function() {
        // Validate the forget password form
        $("#forgetPasswordForm").validate({
            errorClass: 'text-danger validation-error',
            errorElement: 'span',
            rules: {
                login_field: {
                    required: true,
                    minlength: 3
                },
                captcha: {
                    required: true
                }
            },
            messages: {
                login_field: {
                    required: "Email or mobile number is required",
                    minlength: "Please enter at least 3 characters"
                },
                captcha: {
                    required: "Captcha is required"
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                sendForgetPasswordOtp();
            }
        });

        // Validate the OTP verification form
        $("#otpVerificationForm").validate({
            rules: {
                otp: {
                    required: true,
                    minlength: 6,
                    maxlength: 6
                }
            },
            messages: {
                otp: {
                    required: "OTP is required",
                    minlength: "OTP must be 6 digits",
                    maxlength: "OTP must be 6 digits"
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                verifyForgetPasswordOtp();
            }
        });

        // OTP input handling
        $('.otp-input').on('input', function() {
            let value = $(this).val();
            let index = parseInt($(this).data('index'));

            value = value.replace(/[^0-9]/g, '');
            $(this).val(value);

            if (value) {
                $(this).addClass('filled');
                if (index < 5) {
                    $('.otp-input[data-index="' + (index + 1) + '"]').focus();
                }
            } else {
                $(this).removeClass('filled');
            }

            updateOtpValue();
        });

        $('.otp-input').on('keydown', function(e) {
            let index = parseInt($(this).data('index'));

            if (e.keyCode === 8 && !$(this).val() && index > 0) {
                $('.otp-input[data-index="' + (index - 1) + '"]').focus();
            }
        });

        $('#backToForgetPasswordBtn').click(function() {
            showForgetPasswordForm();
        });

        $('#resendOtpBtn').click(function() {
            if (resendTimeRemaining > 0) {
                Swal.fire({
                    icon: "info",
                    title: "Please wait",
                    text: `Resend OTP in ${resendTimeRemaining} seconds`,
                    timer: 2000
                });
                return;
            }
            resendForgetPasswordOtp();
        });

        $('#refresh-captcha').click(function() {
            refreshCaptcha();
        });

        $('input[name="login_field"]').on('input', function() {
            var value = $(this).val();
            var isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            var isMobile = /^[0-9]+$/.test(value);

            if (isEmail) {
                $(this).attr('placeholder', "Email");
            } else if (isMobile && value.length > 0) {
                $(this).attr('placeholder', "Mobile");
            } else {
                $(this).attr('placeholder', "Email or Mobile");
            }
        });
    });

    function sendForgetPasswordOtp() {
        $.ajax({
            url: "{{ route('public.login.forget-password.send-otp') }}",
            type: "POST",
            data: $("#forgetPasswordForm").serialize(),
            dataType: "json",
            beforeSend: function() {
                $('.site-loader').show();
            },
            success: function(response) {
                $('.site-loader').hide();

                if (response.status === "success") {
                    showOtpVerificationForm();
                    startResendCooldown();
                    Swal.fire({
                        icon: "success",
                        title: "OTP Sent",
                        text: response.message,
                        timer: 3000
                    });
                } else {
                    refreshCaptcha();
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                $('.site-loader').hide();
                refreshCaptcha();
                let errorMessage = "Something went wrong";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorMessage
                });
            }
        });
    }

    function verifyForgetPasswordOtp() {
        $.ajax({
            url: "{{ route('public.login.forget-password.verify-otp') }}",
            type: "POST",
            data: $("#otpVerificationForm").serialize(),
            dataType: "json",
            beforeSend: function() {
                $('.site-loader').show();
            },
            success: function(response) {
                $('.site-loader').hide();

                if (response.status === "success") {
                    showSuccessCard();
                    Swal.fire({
                        icon: "success",
                        title: "Email Sent",
                        text: response.message,
                        timer: 3000
                    });
                } else {
                    clearOtpInputs();
                    Swal.fire({
                        icon: "error",
                        title: "Verification Failed",
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                $('.site-loader').hide();
                clearOtpInputs();
                let errorMessage = "Something went wrong";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorMessage
                });
            }
        });
    }

    function resendForgetPasswordOtp() {
        $.ajax({
            url: "{{ route('public.login.forget-password.resend-otp') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            beforeSend: function() {
                $('.site-loader').show();
            },
            success: function(response) {
                $('.site-loader').hide();

                if (response.status === "success") {
                    clearOtpInputs();
                    resetOtpTimer();
                    startResendCooldown();
                    Swal.fire({
                        icon: "success",
                        title: "OTP Resent",
                        text: response.message,
                        timer: 3000,
                        confirmButtonText: "OK"
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "OTP Resend Failed",
                        text: response.message,
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function(xhr) {
                $('.site-loader').hide();
                let errorMessage = "Something went wrong";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorMessage,
                    confirmButtonText: "OK"
                });
            }
        });
    }

    function showOtpVerificationForm() {
        $('#forgetPasswordCard').hide();
        $('#otpVerificationCard').show();
        $('.otp-input:first').focus();
        startOtpTimer();
    }

    function showForgetPasswordForm() {
        $('#otpVerificationCard').hide();
        $('#successCard').hide();
        $('#forgetPasswordCard').show();
        clearOtpInputs();
        stopOtpTimer();
        stopResendTimer();
        $("#forgetPasswordForm")[0].reset();
        refreshCaptcha();
    }

    function showSuccessCard() {
        $('#otpVerificationCard').hide();
        $('#successCard').show();
        stopOtpTimer();
        stopResendTimer();
    }

    function updateOtpValue() {
        let otp = '';
        $('.otp-input').each(function() {
            otp += $(this).val();
        });
        $('#otpValue').val(otp);
    }

    function clearOtpInputs() {
        $('.otp-input').val('').removeClass('filled');
        $('#otpValue').val('');
    }

    function startOtpTimer() {
        otpTimeRemaining = 600;
        updateTimerDisplay();

        otpTimer = setInterval(function() {
            otpTimeRemaining--;
            updateTimerDisplay();

            if (otpTimeRemaining <= 0) {
                stopOtpTimer();
                Swal.fire({
                    icon: "warning",
                    title: "OTP Expired",
                    text: "Your OTP has expired. Please request a new one.",
                    confirmButtonText: "Resend OTP"
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (resendTimeRemaining > 0) {
                            Swal.fire({
                                icon: "info",
                                title: "Please wait",
                                text: `Resend OTP in ${resendTimeRemaining} seconds`,
                                timer: 2000
                            });
                        } else {
                            resendForgetPasswordOtp();
                        }
                    }
                });
            }
        }, 1000);
    }

    function stopOtpTimer() {
        if (otpTimer) {
            clearInterval(otpTimer);
        }
    }

    function resetOtpTimer() {
        stopOtpTimer();
        startOtpTimer();
    }

    function updateTimerDisplay() {
        let minutes = Math.floor(otpTimeRemaining / 60);
        let seconds = otpTimeRemaining % 60;
        $('#otpTimer').text(
            minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0')
        );
    }

    function startResendCooldown() {
        resendTimeRemaining = RESEND_COOLDOWN;
        updateResendButtonState();

        resendTimer = setInterval(function() {
            resendTimeRemaining--;
            updateResendButtonState();

            if (resendTimeRemaining <= 0) {
                stopResendTimer();
            }
        }, 1000);
    }

    function stopResendTimer() {
        if (resendTimer) {
            clearInterval(resendTimer);
        }
        resendTimeRemaining = 0;
        updateResendButtonState();
    }

    function updateResendButtonState() {
        const resendBtn = $('#resendOtpBtn');

        if (resendTimeRemaining > 0) {
            resendBtn.prop('disabled', true);
            resendBtn.html(`<i class="fa fa-clock"></i> Resend OTP (${resendTimeRemaining}s)`);
            resendBtn.removeClass('btn-outline-primary').addClass('btn-secondary');
        } else {
            resendBtn.prop('disabled', false);
            resendBtn.html(`<i class="fa fa-refresh"></i> Resend OTP`);
            resendBtn.removeClass('btn-secondary').addClass('btn-outline-primary');
        }
    }

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
                $('#captcha img').attr('src', data.captcha + '?' + Date.now());
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to refresh captcha"
                });
            }
        });
    }
</script>
@endsection