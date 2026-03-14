<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>{{ isset($siteSettings) ? $siteSettings->site_name : config('app.name') }}</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

    <!-- [Favicon] icon -->
    @if(isset($siteSettings) && $siteSettings->favicon)
    <link rel="icon" href="{{ isset($siteSettings) ? $siteSettings->favicon_full_path :'' }}" type="image/x-icon"> <!-- [Google Font] Family -->
    @endif
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dataTables.bootstrap5.min.css') }}">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{asset('assets/fonts/tabler-icons.min.css')}}">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{asset('assets/fonts/feather.css')}}">
    <!-- Toastr css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/toastr.min.css') }}">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome.css')}}">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{asset('assets/fonts/material.css')}}">
    <!-- Nestable css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/jquery.nestable.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <!-- Sweetalert 2 -->
    <link rel="stylesheet" href="{{asset('assets/css/plugins/sweetalert2.min.css')}}" />
    <!-- Ckeditor5 -->
    <link rel="stylesheet" href="{{asset('assets/plugins/ckeditor5/ckeditor5/ckeditor5.css')}}">
    <!-- File Input -->
    <link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/css/fileinput.min.css" media="all"
        rel="stylesheet" type="text/css" />
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-style.css') }}">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <div class="loader">
        <div class="loader-content">
            <h6>
                Processing, please wait...
            </h6>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="#" class="b-brand text-primary">
                    @if (isset($siteSettings))
                    <img src="{{ generate_file_view_path_for_backend(asset('storage' . Config::get('file_paths')['SITE_ADMIN_PANEL_LOGO_PATH'].'/' . $siteSettings->admin_panel_logo)) }}" class="img-fluid logo-lg navbrand-logo-img" alt="Logo" />
                    @else
                    <h5 class="mb-0 text-white text-center">
                        {{ config('app.name') }}
                    </h5>
                    @endif
                </a>
            </div>
            <div class="navbar-content">
                @include('includes.secure.sidebar')
            </div>
        </div>
    </nav>
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('includes.secure.header')
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            @yield('content')
        </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('includes.secure.footer')

    <!-- Required Js -->
    <script @cspNonce src="{{asset('assets/js/plugins/jquery.min.js')}}"></script>
    <script @cspNonce src="{{asset('assets/js/plugins/popper.min.js')}}"></script>
    <script @cspNonce src="{{asset('assets/js/plugins/simplebar.min.js')}}"></script>
    <script @cspNonce src="{{asset('assets/js/plugins/bootstrap.min.js')}}"></script>
    <script @cspNonce src="{{ asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script @cspNonce src="{{ asset('assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script @cspNonce src="{{ asset('assets/js/plugins/toastr.min.js') }}"></script>
    <script @cspNonce src="{{asset('assets/js/plugins/sweetalert2.min.js')}}"></script>
    <script @cspNonce src="{{asset('assets/js/plugins/jquery.validate.min.js')}}"></script>
    <script @cspNonce src="{{asset('assets/js/plugins/jquery.nestable.min.js')}}"></script>
    {{-- Select 2 --}}
    <script @cspNonce src="{{asset('plugins/select2/js/select2.min.js')}}"></script>

    <script @cspNonce src="{{asset('assets/js/fonts/custom-font.js')}}"></script>
    <script @cspNonce src="{{asset('assets/js/pcoded.js')}}"></script>
    {{-- Bootstrap File Input --}}
    <script @cspNonce src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/plugins/piexif.min.js"
        type="text/javascript"></script>
    <script @cspNonce src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/plugins/sortable.min.js"
        type="text/javascript"></script>
    <script @cspNonce src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/fileinput.min.js"></script>
    <script @cspNonce src="{{asset('assets/js/plugins/feather.min.js')}}"></script>
    <script @cspNonce src="{{asset('assets/js/plugins/crypto-js.min.js')}}"></script>

    <!-- Ckeditor -->
    <script @cspNonce>
        const importMap = {
            imports: {
                "ckeditor5": "{{ asset('assets/plugins/ckeditor5/ckeditor5/ckeditor5.js') }}",
                "ckeditor5/": "{{ asset('assets/plugins/ckeditor5/ckeditor5/') }}"
            }
        };

        document.write(`<script @cspNonce type="importmap">${JSON.stringify(importMap)}<\/script>`);
    </script>

    <script @cspNonce type="module" src="{{ asset('assets/plugins/ckeditor5/ckeditor_config.js') }}?v={{ filemtime(public_path('assets/plugins/ckeditor5/ckeditor_config.js')) }}"></script>
    <script @cspNonce src="{{asset('assets/js/custom-scripts.js')}}"></script>

    <!-- Autologout -->
    <script @cspNonce>
        let inactivityTimeout = "{{ env('SESSION_INACTIVITY_TIMEOUT') }}";
        let idleTime = 0;
        let maxIdleTime = inactivityTimeout * 60; // 20 minutes in seconds
        let sessionInterval;
        let timerInterval;

        function resetIdleTime() {
            idleTime = 0;
        }

        function startIdleTimer() {
            // Increment the idle time counter every second.
            sessionInterval = setInterval(() => {
                idleTime++;

                // Update the countdown display
                updateSessionTimer(maxIdleTime - idleTime);
            }, 1000);
        }

        function updateSessionTimer(secondsLeft) {
            if (secondsLeft < 0) secondsLeft = 0;
            let minutes = Math.floor(secondsLeft / 60);
            let seconds = secondsLeft % 60;
            $("#session-timer").text(`${pad(minutes)}:${pad(seconds)}`);
        }

        function pad(num) {
            return num.toString().padStart(2, "0");
        }

        function pingSession() {
            // Pings backend every minute to check last_activity
            setInterval(() => {
                $.ajax({
                    url: "{{ route('session.ping') }}", // Create this route
                    type: "POST",
                    global: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.logout === true) {
                            toastr.error(response.message, null, {
                                timeOut: 10000
                            });

                            clearInterval(sessionInterval);
                            clearInterval(timerInterval);
                            window.location.href = response.redirect_url; // Redirect to login or home page
                        }
                    },
                });
            }, 60000); // 1 minute
        }

        $(document).ready(function() {
            resetIdleTime();
            startIdleTimer();
            pingSession();
        });

        $('.select2').select2({
            placeholder: "Select",
            allowClear: true
        });

        // Featured Image Upload Start ==========================================
        var featuredImageOptions = {
            showUpload: false,
            previewFileType: "image",
            browseLabel: "Pick Image",
            browseIcon: "<i class=\"bi-file-image\"></i> ",
            allowedFileExtensions: ['jpg', 'jpeg', 'png'],
            allowedFileTypes: ["image"],
            maxFilePreviewSize: 5120,
            showCancel: false
        }

        if ($('.initial_preview').val()) {
            featuredImageOptions.initialPreview = [
                // IMAGE RAW MARKUP
                '<img src="' + $('.initial_preview').val() + '" class="kv-preview-data file-preview-image">'
            ];
        }

        $(".featured_image_upload").fileinput(featuredImageOptions);
        // Featured Image Upload End ==========================================

        $(".multiple_image_upload").fileinput({
            showUpload: false,
            previewFileType: "image",
            browseLabel: "Pick Images",
            browseIcon: "<i class=\"bi-file-image\"></i> ",
            allowedFileExtensions: ['jpg', 'jpeg', 'png'],
            allowedFileTypes: ["image"],
            maxFilePreviewSize: 5120,
            maxFileCount: 10,
            overwriteInitial: false,
            showCancel: false
        });
    </script>

    <script @cspNonce>
        layout_change('dark');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-2");
        font_change("Public-Sans");
    </script>

    <script @cspNonce>
        $(document).ready(function() {
            // Show loader on any AJAX start
            $(document).ajaxStart(function() {
                $('.loader').show();
            });

            // Hide loader on all AJAX complete
            $(document).ajaxStop(function() {
                $('.loader').hide();
            });

            $(document).on('click', '.btn-go-back', function() {
                window.history.back();
            })
        });
    </script>
    @yield('pages-scripts')
</body>
<!-- [Body] end -->

</html>