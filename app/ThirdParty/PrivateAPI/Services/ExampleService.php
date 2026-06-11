<?php

namespace App\ThirdParty\PrivateAPI\Services;

use App\ThirdParty\PrivateAPI\PrivateAPI;

class ExampleService extends PrivateAPI
{
    // Service name
    const CLIENT = 'example';

    // URL
    const URI = [
        'CHECK_TESTING' => 'testing/validation',
    ];


    /** --- FUNCTIONS --- */

    /**
     * @param $payload
     *
     * @return \Illuminate\Http\JsonResponse|null
     * @throws \ErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function testing($payload)
    {
        $url = static::host();
        $url .= static::BASE_URL . static::URI['CHECK_TESTING'];

        return static::call($url, $payload, 'post');
    }

}
