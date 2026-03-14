@extends('layouts.website_layout')

@section('content')
@include('components.website.page-header')
<section class="my-account-section py-5">
    <div class="container">
        <div class="my-account-product-list-col mt-3">
            <div class="row">
                <div class="offset-lg-3 col-lg-6 px-lg-5 px-3">
                    <div class="ma-login-form-col">
                        <small class="text-muted mb-2 d-block">Please verify your email id and phone number. Please check your email inbox of {{session()->get('user')['email']}}.</small>
                        <form action="" class="verification_form" id="verification_form">
                            @csrf

                            <input type="hidden" name="email_id" value="{{ session()->get('user')['email'] }}">
                            <div class="mb-3">
                                <label for="verification_code" class="mb-2">Verification Code <span class="text-danger">*</span></label>
                                <input type="text" name="verification_code" id="verification_code" class="form-control rounded-0" maxlength="10">
                                <p class="mb-0 mt-2 text-end fw-bold resend-otp-wrapper" style="display: none;">
                                    <span id="show-time" class="badge bg-dark rounded-pill me-2"></span>
                                    <a href="Javascript:void(0)" class="text-muted" disabled id="btn-resend-otp"><i class="fas fa-sync"></i> Resend OTP</a>
                                </p>
                            </div>
                            <div class="mb-3 text-center">
                                <button type="submit" class="btn btn-purple"><i class="fas fa-check"></i> Verify</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $("#verification_form").validate({
        errorClass: 'text-danger',
        rules: {
            verification_code: {
                required: true
            }
        },
        submitHandler: function(form, event) {
            event.preventDefault();

            var formData = new FormData(document.getElementById("verification_form"));

            $.ajax({
                url: "{{ route('public.register.verify-user') }}",
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {

                    // SUCCESS
                    if (response.status === true) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = response.redirect_to;
                        });

                        // VALIDATION ERROR
                    } else if (response.status === 'validation_error') {
                        Swal.fire({
                            title: 'Validation Error',
                            text: response.message,
                            icon: 'warning'
                        });

                        // ERROR
                    } else if (response.status === false) {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error'
                        });

                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error'
                        });
                    }
                }
            });
        }
    });


    // TIMER CODE
    var timer = 120;

    function showTimer() {
        $('.resend-otp-wrapper').show();
        $('#show-time').text('Wait ' + timer + 's');
        $('#show-time').show();
        $('#btn-resend-otp').prop('disabled', true).addClass('text-muted');

        var timerInterval = setInterval(() => {
            timer--;
            $('#show-time').text('Wait ' + timer + 's');
            $('#btn-resend-otp').addClass('text-muted');

            if (timer == 0) {
                $('#show-time').hide();
                $('#btn-resend-otp').prop('disabled', false)
                    .removeClass('text-muted')
                    .addClass('text-custom');

                clearInterval(timerInterval);
            }
        }, 1000);
    }

    $(document).ready(function() {
        showTimer();
    });

    $('#btn-resend-otp').on('click', function() {
        if (timer < 1) {
            $.ajax({
                url: "{{ route('public.register.resend-otp') }}",
                type: 'POST',
                data: {
                    email_id: $('#email_id').val(),
                    _token: $('meta[name=csrf-token]').attr('content')
                },
                success: function(response) {
                    if (response.status === true) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            timer = 120;
                            showTimer();
                        });

                    } else if (response.status === 'validation_error') {
                        Swal.fire({
                            title: 'Validation Error',
                            text: response.message,
                            icon: 'warning'
                        });

                    } else if (response.status === false) {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error'
                        });

                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error'
                        });
                    }
                }
            });
        }
    });
</script>
@endsection