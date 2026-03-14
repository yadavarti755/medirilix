@extends('layouts.auth_layout')

@section('content')
<div class="auth-main">
    <div class="auth-wrapper v3">
        <div class="auth-form">
            <div class="card mb-5" id="loginCard">
                <div class="card-body">
                    @if (isset($siteSettings->admin_panel_logo))
                    <div class="text-center">
                        <img src="{{ asset('storage' . Config::get('file_paths')['SITE_ADMIN_PANEL_LOGO_PATH'].'/' . $siteSettings->admin_panel_logo) }}" alt="{{ $siteSettings->site_name }}" class="w-75">
                    </div>
                    @else
                    <div class="text-center">
                        <h4>
                            {{ config('app.name') }}
                        </h4>
                    </div>
                    @endif
                    <hr>
                    <div class="mb-3">
                        <h1 class="mb-0 auth-login-title"><b>Login Here</b></h1>
                        <p class="text-secondary">
                            Enter your credentials to login
                        </p>
                    </div>
                    <form action="" id="loginForm" autocomplete="off">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label" for="input-email">Email Address or Mobile Number</label>
                            <input type="text" id="input-email" name="login_field" class="form-control" placeholder="Enter Email or Mobile Number">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="input-password">Password</label>
                            <input type="password" id="input-password" name="password" class="form-control" placeholder="Password">
                        </div>
                        <div class="form-group mb-3">
                            <div>
                                <div class="captcha mb-2" id="captcha">
                                    <img src="{{ captcha_src() }}" alt="CAPTCHA image to verify you're human">
                                    <button type="button" id="refresh-captcha" class="btn btn-secondary" aria-label="Refresh captcha"><i class="fa fa-sync" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <label for="input-captcha" class="form-label">Captcha</label>
                            <input type="text" id="input-captcha" class="form-control" name="captcha" placeholder="Enter Captcha">
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-semibold">
                                <i class="fa fa-paper-plane"></i> Login
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        // Validate the login form
        $("#loginForm").validate({
            rules: {
                login_field: {
                    required: true,
                    minlength: 3
                },
                password: {
                    required: true,
                },
                captcha: {
                    required: true,
                }
            },
            messages: {
                login_field: {
                    required: "Please enter your email address or mobile number",
                    minlength: "Please enter at least 3 characters"
                },
                password: {
                    required: "Please enter your password"
                },
                captcha: {
                    required: "Please enter the captcha code"
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                performLogin();
            }
        });

        // Refresh captcha on button click
        $('#refresh-captcha').click(function() {
            refreshCaptcha();
        });

        // Dynamic placeholder for login field
        $('input[name="login_field"]').on('input', function() {
            var value = $(this).val();
            var isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            var isMobile = /^[0-9]+$/.test(value);

            if (isEmail) {
                $(this).attr('placeholder', 'Enter Email Address');
            } else if (isMobile && value.length > 0) {
                $(this).attr('placeholder', 'Enter Mobile Number');
            } else {
                $(this).attr('placeholder', 'Enter Email or Mobile Number');
            }
        });
    });

    var encryptionKey = "{{ session()->get('encryption_key') }}";

    function performLogin() {
        const formData = new FormData(document.getElementById('loginForm'));
        const password = encryptPassword(formData.get('password'), encryptionKey);
        formData.set('password', password);

        $.ajax({
            url: "{{ route('login.check') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $('.loader').show();
            },
            success: function(response) {
                $('.loader').hide();

                let key = response.key;
                encryptionKey = key;

                if (response.status === "success") {
                    toastr.success(response.message)
                    window.location.href = response.redirect;
                } else {
                    refreshCaptcha();
                    Swal.fire({
                        icon: "error",
                        title: "Login Failed",
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                $('.loader').hide();
                refreshCaptcha();
                let errorMessage = "An error occurred. Please try again.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorMessage
                });

                let key = xhr.responseJSON.key;
                encryptionKey = key;
            }
        });
    }

    function refreshCaptcha() {
        const formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('login_type', 'EMPLOYEE');
        $.ajax({
            url: "{{ route('captcha.refresh') }}",
            type: "POST",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.loader').show();
            },
            complete: function() {
                $('.loader').hide();
            },
            success: function(data) {
                $('#captcha img').attr('src', data.captcha + '?' + Date.now());

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