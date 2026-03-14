<!DOCTYPE html>

<html lang="en">
<!-- [Head] start -->

<head>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <title>Login | {{isset($siteSettings) ? $siteSettings->site_name : config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-language" content="en">
    <meta name="language" content="en">

    <!-- [Favicon] icon -->
    @if(isset($siteSettings) && $siteSettings->favicon)
    <link rel="icon" href="{{ asset('storage' . Config::get('file_paths')['SITE_FAVICON_PATH'].'/' . $siteSettings->favicon) }}" type="image/x-icon"> <!-- [Google Font] Family -->
    @endif

    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{asset('assets/fonts/tabler-icons.min.css')}}">
    <!-- Toastr css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/toastr.min.css') }}">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{asset('assets/fonts/feather.css')}}">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome.css')}}">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{asset('assets/fonts/material.css')}}">
    <!-- Sweetalert 2 -->
    <link rel="stylesheet" href="{{asset('assets/css/plugins/sweetalert2.min.css')}}" />
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-style.css') }}">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader">
        <div class="loader-content">
            <h6>
                Processing, please wait...
            </h6>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    @yield('content')

    <!-- [ Main Content ] end -->
    <!-- Required Js -->
    <script src="{{asset('assets/js/plugins/jquery.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/popper.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/crypto-js.min.js')}}"></script>
    <script src="{{asset('assets/js/fonts/custom-font.js')}}"></script>
    <script src="{{ asset('assets/js/plugins/toastr.min.js') }}"></script>
    <script src="{{asset('assets/js/plugins/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/pcoded.js')}}"></script>
    <script src="{{asset('assets/js/plugins/feather.min.js')}}"></script>

    <script src="{{asset('assets/js/custom-scripts.js')}}"></script>

    <script @cspNonce>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-2");
        font_change("Public-Sans");
    </script>

    @yield('pages-scripts')

</body>
<!-- [Body] end -->

</html>