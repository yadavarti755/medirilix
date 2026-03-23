@extends('layouts.website_layout')

@section('content')
@include('components.website.page-header')
<section class="my-account-section py-sm-5 py-4">
    <div class="container">

        <div class="my-account-product-list-col">
            <div class="row">
                <div class="offset-lg-3 col-lg-6 px-lg-5 px-3">
                    <div class="card card-body shadow-sm border-0">
                        <div class="ma-register-form-col px-lg-3 px-0">
                            <small class="text-muted mb-3 d-block text-center">Register with us for a faster checkout,
                                to track the status of your order and more.</small>
                            <form action="" class="ma_register_form" id="ma_register_form">
                                @csrf
                                @if(isset($type) && !empty($type))
                                <input type="hidden" name="type" value="{{customUrlEncode($type)}}">
                                @endif
                                <div class="form-floating mb-3">
                                    <input type="name" class="form-control rounded-4" id="name" placeholder="Full Name"
                                        name="name">
                                    <label for="name">Full Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control rounded-4" id="email_id"
                                        placeholder="name@example.com" name="email_id">
                                    <label for="email_id">Email Id</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control rounded-4" id="phone_number" placeholder="Phone Number (Optional)">
                                    <!-- <label for="phone_number">Phone Number</label> -->

                                    <!-- Hidden field that will store FULL international number -->
                                    <input type="hidden" name="phone_number">
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
                                    <button type="submit" class="btn btn-custom" id="ma-cr-submit-btn"><i
                                            class="fas fa-user-plus"></i> Register</button>
                                </div>
                                <div class="mb-3 text-center">
                                    <p>OR</p>
                                    <a href="{{ route('auth.google') }}" class="btn btn-outline-danger w-100 rounded-4 py-2">
                                        <i class="fab fa-google me-2"></i> Sign up with Google
                                    </a>
                                </div>
                                <div class="mb-3 text-start">
                                    <a href="{{route('public.login')}}" class="text-dark">Already have an account? <span
                                            class="text-custom">Login Here</span></a>
                                </div>
                            </form>
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
    $("#ma_register_form").validate({
        errorClass: 'text-danger validation-error',
        errorElement: 'span',

        rules: {
            name: {
                required: true
            },
            phone_number: {
                required: false
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

            $.ajax({
                url: "{{ route('public.register.store') }}",
                type: "POST",
                processData: false,
                contentType: false,
                data: formData,

                success: function(response) {
                    if (response.success || response.status === true) {

                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = response.redirect_to;
                        });

                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error"
                        });
                    }
                },

                error: function(xhr) {
                    // Laravel validation errors (422)
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.message;
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


    const input = document.querySelector("#phone_number");
    const hiddenInput = document.querySelector("input[name='phone_number']");

    const iti = window.intlTelInput(input, {
        separateDialCode: true,
        initialCountry: "auto",
        // utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js",
        // geoIpLookup: callback => {
        //     fetch("https://ipapi.co/json")
        //         .then(res => res.json())
        //         .then(data => callback(data.country_code))
        //         .catch(() => callback("IN"));
        // }
    });

    input.addEventListener("blur", function() {
        hiddenInput.value = iti.getNumber(); // Example: +919876543210
    });
</script>
@endsection