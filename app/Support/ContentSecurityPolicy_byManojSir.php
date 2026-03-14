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
        ])->addNonce(Directive::SCRIPT);

        // ✅ Explicit style-src for inline + external styles
        $policy->add(Directive::STYLE, [
            Keyword::SELF,
            'https://fonts.googleapis.com',
            'https://cdnjs.cloudflare.com',
        ])->addNonce(Directive::STYLE);

        // STYLE_ELEM is useful too, especially for <style> blocks
        $policy->add(Directive::STYLE_ELEM, [
            Keyword::SELF,
            'https://fonts.googleapis.com',
            'https://cdnjs.cloudflare.com',
        ])->addNonce(Directive::STYLE_ELEM);

        // ✅ Allow style attributes (for SweetAlert2, inline styling)
        $policy->add(Directive::STYLE_ATTR, Keyword::UNSAFE_INLINE);

        $policy->add(Directive::FONT, [
            Keyword::SELF,
            'https://fonts.gstatic.com',
            'https://cdnjs.cloudflare.com',
            'data:',
        ]);

        $policy->add(Directive::IMG, [
            Keyword::SELF,
            'data:',
            'https:',
        ]);

        $policy->add(Directive::FRAME, [
            Keyword::SELF,
            'https://platform.twitter.com',
            'https://www.youtube-nocookie.com',
            'https://www.youtube.com',
        ]);

        $policy->add(Directive::OBJECT, Keyword::NONE);
        $policy->add(Directive::FRAME_ANCESTORS, Keyword::NONE);
        $policy->add(Directive::BASE, Keyword::SELF);
        $policy->add(Directive::FORM_ACTION, Keyword::SELF);

        $policy->add(Directive::CHILD, [
            Keyword::SELF,
            'https://www.youtube.com',
            'https://www.youtube-nocookie.com',
            'https://platform.twitter.com',
        ]);
    }
}
