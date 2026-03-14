@extends('layouts.app_layout')

@section('content')
<x-page-header title="{{ $pageTitle }}" :backButton="true" />

<form id="site-settings-form" enctype="multipart/form-data">
    @csrf

    <!-- General Information & SEO Row -->
    <div class="row">
        <!-- General Information Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">General Information</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="form-label" for="site_name">Site Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="{{ $settings->site_name ?? '' }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="site_tag_line">Site Tag Line</label>
                        <input type="text" class="form-control" id="site_tag_line" name="site_tag_line" value="{{ $settings->site_tag_line ?? '' }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="currency_id">Currency</label>
                        <select class="form-control" id="currency_id" name="currency_id">
                            <option value="">Select Currency</option>
                            @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ (isset($settings->currency_id) && $settings->currency_id == $currency->id) ? 'selected' : '' }}>
                                {{ $currency->currency }} ({{ $currency->symbol }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label" for="footer_about_us">Footer About Us</label>
                        <textarea class="form-control" id="footer_about_us" name="footer_about_us" rows="4">{{ $settings->footer_about_us ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">SEO Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="form-label" for="seo_keywords">SEO Keywords</label>
                        <textarea class="form-control" id="seo_keywords" name="seo_keywords" rows="3">{{ $settings->seo_keywords ?? '' }}</textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label" for="seo_description">SEO Description</label>
                        <textarea class="form-control" id="seo_description" name="seo_description" rows="5">{{ $settings->seo_description ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Branding & Logos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Branding & Logos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 text-center mb-3">
                            <label class="form-label fw-bold d-block" for="header_logo">Header Logo</label>
                            <div class="mb-2">
                                @if($settings->header_logo)
                                <img src="{{ $settings->header_logo_full_path }}" alt="Header Logo" class="img-thumbnail" style="height: 100px; object-fit: contain;" />
                                @else
                                <div class="text-muted p-4 border bg-light">No Image</div>
                                @endif
                            </div>
                            <input type="file" class="form-control form-control-sm" id="header_logo" name="header_logo" accept=".png, .jpg, .jpeg" />
                        </div>

                        <div class="col-md-3 col-sm-6 text-center mb-3">
                            <label class="form-label fw-bold d-block" for="footer_logo">Footer Logo</label>
                            <div class="mb-2">
                                @if($settings->footer_logo)
                                <img src="{{ $settings->footer_logo_full_path }}" alt="Footer Logo" class="img-thumbnail" style="height: 100px; object-fit: contain;" />
                                @else
                                <div class="text-muted p-4 border bg-light">No Image</div>
                                @endif
                            </div>
                            <input type="file" class="form-control form-control-sm" id="footer_logo" name="footer_logo" accept=".png, .jpg, .jpeg" />
                        </div>

                        <div class="col-md-3 col-sm-6 text-center mb-3">
                            <label class="form-label fw-bold d-block" for="favicon">Favicon</label>
                            <div class="mb-2">
                                @if($settings->favicon)
                                <img src="{{ $settings->favicon_full_path }}" alt="Favicon" class="img-thumbnail" style="height: 100px; object-fit: contain;" />
                                @else
                                <div class="text-muted p-4 border bg-light">No Image</div>
                                @endif
                            </div>
                            <input type="file" class="form-control form-control-sm" id="favicon" name="favicon" accept=".png, .jpg, .jpeg" />
                        </div>

                        <div class="col-md-3 col-sm-6 text-center mb-3">
                            <label class="form-label fw-bold d-block" for="admin_panel_logo">Admin Panel Logo</label>
                            <div class="mb-2">
                                @if($settings->admin_panel_logo)
                                <img src="{{ $settings->admin_panel_logo_full_path }}" alt="Admin Logo" class="img-thumbnail" style="height: 100px; object-fit: contain;" />
                                @else
                                <div class="text-muted p-4 border bg-light">No Image</div>
                                @endif
                            </div>
                            <input type="file" class="form-control form-control-sm" id="admin_panel_logo" name="admin_panel_logo" accept=".png, .jpg, .jpeg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer & Credits -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Footer Credits</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label class="form-label" for="copyright_text">Copyright Text</label>
                            <input type="text" class="form-control" id="copyright_text" name="copyright_text" value="{{ $settings->copyright_text ?? '' }}">
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label class="form-label" for="maintained_by_text">Maintained By Text</label>
                            <input type="text" class="form-control" id="maintained_by_text" name="maintained_by_text" value="{{ $settings->maintained_by_text ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> Save Settings
            </button>
        </div>
    </div>

</form>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        $('#site-settings-form').validate({
            rules: {
                site_name: {
                    required: true,
                    minlength: 3
                },
                site_tag_line: {
                    required: false,
                    minlength: 3
                },
                seo_keywords: {
                    required: false
                },
                seo_description: {
                    required: false
                }
            },
            messages: {
                site_name: {
                    required: "Please enter the site name.",
                    minlength: "The site name must be at least 3 characters long."
                },
                site_tag_line: {
                    minlength: "The site tag line must be at least 3 characters long."
                },
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $.ajax({
                    url: "{{ route('site-settings.update', $settings->id) }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            toastr.error(response.message);
                        }
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
                    }
                });
            }
        });
    });
</script>
@endsection