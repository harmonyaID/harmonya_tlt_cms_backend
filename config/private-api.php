<?php

return [

    'credentials' => [

        'testing' => [
            'id' => env('PRIVATE_API_TESTING_ID', ''),
            'key' => env('PRIVATE_API_TESTING_KEY', '')
        ],

    ],

    'clients' => [

        'example' => [
            'host' => env('CLIENT_PRIVATE_API_EXAMPLE_HOST', ''),
            'client-id' => env('CLIENT_PRIVATE_API_EXAMPLE_CLIENT_ID', ''),
            'client-name' => env('CLIENT_PRIVATE_API_EXAMPLE_CLIENT_NAME', ''),
            'client-secret' => env('CLIENT_PRIVATE_API_EXAMPLE_CLIENT_SECRET', ''),
        ],

    ]

];
