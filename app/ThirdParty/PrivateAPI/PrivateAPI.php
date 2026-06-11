<?php

namespace App\ThirdParty\PrivateAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class PrivateAPI
{
    const CLIENT = 'default';

    const BASE_URL = 'private-api/';


    /** --- PROTECTED FUNCTIONS --- */

    /**
     * @param $url
     * @param $payload
     * @param $method
     *
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \GlobalXtreme\Response\Exception\ErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected static function call($url, $payload = [], $method = 'get')
    {
        try {

            // Create a guzzle client
            $client = new Client();

            // Forward the request and get the response.
            $response = $client->request($method, $url, self::prepare($payload));

            // Set response body
            $body = json_decode($response->getBody());

            // Check http status code
            $statusCode = $response->getStatusCode();
            if ($statusCode > 299) {
                if ($status = $body?->status) {
                    error($statusCode, $status->message, $status->internalMsg, $status->attributes);
                }

                errDefault();
            }

            // Response with json
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

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    protected static function host()
    {
        return config('private-api.clients.' . static::CLIENT . '.host');
    }

    /**
     * @param array $payload
     *
     * @return array
     */
    protected static function prepare(array $payload)
    {
        // Default options
        $options = [];

        // Set headers
        $options['headers'] = self::setHeaders();

        // Set content type
        $options['json'] = $payload;

        return $options;
    }

    /**
     * @return array
     */
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
