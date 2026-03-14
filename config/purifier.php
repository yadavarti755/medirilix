<?php

/**
 * Ok, glad you are here
 * first we get a config instance, and set the settings
 * $config = HTMLPurifier_Config::createDefault();
 * $config->set('Core.Encoding', $this->config->get('purifier.encoding'));
 * $config->set('Cache.SerializerPath', $this->config->get('purifier.cachePath'));
 * if ( ! $this->config->get('purifier.finalize')) {
 *     $config->autoFinalize = false;
 * }
 * $config->loadArray($this->getConfig());
 *
 * You must NOT delete the default settings
 * anything in settings should be compacted with params that needed to instance HTMLPurifier_Config.
 *
 * @link http://htmlpurifier.org/live/configdoc/plain.html
 */

return [
    'encoding'           => 'UTF-8',
    'finalize'           => true,
    'ignoreNonStrings'   => false,
    'cachePath'          => storage_path('app/purifier'),
    'cacheFileMode'      => 0755,
    'settings'      => [
        'default' => [
            'HTML.Doctype' => 'HTML 4.01 Transitional',

            // Comprehensive HTML tags for rich content editing
            'HTML.Allowed' => implode(',', [
                // Text formatting
                'p[style|class|id]',
                'div[style|class|id]',
                'span[style|class|id]',
                'br',

                // Headers
                'h1[style|class|id]',
                'h2[style|class|id]',
                'h3[style|class|id]',
                'h4[style|class|id]',
                'h5[style|class|id]',
                'h6[style|class|id]',

                // Text emphasis
                'strong[style|class]',
                'b[style|class]',
                'em[style|class]',
                'i[style|class]',
                'u[style|class]',
                's[style|class]',
                'strike[style|class]',
                'del[style|class]',
                'ins[style|class]',
                'mark[style|class]',
                'small[style|class]',
                'sup[style|class]',
                'sub[style|class]',

                // Links
                'a[href|title|target|rel|style|class]',

                // Lists
                'ul[style|class]',
                'ol[style|class|start|type]',
                'li[style|class]',
                'dl[style|class]',
                'dt[style|class]',
                'dd[style|class]',

                // Tables
                'table[style|class|border|cellpadding|cellspacing|width]',
                'thead[style|class]',
                'tbody[style|class]',
                'tfoot[style|class]',
                'tr[style|class]',
                'th[style|class|colspan|rowspan|scope]',
                'td[style|class|colspan|rowspan]',
                'caption[style|class]',
                'colgroup[style|class]',
                'col[style|class|span]',

                // Media
                'img[src|alt|title|width|height|style|class]',
                'figure[style|class]',
                'figcaption[style|class]',

                // Code and preformatted text
                'pre[style|class]',
                'code[style|class]',
                'kbd[style|class]',
                'samp[style|class]',
                'var[style|class]',

                // Quotes and citations
                'blockquote[style|class|cite]',
                'q[style|class|cite]',
                'cite[style|class]',

                // Definition and description
                'abbr[style|class|title]',
                'acronym[style|class|title]',

                // Horizontal rule
                'hr[style|class]',
            ]),

            // Comprehensive CSS properties for design flexibility
            'CSS.AllowedProperties' => implode(',', [
                // Typography
                'font-family',
                'font-size',
                'font-weight',
                'font-style',
                'font-variant',
                'line-height',
                'letter-spacing',
                'word-spacing',
                'text-align',
                'text-decoration',
                'text-decoration-line',
                'text-decoration-style',
                'text-decoration-color',
                'text-transform',
                'text-indent',
                'white-space',

                // Colors
                'color',
                'background-color',
                'background',
                'background-image',
                'background-repeat',
                'background-position',
                'background-size',
                'background-attachment',

                // Box model
                'margin',
                'margin-top',
                'margin-right',
                'margin-bottom',
                'margin-left',
                'padding',
                'padding-top',
                'padding-right',
                'padding-bottom',
                'padding-left',
                'width',
                'height',
                'min-width',
                'min-height',
                'max-width',
                'max-height',

                // Borders
                'border',
                'border-top',
                'border-right',
                'border-bottom',
                'border-left',
                'border-width',
                'border-style',
                'border-color',

                // Lists
                'list-style',
                'list-style-type',
                'list-style-position',
                'list-style-image',

                // Tables
                'table-layout',
                'border-collapse',
                'border-spacing',
                'caption-side',
                'vertical-align',
            ]),

            // Enhanced configuration options
            'HTML.SafeIframe' => false, // Keep iframes blocked for security
            'HTML.SafeObject' => false, // Keep objects blocked for security
            'HTML.SafeEmbed' => false,  // Keep embeds blocked for security
            'HTML.FlashAllowFullScreen' => false,
            'HTML.Nofollow' => true, // Add rel="nofollow" to external links
            'HTML.TargetBlank' => true, // Add target="_blank" to external links
            'HTML.TidyLevel' => 'heavy',

            // Auto-formatting
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
            'AutoFormat.RemoveSpansWithoutAttributes' => false,
            'AutoFormat.Linkify' => true,

            // URI filtering
            'URI.DisableExternalResources' => false, // Allow external images
            'URI.AllowedSchemes' => ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true, 'data' => true],

            // CSS specific configurations
            'CSS.MaxImgLength' => '1200px',
            'CSS.Proprietary' => false, // Disable proprietary CSS
            'CSS.ForbiddenProperties' => 'javascript,behavior,expression,-moz-binding', // Block dangerous CSS

            // Output configuration
            'Output.FlashCompat' => false,
            'Output.FixInnerHTML' => true,
            'Output.SortAttr' => true,
            'Output.TidyFormat' => true,
        ],
        'test'    => [
            'Attr.EnableID' => 'true',
        ],
        "youtube" => [
            "HTML.SafeIframe"      => 'true',
            "URI.SafeIframeRegexp" => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
        ],
        'custom_definition' => [
            'id'  => 'html5-definitions',
            'rev' => 1,
            'debug' => false,
            'elements' => [
                // http://developers.whatwg.org/sections.html
                ['section', 'Block', 'Flow', 'Common'],
                ['nav',     'Block', 'Flow', 'Common'],
                ['article', 'Block', 'Flow', 'Common'],
                ['aside',   'Block', 'Flow', 'Common'],
                ['header',  'Block', 'Flow', 'Common'],
                ['footer',  'Block', 'Flow', 'Common'],

                // Content model actually excludes several tags, not modelled here
                ['address', 'Block', 'Flow', 'Common'],
                ['hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common'],

                // http://developers.whatwg.org/grouping-content.html
                ['figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common'],
                ['figcaption', 'Inline', 'Flow', 'Common'],

                // http://developers.whatwg.org/the-video-element.html#the-video-element
                ['video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                    'src' => 'URI',
                    'type' => 'Text',
                    'width' => 'Length',
                    'height' => 'Length',
                    'poster' => 'URI',
                    'preload' => 'Enum#auto,metadata,none',
                    'controls' => 'Bool',
                ]],
                ['source', 'Block', 'Flow', 'Common', [
                    'src' => 'URI',
                    'type' => 'Text',
                ]],

                // http://developers.whatwg.org/text-level-semantics.html
                ['s',    'Inline', 'Inline', 'Common'],
                ['var',  'Inline', 'Inline', 'Common'],
                ['sub',  'Inline', 'Inline', 'Common'],
                ['sup',  'Inline', 'Inline', 'Common'],
                ['mark', 'Inline', 'Inline', 'Common'],
                ['wbr',  'Inline', 'Empty', 'Core'],

                // http://developers.whatwg.org/edits.html
                ['ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
                ['del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
            ],
            'attributes' => [
                ['iframe', 'allowfullscreen', 'Bool'],
                ['table', 'height', 'Text'],
                ['td', 'border', 'Text'],
                ['th', 'border', 'Text'],
                ['tr', 'width', 'Text'],
                ['tr', 'height', 'Text'],
                ['tr', 'border', 'Text'],
            ],
        ],
        'custom_attributes' => [
            ['a', 'target', 'Enum#_blank,_self,_target,_top'],
        ],
        'custom_elements' => [
            ['u', 'Inline', 'Inline', 'Common'],
        ],
    ],

];
