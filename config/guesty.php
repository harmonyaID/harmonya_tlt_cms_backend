<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Guesty Open API credentials
     |-------------------------------------------------------------------------
     | Generated from: Guesty dashboard > Integrations > API & Webhooks
     */

    'client_id' => env('GUESTY_CLIENT_ID'),
    'client_secret' => env('GUESTY_CLIENT_SECRET'),

    'auth_url' => env('GUESTY_AUTH_URL', 'https://open-api.guesty.com/oauth2/token'),
    'base_url' => env('GUESTY_BASE_URL', 'https://open-api.guesty.com/v1'),

];