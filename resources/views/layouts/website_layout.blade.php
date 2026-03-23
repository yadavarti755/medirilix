<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ (isset($pageTitle) && !empty($pageTitle)) ? $pageTitle . ' | ':'' }} {{ $siteSettings->site_name }}</title>

  @if (isset($metaKeywords) && !empty($metaKeywords))
  <meta
    name="keywords"
    content="{{ $metaKeywords }}" />
  @elseif( isset($siteSettings->seo_keywords) && !empty($siteSettings->seo_keywords))
  <meta
    name="keywords"
    content="{{ $siteSettings->seo_keywords }}" />
  @else
  <meta
    name="keywords"
    content="Ecommerce website, sale, products, best products, india" />
  @endif

  @if (isset($metaDescription) && !empty($metaDescription))
  <meta
    name="description"
    content="{{ $metaDescription }}" />
  @elseif( isset($siteSettings->seo_description) && !empty($siteSettings->seo_description))
  <meta
    name="description"
    content="{{ $siteSettings->seo_description }}" />
  @else
  <meta
    name="description"
    content="Ecommerce website, sale, products, best products, india" />
  @endif
  <!-- CSRF Token for AJAX -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicon -->
  @if($siteSettings->favicon)
  <link rel="icon" href="{{ asset('storage' . Config::get('file_paths')['SITE_FAVICON_PATH'].'/' . $siteSettings->favicon) }}" type="image/x-icon"> <!-- [Google Font] Family -->
  @endif

  {{-- Facebook OG Meta tags --}}
  <meta property="og:title"
    content="@if(isset($pageTitle)) {{$pageTitle.' - '.config('app.name')}} @else Artificial Gehna @endif" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{url()->current()}}" />
  <meta property="og:image"
    content="@if(isset($product_details->featured_image)) {{ asset('storage/product_images/'.$product_details->featured_image) }} @else {{asset('website_assets/images/ag-logo.png')}} @endif" />
  <meta property="og:site_name" content="Artificial Gehna" />
  <meta property="og:description"
    content="We're a company based in New Delhi trying to bring you the latest fashion of the best quality at the lowest possible prices.">

  <!-- Twitter Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta property="twitter:domain" content="artificialgehna.com">
  <meta property="twitter:url" content="{{url()->current()}}">
  <meta name="twitter:title"
    content="@if(isset($pageTitle)) {{$pageTitle.' - '.config('app.name')}} @else Artificial Gehna @endif">
  <meta name="twitter:description"
    content="We're a company based in New Delhi trying to bring you the latest fashion of the best quality at the lowest possible prices.">
  <meta name="twitter:image"
    content="@if(isset($product_details->featured_image)) {{ asset('storage/product_images/'.$product_details->featured_image) }} @else {{asset('website_assets/images/ag-logo.png')}} @endif">

  {{-- Website Meta tags --}}
  <meta name="title"
    content="@if(isset($pageTitle)) {{$pageTitle.' - '.config('app.name')}} @else Artificial Gehna @endif">
  <meta name="description"
    content="We're a company based in New Delhi trying to bring you the latest fashion of the best quality at the lowest possible prices.">

  <!-- Bootstrap -->
  <link href="{{asset('website_assets/plugins/bootstrap/bootstrap.min.css')}}" rel="stylesheet">

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">

  {{-- Font Awesome --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  {{--
    <link rel="stylesheet" href="{{asset('website_assets/plugins/fontawesome/fontawesome.css')}}" /> --}}

  {{-- Jquery confirm --}}
  <link href="{{asset('plugins/jquery-confirm/jquery-confirm.min.css')}}" rel="stylesheet">
  {{-- Slick --}}
  <link rel="stylesheet" type="text/css" href="{{asset('website_assets/plugins/slick/slick.css')}}" />
  <link rel="stylesheet" type="text/css" href="{{asset('website_assets/plugins/slick/slick_theme.css')}}" />
  <!-- Toastr -->
  <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
  {{-- Sweet Alert --}}
  <link rel="stylesheet" href="{{asset('plugins/sweetalert2/sweetalert2.min.css')}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
  <!-- no ui slider -->
  <link rel="stylesheet" href="{{asset('website_assets/plugins/nouislider/dist/nouislider.min.css')}}">
  <!-- xzoom -->
  <link rel="stylesheet" href="{{asset('website_assets/plugins/xzoom/dist/xzoom.css')}}">
  {{-- Fancy Box --}}
  <link rel="stylesheet" href="{{asset('website_assets/plugins/fancybox/fancybox.min.css')}}" />
  <!-- Intl -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css" />
  <!-- Main style sheet -->
  <link href="{{asset('website_assets/css/style.css')}}" rel="stylesheet">

  <script @cspNonce>
    var base_url = '{{ config("app.url") }}'
  </script>

  <script type="text/javascript" @cspNonce>
    function callbackThen(response) {
      // read Promise object
      response.json().then(function(data) {
        if (data.score < 0.5) {
          window.location.href = base_url + '/page-not-found';
        }
      });
    }

    function callbackCatch(error) {
      console.error('Bot access error')
    }
  </script>

</head>

<body class="font-size-md">

  <x-website.loader />

  @include('includes.website.header')
  @include('includes.website.menu')

  @yield('content')

  @include('includes.website.footer')

  <!-- Login Modal -->
  <div class="modal fade" id="modalLoginForm" tabindex="-1" aria-labelledby="modalLoginFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold" id="modalLoginFormLabel">Welcome Back!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <p class="text-muted text-center mb-4">Login to your account to continue</p>

          <form action="" id="loginForm" autocomplete="off">
            @csrf

            <!-- Email -->
            <div class="form-floating mb-3">
              <input type="text"
                id="input-email"
                name="login_field"
                class="form-control rounded-4"
                placeholder="Email">
              <label for="input-email">Email <span class="text-danger">*</span></label>
            </div>

            <!-- Password -->
            <div class="form-floating mb-3 position-relative">
              <input type="password"
                id="input-password"
                name="password"
                class="form-control rounded-4"
                placeholder="Password">

              <label for="input-password">Password <span class="text-danger">*</span></label>

              <!-- Password toggle icon -->
              <span id="btn-view-password"
                class="position-absolute top-50 end-0 translate-middle-y me-3"
                style="cursor: pointer;">
                <i class="fa fa-eye"></i>
              </span>
            </div>

            <!-- Captcha -->
            <div class="mb-3">
              <div class="captcha mb-2 d-flex align-items-center gap-2" id="captcha">
                <img src="{{ captcha_src() }}" alt="CAPTCHA image" class="rounded" style="height: 40px;">
                <button type="button"
                  id="refresh-captcha"
                  class="btn btn-secondary rounded-4"
                  aria-label="Refresh Captcha">
                  <i class="fa fa-sync"></i>
                </button>
              </div>

              <div class="form-floating">
                <input type="text"
                  id="input-captcha"
                  name="captcha"
                  class="form-control rounded-4"
                  placeholder="Enter captcha code">
                <label for="input-captcha">Captcha <span class="text-danger">*</span></label>
              </div>
            </div>

            <!-- Forgot password -->
            <div class="mb-3 text-end">
              <a href="{{ route('public.login.forget-password.index') }}" class="text-primary text-decoration-none">
                <small><i class="fa fa-lock"></i> Forgot Password?</small>
              </a>
            </div>

            <!-- Submit button -->
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-custom py-3 rounded-4 fw-bold">
                <i class="fa fa-paper-plane me-2"></i> Login
              </button>
            </div>

            <div class="text-center mb-3">
              <span class="text-muted">OR</span>
            </div>

            <!-- Google Sign In -->
            <div class="mb-3">
              <a href="{{ route('auth.google') }}" id="btn-google-login" class="btn btn-outline-danger w-100 rounded-4 py-2">
                <i class="fab fa-google me-2"></i> Sign in with Google
              </a>
            </div>

            <div class="text-center">
              <p class="mb-0 text-muted">Do not have an account? <a href="{{ route('public.register') }}" class="text-primary text-decoration-none fw-bold">Register here</a></p>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery library -->
  <script @cspNonce src="{{asset('website_assets/plugins/jquery/jquery.min.js')}}"></script>
  {{-- Popper Js --}}
  <script @cspNonce src="{{asset('website_assets/plugins/popperjs/popper.min.js')}}"></script>
  {{-- Bootstrap --}}
  <script @cspNonce src="{{asset('website_assets/plugins/bootstrap/bootstrap.min.js')}}"></script>
  {{-- Jquery validation --}}
  <script @cspNonce src="{{asset('plugins/jquery-validation/jquery.validate.min.js')}}"></script>
  {{-- Jquery confirm --}}
  <script @cspNonce src="{{asset('plugins/jquery-confirm/jquery-confirm.min.js')}}"></script>
  {{-- Slick --}}
  <script @cspNonce type="text/javascript" src="{{asset('website_assets/plugins/slick/slick.min.js')}}"></script>
  {{-- Toastr --}}
  <script @cspNonce src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
  {{-- Sweet Alert --}}
  <script @cspNonce src="{{asset('plugins/sweetalert2/sweetalert2.min.js')}}"></script>
  {{-- Select 2 --}}
  <script @cspNonce src="{{asset('plugins/select2/js/select2.min.js')}}"></script>
  <!-- No UI Slider -->
  <script @cspNonce src="{{asset('website_assets/plugins/nouislider/dist/nouislider.min.js')}}"></script>
  <!-- xzoom -->
  <script @cspNonce src="{{asset('website_assets/plugins/xzoom/dist/xzoom.min.js')}}"></script>
  {{-- Fancy Box --}}
  <script @cspNonce src="{{asset('website_assets/plugins/fancybox/fancybox.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>
  <!-- Crypto -->
  <script @cspNonce src="{{asset('assets/js/plugins/crypto-js.min.js')}}"></script>
  <!-- Custom js -->
  <script @cspNonce src="{{asset('website_assets/js/functions.js')}}"></script>
  <script @cspNonce src="{{asset('website_assets/js/custom.js')}}"></script>

  <script @cspNonce type="text/javascript">
    $(document).ready(function() {
      // Open Mobile Menu
      $(document).on('click', '.btn-mobile-menu', function() {
        $('#mobileMenuDrawer').addClass('active');
        $('.mobile-menu-overlay').addClass('active');
        $('body').css('overflow', 'hidden');
      });

      // Close Mobile Menu
      $(document).on('click', '.btn-close-mobile, .mobile-menu-overlay', function() {
        $('#mobileMenuDrawer').removeClass('active');
        $('.mobile-menu-overlay').removeClass('active');
        $('body').css('overflow', 'auto');
      });

      // Toggle Submenu (Accordion)
      $(document).on('click', '.has-submenu', function(e) {
        e.preventDefault();
        var $submenu = $(this).next('.mobile-submenu');
        var $icon = $(this).find('.arrow-icon');

        // Toggle slide
        $submenu.stop(true, true).slideToggle(300);

        // Toggle icon
        if ($icon.hasClass('fa-chevron-down')) {
          $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        } else {
          $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }
      });

      $('.btn-wishlist-alert').on('click', function() {
        toastr.error('Login to view your wishlist');
      });
    });
  </script>

  @yield('pages-scripts')

</body>
<!-- [Body] end -->

</html>