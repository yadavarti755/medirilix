@extends('layouts.website_layout')

@section('content')
@include('components.website.page-header')
<section class="section">
    <div class="container">

        @if($contactDetails->count() > 0)
        <div class="row align-items-stretch justify-content-center">
            @foreach($contactDetails as $detail)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body text-center p-4">
                        <div class="mb-3 text-custom">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">Address</h5>
                        <p class="card-text text-muted">
                            {!! nl2br(e($detail->address)) !!}
                        </p>

                        @if($detail->phone_numbers)
                        <hr class="my-3">
                        <div class="mb-2">
                            <i class="fas fa-phone-alt text-custom me-2"></i>
                            <span class="fw-bold">Phone:</span>
                            <br>
                            @foreach(explode(',', $detail->phone_numbers) as $phone)
                            <a href="tel:{{ trim($phone) }}" class="text-decoration-none text-dark">{{ trim($phone) }}</a><br>
                            @endforeach
                        </div>
                        @endif

                        @if($detail->email_ids)
                        <div class="mb-2">
                            <i class="fas fa-envelope text-custom me-2"></i>
                            <span class="fw-bold">Email:</span>
                            <br>
                            @foreach(explode(',', $detail->email_ids) as $email)
                            <a href="mailto:{{ trim($email) }}" class="text-decoration-none text-dark">{{ trim($email) }}</a><br>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="row">
            <div class="col-12 text-center">
                <p class="alert alert-warning">No contact details available at the moment.</p>
            </div>
        </div>
        @endif

        <div class="row mt-5">
            <div class="col-md-8 offset-md-2">
                <div class="card border-0 shadow rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-lg-5 bg-custom text-white p-5 d-flex flex-column justify-content-center align-items-center text-center">
                                <h3 class="fw-bold mb-4">Get in Touch</h3>
                                <p class="mb-4">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                                <div class="display-1"><i class="fas fa-envelope-open-text"></i></div>
                            </div>
                            <div class="col-lg-7 p-5">
                                <h4 class="fw-bold mb-4">Send Message</h4>
                                <form class="" id="contact_us_form">
                                    @csrf

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control rounded-3" name="name" id="floatingName" placeholder="Name">
                                        <label for="floatingName">Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control rounded-3" name="email_id" id="floatingEmail" placeholder="Email Id">
                                        <label for="floatingEmail">Email Id <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control rounded-3" name="phone_number" id="floatingPhoneNumber" placeholder="Phone Number" maxlength="10">
                                        <label for="floatingPhoneNumber">Phone Number <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control rounded-3" name="message" id="floatingMessage" placeholder="Message" style="height: 150px;"></textarea>
                                        <label for="floatingMessage">Message <span class="text-danger">*</span></label>
                                    </div>

                                    <div class="form-group mt-4 mb-4">
                                        <div class="captcha d-flex gap-2 align-items-center">
                                            <img src="{{ captcha_src() }}" alt="CAPTCHA image" class="rounded border" style="height: 40px;" id="captcha-img">
                                            <button type="button" class="btn btn-dark rounded-3 reload" id="reload" aria-label="Refresh Captcha">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-floating mb-4">
                                        <input id="captcha" type="text" class="form-control rounded-3" placeholder="Enter Captcha" name="captcha">
                                        <label for="captcha">Enter Captcha <span class="text-danger">*</span></label>
                                    </div>

                                    <div class="d-grid">
                                        <button class="btn btn-custom w-100 py-3 rounded-3" type="submit">
                                            <i class="fas fa-paper-plane me-2"></i> Submit Query
                                        </button>
                                    </div>
                                </form>
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
    // Contact Us ===============================================
    // On submitting the form
    $('#contact_us_form').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(document.getElementById('contact_us_form'));

        // Send Ajax Request
        $.ajax({
            url: base_url + '/contact-us/submit',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                // Optional: Show loader if available
                $('.loader').show();
            },
            success: function(response) {
                // Optional: Hide loader
                $('.loader').hide();

                if (response.success == true) {
                    $.confirm({
                        title: 'Success',
                        type: 'green',
                        content: response.message,
                        buttons: {
                            confirm: function() {
                                window.location.reload();
                            }
                        }
                    });
                } else {
                    toastr.error(response.message);
                    refreshCaptcha(); // Reload captcha on logic error
                }
            },
            error: function(xhr, status, error) {
                // Optional: Hide loader
                $('.loader').hide();
                refreshCaptcha(); // Reload captcha on server/validation error

                if (xhr.status === 422) {
                    var response = xhr.responseJSON;
                    var errorString = '<ul class="list-unstyled">';
                    $.each(response.errors, function(key, value) {
                        // value is an array of messages
                        $.each(value, function(index, msg) {
                            errorString += '<li>' + msg + '</li>';
                        });
                    });
                    errorString += '</ul>';

                    $.dialog({
                        title: 'Validation Error',
                        content: errorString,
                        type: 'red'
                    });
                } else {
                    toastr.error('Something went wrong. Please try again.');
                }
            }
        });
    });

    // Refresh captcha on button click
    $('#reload').click(function() {
        refreshCaptcha();
    });

    function refreshCaptcha() {
        const formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: "{{ route('captcha.refresh') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(data) {
                $('#captcha-img').attr('src', data.captcha);
            },
            error: function() {
                toastr.error('Failed to refresh captcha.');
            }
        });
    }
</script>
@endsection