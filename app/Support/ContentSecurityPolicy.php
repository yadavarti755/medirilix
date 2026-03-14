<?php

namespace App\Support;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;

class ContentSecurityPolicy implements Preset
{
    public function configure(Policy $policy): void
    {
        // your existing allow-list (unchanged)
        $policy->add(Directive::DEFAULT, [
            Keyword::SELF,
            'https://csp.secureserver.net',
            'https://cdn.ckeditor.com',
        ]);

        $policy->add(Directive::SCRIPT, [
            Keyword::SELF,
            'https://cdnjs.cloudflare.com',
            'https://platform.twitter.com',
            'https://www.w3.org',
            'https://img1.wsimg.com',
            'https://cdn.jsdelivr.net',
            'https://translate.google.com',
            'https://www.gstatic.com',
            'https://translate-pa.googleapis.com',
            'https://ipapi.co',
            'https://checkout.razorpay.com',
        ])
            ->addNonce(Directive::SCRIPT);

        // Keep element <style> strict & nonced
        $policy->add(Directive::STYLE_ELEM, [
            Keyword::SELF,
            'https://fonts.googleapis.com',
            'https://cdnjs.cloudflare.com',
            'https://cdn.jsdelivr.net',
            'https://www.gstatic.com',
            'https://translate.google.com',
            'https://translate-pa.googleapis.com'
        ])
            ->addNonce(Directive::STYLE_ELEM);

        // ✅ Allow ONLY style attributes (needed by SweetAlert2)
        $policy->add(Directive::STYLE_ATTR, Keyword::UNSAFE_INLINE);

        $policy->add(Directive::FONT, [
            Keyword::SELF,
            'https://fonts.gstatic.com',
            'https://cdnjs.cloudflare.com',
            'data:',
        ]);

        $policy->add(Directive::IMG, [Keyword::SELF, 'data:', 'https:', 'http://translate.google.com', 'https://checkout.razorpay.com']);
        $policy->add(Directive::FRAME, [
            Keyword::SELF,
            'https://platform.twitter.com',
            'https://www.youtube-nocookie.com',
            'https://www.youtube.com',
            'https://api.razorpay.com',
            'https://checkout.razorpay.com'
        ]);
        $policy->add(Directive::OBJECT, Keyword::NONE);
        $policy->add(Directive::FRAME_ANCESTORS, Keyword::NONE);
        $policy->add(Directive::BASE, Keyword::SELF);
        $policy->add(Directive::FORM_ACTION, [
            Keyword::SELF,
            'http://127.0.0.1:8000',
            'http://localhost:8000',
            'https://www.sandbox.paypal.com',
            'https://www.paypal.com',
            'https://api.razorpay.com'
        ]);

        $policy->add(Directive::CHILD, [
            Keyword::SELF,
            'https://www.youtube.com',
            'https://www.youtube-nocookie.com',
            'https://platform.twitter.com',
        ]);

        $policy->add(Directive::CONNECT, [
            Keyword::SELF,
            'https://ipapi.co',
            'https://translate-pa.googleapis.com',
            'https://api.razorpay.com',
            'https://checkout.razorpay.com',
        ]);
    }
}
