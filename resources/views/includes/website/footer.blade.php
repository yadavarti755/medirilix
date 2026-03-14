{{-- Footer Section --}}
<section class="hp-footer-section">
    <div class="container">
        <div class="row">

            <div class="col-lg-3 col-sm-6 col-12 footer-col">
                <div class="footer-content-wrapper">
                    <h4 class="footer-title">
                        Quick Links
                    </h4>
                    <ul class="footer-ul list-unstyled">
                        @foreach($quickLinks as $menu)
                        <li>
                            <a href="{{ $menu->url }}">{{ $menu->title }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12 footer-col">
                <div class="footer-content-wrapper">
                    <h4 class="footer-title">
                        Information
                    </h4>
                    <ul class="footer-ul list-unstyled">
                        @foreach($informationMenus as $menu)
                        <li>
                            <a href="{{ $menu->url }}">{{ $menu->title }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 col-12 footer-col">
                <div class="footer-content-wrapper">
                    <a class="footer-logo-title" href="/">
                        <img src="{{ $siteSettings->footer_logo_full_path }}" class="site-logo-img" alt="Logo">
                    </a>
                    <div class="footer-social-links">
                        <ul class="footer-social-ul">
                            @foreach($socialLinks as $link)
                            <li>
                                <a href="{{ $link->url }}" target="_BLANK">
                                    <span class="{{ $link->icon_class }}"></span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <p class="footer-about">
                        {{ $siteSettings->footer_about_us }}
                    </p>
                    @if($contactDetail)
                    <p class="footer-phone mb-1"><strong>Support Hotline:</strong>
                        @php
                        $phones = explode(',', $contactDetail->phone_numbers);
                        @endphp
                        {{ $phones[0] ?? '' }}
                    </p>
                    <p class="footer-email mb-1"><strong>Support Email:</strong>
                        @php
                        $emails = explode(',', $contactDetail->email_ids);
                        @endphp
                        {{ $emails[0] ?? '' }}
                    </p>
                    @endif
                </div>
            </div>

        </div>

        @if($paymentMethods->isNotEmpty())
        <div class="row mt-2 pt-3 border-top border-secondary">
            <div class="col-12 text-center text-lg-start">
                <div class="footer-payment-methods">
                    <span class="me-3 footer-payment-label">We Accept:</span>
                    <ul class="list-inline d-inline-block mb-0">
                        @foreach($paymentMethods as $method)
                        <li class="list-inline-item mb-2">
                            <div class="payment-method-icon">
                                <img src="{{ $method->file_url }}" alt="{{ $method->title }}" title="{{ $method->title }}">
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

{{-- Bottom Footer --}}
<section class="bottom-footer">
    <div class="container">
        <p class="mb-0">
            &copy; {{ date('Y') }} All rights reserved to {{ $siteSettings->site_name }}
        </p>
    </div>
</section>