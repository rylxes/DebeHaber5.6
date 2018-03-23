<?php

$protocol = 'https://';

if (! isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
{
    $protocol = 'http://';
}

return [

    /*
    * X-Content-Type-Options
    *
    * Reference: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options
    *
    * Available Value: 'nosniff'
    */

    'x-content-type-options' => 'nosniff',

    /*
    * X-Download-Options
    *
    * Reference: https://msdn.microsoft.com/en-us/library/jj542450(v=vs.85).aspx
    *
    * Available Value: 'noopen'
    */

    'x-download-options' => 'noopen',

    /*
    * X-Frame-Options
    *
    * Reference: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options
    *
    * Available Value: 'deny', 'sameorigin', 'allow-from <uri>'
    */

    'x-frame-options' => 'sameorigin',

    /*
    * X-Permitted-Cross-Domain-Policies
    *
    * Reference: https://www.adobe.com/devnet/adobe-media-server/articles/cross-domain-xml-for-streaming.html
    *
    * Available Value: 'all', 'none', 'master-only', 'by-content-type', 'by-ftp-filename'
    */

    'x-permitted-cross-domain-policies' => 'none',

    /*
    * X-XSS-Protection
    *
    * Reference: https://blogs.msdn.microsoft.com/ieinternals/2011/01/31/controlling-the-xss-filter
    *
    * Available Value: '1', '0', '1; mode=block'
    */

    'x-xss-protection' => '1; mode=block',

    /*
    * Referrer-Policy
    *
    * Reference: https://w3c.github.io/webappsec-referrer-policy
    *
    * Available Value: 'no-referrer', 'no-referrer-when-downgrade', 'origin', 'origin-when-cross-origin',
    *                  'same-origin', 'strict-origin', 'strict-origin-when-cross-origin', 'unsafe-url'
    */

    'referrer-policy' => 'no-referrer',

    /*
    * HTTP Strict Transport Security
    *
    * Reference: https://developer.mozilla.org/en-US/docs/Web/Security/HTTP_strict_transport_security
    *
    * Please ensure your website had set up ssl/tls before enable hsts.
    */

    'hsts' => [
        'enable' => false,

        'max-age' => 15552000,

        'include-sub-domains' => false,
    ],

    /*
    * Public Key Pinning
    *
    * Reference: https://developer.mozilla.org/en-US/docs/Web/Security/Public_Key_Pinning
    *
    * hpkp will be ignored if hashes is empty.
    */

    'hpkp' => [
        'hashes' => [
            // [
            //     'algo' => 'sha256',
            //     'hash' => 'hash-value',
            // ],
        ],

        'include-sub-domains' => false,

        'max-age' => 15552000,

        'report-only' => false,

        'report-uri' => null,
    ],

    /*
    * Content Security Policy
    *
    * Reference: https://developer.mozilla.org/en-US/docs/Web/Security/CSP
    *
    * csp will be ignored if custom-csp is not null. To disable csp, set custom-csp to empty string.
    *
    * Note: custom-csp does not support report-only.
    */

    'custom-csp' => null,

    'csp' => [
        'report-only' => false,

        'report-uri' => null,

        'upgrade-insecure-requests' => false,

        // enable or disable the automatic conversion of sources to https
        'https-transform-on-https-connections' => true,

        'base-uri' => [
            //
        ],

        'default-src' => [
            //
        ],

        'child-src' => [
            //
        ],

        'script-src' => [
            'allow' => [
                $protocol.'cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js',
                $protocol.'www.google-analytics.com/',
                $protocol.'www.googletagmanager.com/',
                $protocol.'js.stripe.com/v3/',
                $protocol.'fonts.googleapis.com/',
                $protocol.'www.gravatar.com/avatar/',
                $protocol.'ajax.googleapis.com/ajax/libs/webfont/',
                $protocol.'code.jquery.com/jquery-3.2.1.min.js',
            ],

            'hashes' => [
                // ['sha256' => 'hash-value'],
            ],

            'nonces' => [
                //
            ],

            'self' => true,

            'unsafe-inline' => true,

            'unsafe-eval' => true,
        ],

        'style-src' => [
            'allow' => [
                $protocol.'fonts.googleapis.com/',
                $protocol.'maxcdn.bootstrapcdn.com/',
                $protocol.'cdnjs.cloudflare.com',
            ],

            'self' => true,
            //TODO Change to false;
            'unsafe-inline' => true,
        ],

        'img-src' => [
            'allow' => [
                $protocol.'www.debehaber.com',
                $protocol.'www.google-analytics.com',
                $protocol.'www.gravatar.com/avatar/',
            ],

            'types' => [
                //
            ],

            'self' => true,

            'data' => false,
        ],

        /*
        * The following directives are all use 'allow' and 'self' flag.
        *
        * Note: default value of 'self' flag is false.
        */

        'font-src' => [
            'allow' => [
                $protocol.'fonts.gstatic.com/s/',
            ],

            'self' => true,
        ],

        'connect-src' => [
          'allow' => [
              $protocol.'www.debehaber.com'

          ],
            'self' => true,
        ],

        'form-action' => [
            'self' => true,
        ],

        'frame-ancestors' => [
            //
        ],

        'media-src' => [
            'self' => true,
        ],

        'object-src' => [
            //
        ],

        /*
        * plugin-types only support 'allow'.
        */

        'plugin-types' => [
            //
        ],
    ],

];
