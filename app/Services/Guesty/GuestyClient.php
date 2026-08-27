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

    /**
     * Resolve a Guesty credential/setting, preferring the DB-stored configuration
     * (managed via Property > Guesty Configuration in the admin panel) and
     * falling back to .env / config/guesty.php when it hasn't been set up yet.
     *
     * @param string $field one of: client_id, client_secret, auth_url, base_url
     * @param string|null $envFallback
     *
     * @return string|null
     */
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

    /**
     * Exchange client credentials for an access token (cached until near-expiry).
     *
     * @return string
     * @throws \Exception
     */
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

            // Cache for slightly less than the token's real lifetime (default 86400s / 24h)
            $ttl = ($data['expires_in'] ?? 86400) - 300;
            Cache::put(self::TOKEN_CACHE_KEY, $data['access_token'], $ttl);

            return $data['access_token'];
        });
    }

    /**
     * @return string
     */
    protected function baseUrl(): string
    {
        return $this->credential('base_url', config('guesty.base_url'));
    }

    /**
     * Fetch a page of listings from Guesty.
     *
     * @param int $limit
     * @param int $skip
     *
     * @return array
     * @throws \Exception
     */
    public function getListings(int $limit = 25, int $skip = 0): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->get($this->baseUrl() . '/listings', [
                'limit' => $limit,
                'skip' => $skip,
            ]);

        if ($response->status() === 401) {
            // Token might be stale/revoked, force refresh once and retry
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

    /**
     * Generic authenticated GET request to the Guesty Open API, with one retry on 401.
     *
     * @param string $path e.g. '/properties-api/amenities/supported'
     * @param array $query
     *
     * @return array
     * @throws \Exception
     */
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

    /**
     * Download a remote image's raw bytes (used for listing pictures).
     *
     * @param string $url
     *
     * @return string|null
     */
    public function downloadImage(string $url): ?string
    {
        $response = Http::timeout(30)->get($url);

        return $response->successful() ? $response->body() : null;
    }

    /**
     * All amenities supported by Guesty, with their names, groups and channel mappings.
     *
     * @return array
     * @throws \Exception
     */
    public function getSupportedAmenities(): array
    {
        return $this->get('/properties-api/amenities/supported');
    }

    /**
     * All amenity groups/categories supported by Guesty.
     *
     * @return array
     * @throws \Exception
     */
    public function getAmenityGroups(): array
    {
        return $this->get('/properties-api/amenities/groups');
    }

    /**
     * All room/space types supported by Guesty (e.g. Living room, Kitchen, Bedroom).
     *
     * @return array
     * @throws \Exception
     */
    public function getRoomTypes(): array
    {
        return $this->get('/properties/spaces/room-types');
    }

    /**
     * All bed types supported by Guesty (e.g. KING_BED, QUEEN_BED, SOFA_BED).
     *
     * @return array
     * @throws \Exception
     */
    public function getBedTypes(): array
    {
        return $this->get('/properties/spaces/bed-types');
    }
}
