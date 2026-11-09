<?php

namespace App\Services\Guesty;

use App\Models\Setting\ApiConfiguration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GuestyClient
{
    const TOKEN_CACHE_KEY = 'guesty_access_token';
    const CONFIG_CACHE_KEY = 'guesty_api_configuration';
    const CONFIG_KEY = 'guesty';

    protected function credential(string $field, ?string $envFallback = null): ?string
    {
        $config = Cache::remember(self::CONFIG_CACHE_KEY, 300, function () {
            return ApiConfiguration::where('key', self::CONFIG_KEY)
                ->where('isActive', true)
                ->first();
        });

        $value = $config?->credential($field);

        return $value ?: $envFallback;
    }

    public function getAccessToken(): string
    {
        return Cache::remember(self::TOKEN_CACHE_KEY, 3600 * 23, function () {
            $authUrl = $this->credential('auth_url', config('guesty.auth_url'));

            $response = Http::asForm()->post($authUrl, [
                'grant_type' => 'client_credentials',
                'scope' => 'open-api',
                'client_id' => $this->credential('client_id', config('guesty.client_id')),
                'client_secret' => $this->credential('client_secret', config('guesty.client_secret')),
            ]);

            if (!$response->successful()) {
                throw new \Exception('Unable to authenticate with Guesty: ' . $response->body());
            }

            $data = $response->json();

            $ttl = ($data['expires_in'] ?? 86400) - 300;
            Cache::put(self::TOKEN_CACHE_KEY, $data['access_token'], $ttl);

            return $data['access_token'];
        });
    }

    protected function baseUrl(): string
    {
        return $this->credential('base_url', config('guesty.base_url'));
    }

    public function getListings(int $limit = 25, int $skip = 0): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->get($this->baseUrl() . '/listings', [
                'limit' => $limit,
                'skip' => $skip,
            ]);

        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_CACHE_KEY);

            $response = Http::withToken($this->getAccessToken())
                ->get($this->baseUrl() . '/listings', [
                    'limit' => $limit,
                    'skip' => $skip,
                ]);
        }

        if (!$response->successful()) {
            throw new \Exception('Unable to fetch Guesty listings: ' . $response->body());
        }

        return $response->json();
    }

    public function get(string $path, array $query = []): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->get($this->baseUrl() . $path, $query);

        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_CACHE_KEY);

            $response = Http::withToken($this->getAccessToken())
                ->get($this->baseUrl() . $path, $query);
        }

        if (!$response->successful()) {
            throw new \Exception("Unable to fetch Guesty $path: " . $response->body());
        }

        return $response->json();
    }

    protected function send(string $method, string $path, array $payload): array
    {
        $call = fn () => Http::withToken($this->getAccessToken())
            ->acceptJson()
            ->asJson()
            ->send($method, $this->baseUrl() . $path, ['json' => $payload]);

        $response = $call();

        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_CACHE_KEY);
            $response = $call();
        }

        if (!$response->successful()) {
            throw new \Exception("Guesty $method $path failed ({$response->status()}): " . $response->body());
        }

        return $response->json() ?? [];
    }

    public function createListing(array $payload): array
    {
        return $this->send('POST', '/listings', $payload);
    }

    public function updateListing(string $guestyId, array $payload): array
    {
        return $this->send('PUT', '/listings/' . $guestyId, $payload);
    }

    public function isConfigured(): bool
    {
        return (bool) $this->credential('client_id', config('guesty.client_id'))
            && (bool) $this->credential('client_secret', config('guesty.client_secret'));
    }

    public function downloadImage(string $url): ?string
    {
        $response = Http::timeout(30)->get($url);

        return $response->successful() ? $response->body() : null;
    }

    public function getSupportedAmenities(): array
    {
        return $this->get('/properties-api/amenities/supported');
    }

    public function getAmenityGroups(): array
    {
        return $this->get('/properties-api/amenities/groups');
    }

    public function getRoomTypes(): array
    {
        return $this->get('/properties/spaces/room-types');
    }

    public function getBedTypes(): array
    {
        return $this->get('/properties/spaces/bed-types');
    }
}
