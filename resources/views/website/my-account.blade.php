@extends('layouts.website_layout')

@section('content')
@include('components.website.page-header')
<section class="my-account-section py-sm-5 py-4">
    <div class="container">

        <div class="my-account-product-list-col mt-4">
            <div class="row">
                <div class="col-lg-6 px-lg-5 px-3">
                    <div class="ma-login-form-col px-lg-3 px-0">
                        <h5 class="text-uppercase">Customer Login</h5>
                        <small class="text-muted mb-3 d-block">I am already a customer</small>
                        <input type="hidden" id="page" value="MY_ACCOUNT">
                        <form action="" class="login_form" id="login_form">
                            @csrf

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control rounded-4" id="floatingInput"
                                    placeholder="name@example.com" name="email">
                                <label for="floatingInput">Email Id</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control rounded-4" id="floatingPassword"
                                    placeholder="Password" name="password">
                                <label for="floatingPassword">Password</label>
                            </div>
                            <div class="mb-3 text-end">
                                <a href="" class="fw-bold text-custom">Forget Password?</a>
                            </div>
                            <div class="mb-3 text-center">
                                <button type="submit" class="btn btn-purple" id="btn_login_submit"><i class="fas fa-sign-in-alt"></i> Login</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6 px-lg-5 px-3">
                    <div class="ma-register-form-col px-lg-3 px-0">
                        <h5 class="text-uppercase">Customer Registration</h5>
                        <small class="text-muted mb-3 d-block">Register here to shop faster and keep track of all your orders.</small>
                        <form action="" class="ma_register_form" id="ma_register_form">
                            @csrf

                            <div class="form-floating mb-3">
                                <input type="name" class="form-control rounded-4" id="name"
                                    placeholder="Full Name" name="name">
                                <label for="name">Full Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control rounded-4" id="email_id"
                                    placeholder="name@example.com" name="email_id">
                                <label for="email_id">Email Id</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-4" id="phone_number"
                                    placeholder="Phone Number" name="phone_number" maxlength="10" minlength="10">
                                <label for="phone_number">Phone Number</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control rounded-4" id="password"
                                    placeholder="Password" name="password">
                                <label for="password">Password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control rounded-4" id="retype_password"
                                    placeholder="Re-type Password" name="retype_password">
                                <label for="retype_password">Re-type Password</label>
                            </div>
                            <div class="mb-3 text-center">
                                <button type="submit" class="btn btn-purple" id="ma-cr-submit-btn"><i class="fas fa-user-plus"></i> Register</button>
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
    $("#ma_register_form").validate({
        errorClass: 'text-danger validation-error',
        errorElement: 'span',
        rules: {
            name: {
                required: true
            },
            phone_number: {
                required: true,
                maxlength: 10,
                minlength: 10
            },
            email_id: {
                required: true
            },
            password: {
                required: true
            },
            retype_password: {
                required: true
            }
        },
        submitHandler: function(form, event) {
            event.preventDefault();
            var formData = new FormData(document.getElementById("ma_register_form"));
            console.log('ello')
            $.ajax({
                url: base_url + "/user/register",
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                beforeSend: function() {

                },
                complete: function() {

                },
                success: function(response) {
                    if (response.status == true) {
                        $.confirm({
                            title: 'Success',
                            content: response.message,
                            type: 'green',
                            typeAnimated: true,
                            buttons: {
                                Ok: function() {
                                    window.location.href = response.redirect_to
                                }
                            }
                        });
                    } else if (response.status == 'validation_error') {
                        $.dialog({
                            title: 'Validation Error',
                            content: response.message,
                            type: 'red'
                        });
                    } else if (response.status == false) {
                        $.dialog({
                            title: 'Error',
                            content: response.message,
                            type: 'red'
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                }
            });
        }
    });
</script>
@endsection