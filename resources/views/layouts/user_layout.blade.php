<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $pageTitle ? $pageTitle.' | ' : '' }} {{ $siteSettings->site_name }}</title>
    <meta
        name="description"
        content="" />

    <!-- Favicon -->
    @if($siteSettings->favicon)
    <link rel="icon" href="{{ asset('storage' . Config::get('file_paths')['SITE_FAVICON_PATH'].'/' . $siteSettings->favicon) }}" type="image/x-icon"> <!-- [Google Font] Family -->
    @endif
    <!-- Bootstrap CSS -->
    <link href="{{ asset('/user_assets/css/plugins/bootstrap.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <!-- Toastr -->
    <link href="{{ asset('/user_assets/css/plugins/toastr.min.css') }}" rel="stylesheet" />

    <!-- Sweetalert 2 -->
    <link rel="stylesheet" href="{{asset('user_assets/css/plugins/sweetalert2.min.css')}}" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('/user_assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('/user_assets/css/material.css') }}" />
    <link rel="stylesheet" href="{{ asset('/user_assets/css/accessibility.css') }}" />

</head>

<body class="font-size-md">
    <!-- Skip to main content link for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Sidebar -->
    @include('includes.user.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        @include('includes.user.navbar')

        <!-- Main Content -->
        <div class="container-fluid py-4" id="main-content">
            <div class="tab-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script @cspNonce src="{{ asset('user_assets/js/plugins/jquery.min.js') }}"></script>
    <script @cspNonce src="{{ asset('user_assets/js/plugins/bootstrap.min.js') }}"></script>
    <!-- Jquery Validator -->
    <script @cspNonce src="{{ asset('user_assets/js/plugins/jquery.validate.min.js') }}"></script>
    <!-- Toastr -->
    <script @cspNonce src="{{ asset('user_assets/js/plugins/toastr.min.js') }}"></script>
    <!-- Crypto -->
    <script @cspNonce src="{{asset('assets/js/plugins/crypto-js.min.js')}}"></script>
    <!-- Sweetalert2 -->
    <script @cspNonce src="{{asset('user_assets/js/plugins/sweetalert2.min.js')}}"></script>

    <!-- Custom JS -->
    <script @cspNonce src="{{asset('user_assets/js/main.js')}}"></script>
    <script @cspNonce src="{{asset('user_assets/js/material.js')}}"></script>
    <script @cspNonce src="{{asset('user_assets/js/accessibility.js')}}"></script>
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
    </script>

    <script @cspNonce>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>

    @yield('pages-scripts')

</body>

</html>