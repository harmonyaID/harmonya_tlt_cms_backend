<?php

namespace App\ThirdParty\PrivateAPI\Services;

use App\ThirdParty\PrivateAPI\PrivateAPI;

class ExampleService extends PrivateAPI
{
    const CLIENT = 'example';

    const URI = [
        'CHECK_TESTING' => 'testing/validation',
    ];

    public static function testing($payload)
    {
        $url = static::host();
        $url .= static::BASE_URL . static::URI['CHECK_TESTING'];

        return static::call($url, $payload, 'post');
    }
}
