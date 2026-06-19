<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web-admin'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'staff'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web-admin' => [
            'driver' => 'jwt',
            'provider' => 'staff',
            'hash' => false
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'staff' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Staff\StaffUser::class,
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'staff' => [
            'provider' => 'staff',
            'table' => env('AUTH_STAFF_PASSWORD_RESET_TOKEN_TABLE', 'staff_user_passwords'),
            'expire' => 60,
            'throttle' => 60,
        ],
        'partner' => [
            'provider' => 'partner',
            'table' => env('AUTH_PARTNER_PASSWORD_RESET_TOKEN_TABLE', 'partner_staff_user_passwords'),
            'expire' => 60,
            'throttle' => 60,
        ],
        'member' => [
            'provider' => 'member',
            'table' => env('AUTH_MEMBER_PASSWORD_RESET_TOKEN_TABLE', 'member_user_passwords'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

    'password_default' => env('AUTH_PASSWORD_DEFAULT', 'adminnewin101'),
    'password_default_partner' => env('AUTH_PASSWORD_DEFAULT', 'partnernewin101'),
    'password_default_member' => env('AUTH_PASSWORD_DEFAULT', 'membernewin101'),

    'forgot-password-link' => [
        'staff' => env('FORGOT_PASSWORD_LINK_STAFF', ''),
        'partner' => env('FORGOT_PASSWORD_LINK_PARTNER', ''),
        'member' => env('FORGOT_PASSWORD_LINK_MEMBER', ''),
    ],

    'with-permission' => env('WITH_PERMISSION', false)

];
