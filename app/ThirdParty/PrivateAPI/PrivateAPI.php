<?php

namespace App\ThirdParty\PrivateAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class PrivateAPI
{
    const CLIENT = 'default';

    const BASE_URL = 'private-api/';

    protected static function call($url, $payload = [], $method = 'get')
    {
        try {
            $client = new Client();

            $response = $client->request($method, $url, self::prepare($payload));

            $body = json_decode($response->getBody());

            $statusCode = $response->getStatusCode();
            if ($statusCode > 299) {
                if ($status = $body?->status) {
                    error($statusCode, $status->message, $status->internalMsg, $status->attributes);
                }

                errDefault();
            }

            return response()->json($body, $statusCode);
        } catch (BadResponseException $e) {
            $body = json_decode($e->getResponse()->getBody());
            if ($body?->status) {
                $status = $body->status;
                error($status->code, $status->message, $status->internalMsg, $status->attributes);
            }

            errDefault($e->getMessage());
        }
    }

    protected static function host()
    {
        return config('private-api.clients.' . static::CLIENT . '.host');
    }

    protected static function prepare(array $payload)
    {
        $options = [];

        $options['headers'] = self::setHeaders();

        $options['json'] = $payload;

        return $options;
    }

    protected static function setHeaders()
    {
        $client = config('private-api.clients.' . static::CLIENT);
        if (!$client) {
            errThirdPartyPrivateAPIClientInvalid();
        }

        return [
            'Client-ID' => $client['client-id'],
            'Client-Name' => $client['client-name'],
            'Client-Secret' => $client['client-secret'],
        ];
    }
}
