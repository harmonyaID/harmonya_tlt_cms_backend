<?php

namespace App\Http\Controllers\Public\Guesty;

use App\Http\Controllers\Controller;
use App\Services\Guesty\GuestyClient;
use App\Services\Guesty\GuestyTokenLimitException;

class GuestyTokenController extends Controller
{
    public function show(GuestyClient $client)
    {
        if (!$client->isConfigured()) {
            error(503, 'Guesty is not configured');
        }

        try {
            return success($client->publicToken());
        } catch (GuestyTokenLimitException $e) {
            error(503, 'Guesty token is temporarily unavailable');
        } catch (\Throwable $e) {
            report($e);
            error(503, 'Guesty token is temporarily unavailable');
        }
    }
}
