<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Base Prefix, Namespace & Login patch (endpoint)
     |--------------------------------------------------------------------------
     |
     | Set up base endpoint for web, mobile and B2B (default)
     | Set up base namespace controller for web, mobile and B2B (default)
     |
     */

    'prefix' => [

        'web' => [

            'admin' => env('BASE_WEB_ADMIN_PREFIX', 'api/web/admin'),

            'partner' => env('BASE_WEB_PARTNER_PREFIX', 'api/web/partner'),

        ],

        'mobile' => [

            'partner' => env('BASE_MOBILE_PARTNER_PREFIX', 'api/mobile/partner'),

            'member' => env('BASE_MOBILE_MEMBER_PREFIX', 'api/mobile/member'),

        ],

        'public' => env('BASE_PUBLIC_PREFIX', 'api/public'),

    ],

    'namespace' => [

        'web' => [

            'admin' => env('BASE_WEB_ADMIN_NAMESPACE', 'Web\\Admin'),

            'partner' => env('BASE_WEB_PARTNER_NAMESPACE', 'Web\\Partner'),

        ],

        'mobile' => [

            'partner' => env('BASE_MOBILE_PARTNER_NAMESPACE', 'Mobile\\Partner'),

            'member' => env('BASE_MOBILE_MEMBER_NAMESPACE', 'Mobile\\Member'),

        ],

        'public' => env('BASE_PUBLIC_NAMESPACE', 'Public'),

    ],

    'login' => [

        'web' => [

            'admin' => env('BASE_WEB_ADMIN_LOGIN_URI', 'api/web/admin/auths/login'),

            'partner' => env('BASE_WEB_PARTNER_LOGIN_URI', 'api/web/partner/auths/login'),

        ],

        'mobile' => [

            'partner' => env('BASE_MOBILE_PARTNER_LOGIN_URI', 'api/mobile/partner/auths/login'),

            'member' => env('BASE_MOBILE_MEMBER_LOGIN_URI', 'api/mobile/member/auths/login'),

            'member-second' => env('BASE_MOBILE_MEMBER_LOGIN_URI', 'api/mobile/member/auths/login/social-media'),

        ],

    ],


    /*
     |--------------------------------------------------------------------------
     | Developer E-Mails
     |--------------------------------------------------------------------------
     |
     | Contact the email below if you want to discuss Laravel
     |
     */

    'dev-emails' => [
        'yusologia@gmail.com',
        'yuswa98@gmail.com',
    ],

];
