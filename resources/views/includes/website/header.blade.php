{{-- Top Header --}}
<div class="top-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-12">
                <small class="mb-0 top-header-text">Welcome in {{ $siteSettings->site_name }}
                </small>
            </div>
            <div class="col-sm-4 col-12 text-right">

                <ul class="top-header-social-ul">
                    <li>
                        <div class="custom-lang-selector">
                            <button class="lang-btn">
                                <i class="fas fa-globe"></i> Language <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                            </button>
                            <ul class="lang-dropdown">
                                <li><a href="#" class="change-lang" data-lang="en">English</a></li>
                                <li><a href="#" class="change-lang" data-lang="hi">Hindi</a></li>
                                <li><a href="#" class="change-lang" data-lang="bn">Bengali</a></li>
                                <li><a href="#" class="change-lang" data-lang="ar">Arabic</a></li>
                                <li><a href="#" class="change-lang" data-lang="fr">French</a></li>
                                <li><a href="#" class="change-lang" data-lang="es">Spanish</a></li>
                            </ul>
                        </div>

                        <div id="google_translate_element" style="display:none"></div>

                        <script @cspNonce type="text/javascript">
                            function googleTranslateElementInit() {
                                new google.translate.TranslateElement({
                                    pageLanguage: 'en',
                                    autoDisplay: false
                                }, 'google_translate_element');
                            }

                            function changeLanguage(langCode) {
                                var selectField = document.querySelector(".goog-te-combo");
                                if (selectField) {
                                    selectField.value = langCode;
                                    selectField.dispatchEvent(new Event('change'));
                                }
                            }

                            // Use event listeners to comply with CSP (no inline scripts/onclick)
                            document.addEventListener('DOMContentLoaded', function() {
                                document.querySelectorAll('.change-lang').forEach(function(element) {
                                    element.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        var lang = this.getAttribute('data-lang');
                                        changeLanguage(lang);
                                    });
                                });
                            });
                        </script>
                        <script @cspNonce type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
                    </li>
                    @foreach($socialLinks as $link)
                    <li>
                        <a href="{{ $link->url }}" target="_BLANK">
                            <span class="{{ $link->icon_class }}"></span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Header --}}
<header class="site-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-6 col-sm-6 col-6">
                <div class="header-logo-wrapper">
                    <a href="{{ route('homepage') }}" class="site-logo">
                        <img src="{{ $siteSettings->header_logo_full_path }}" class="site-logo-img" alt="Logo">
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                <div class="header-search-col">
                    <div class="header-search-input-group-wrapper" id="header-search-input-group-wrapper">
                        <form action="{{ route('search') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control header-search-input"
                                    name="q"
                                    placeholder="Search Here..." aria-label="Search Here..."
                                    aria-describedby="button-addon2" id="header-search-input"
                                    value="{{ request('q') }}">
                                <button class="btn btn-outline-secondary header-search-btn" type="submit"
                                    id="button-addon2"><span class="fas fa-search"></span></button>
                            </div>
                        </form>
                        <ul id="search_results"></ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-6 header-details-col">

                <div class="header-cart-icons-col">
                    <ul class="header-carts-icons-list">
                        <li class="header-contact-icon-list-item">
                            <a href="{{ route('contact-us') }}" class="header-contact-icon-wrapper">
                                <span class="header-mobile-contact-icon">
                                    <i class="fa-solid fa-headset"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cart') }}" class="header-cart-icon-wrapper">
                                <span class="header-cart-icon">
                                    <i class="fas fa-cart-shopping"></i> <span class="cart-icon-text">Cart</span>
                                </span>
                                <span class="header-cart-count">{{ $cartCount }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>