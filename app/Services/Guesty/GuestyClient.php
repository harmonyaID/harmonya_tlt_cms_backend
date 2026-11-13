<?php

namespace App\Services\Guesty;

use App\Models\Setting\ApiConfiguration;
use App\Models\Setting\GuestyAccessToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GuestyClient
{
    const TOKEN_CACHE_KEY = 'guesty_access_token';
    const TOKEN_LOCK_KEY = 'guesty_access_token_lock';
    const MAX_TOKENS_PER_DAY = 4;
    const EXPIRY_BUFFER = 300;
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
     * Guesty allows 5 token requests per 24h, so a token is generated once and
     * reused from cache, then from the database, until shortly before it expires.
     *
     * @throws \Exception
     */
    public function getAccessToken(bool $force = false): string
    {
        if (!$force && ($token = $this->storedToken())) {
            return $token;
        }

        return Cache::lock(self::TOKEN_LOCK_KEY, 30)->block(15, function () use ($force) {
            $latest = $this->latestValidToken();

            if ($latest && (!$force || $latest->createdAt->gt(now()->subMinute()))) {
                return $latest->accessToken;
            }

            return $this->requestNewToken();
        });
    }

    /**
     * @throws \Exception
     */
    public function refreshToken(): string
    {
        return $this->getAccessToken(true);
    }

    /**
     * @throws \Exception
     */
    public function publicToken(): array
    {
        $token = $this->getAccessToken();
        $record = $this->latestValidToken();

        return [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'expiresAt' => $record?->expiresAt,
            'expiresInSeconds' => $record ? max(0, (int) now()->diffInSeconds($record->expiresAt, false)) : 0,
        ];
    }

    public function tokenStatus(): array
    {
        $latest = $this->latestValidToken();
        $recent = GuestyAccessToken::where('createdAt', '>=', now()->subDay())->orderBy('createdAt')->get();
        $remaining = max(0, self::MAX_TOKENS_PER_DAY - $recent->count());

        return [
            'hasToken' => (bool) $latest,
            'expiresAt' => $latest?->expiresAt,
            'expiresInSeconds' => $latest ? max(0, (int) now()->diffInSeconds($latest->expiresAt, false)) : 0,
            'generatedLast24h' => $recent->count(),
            'remainingQuota' => $remaining,
            'nextTokenAvailableAt' => $remaining === 0 ? $recent->first()->createdAt->copy()->addDay() : null,
        ];
    }

    protected function storedToken(): ?string
    {
        if ($token = Cache::get(self::TOKEN_CACHE_KEY)) {
            return $token;
        }

        $latest = $this->latestValidToken();

        if ($latest) {
            Cache::put(self::TOKEN_CACHE_KEY, $latest->accessToken, $latest->expiresAt->copy()->subSeconds(self::EXPIRY_BUFFER));
        }

        return $latest?->accessToken;
    }

    protected function latestValidToken(): ?GuestyAccessToken
    {
        return GuestyAccessToken::where('credentialHash', $this->credentialHash())
            ->where('expiresAt', '>', now()->addSeconds(self::EXPIRY_BUFFER))
            ->latest('createdAt')
            ->first();
    }

    protected function credentialHash(): string
    {
        return hash('sha256', $this->credential('client_id', config('guesty.client_id')) . '|' . $this->credential('client_secret', config('guesty.client_secret')));
    }

    /**
     * @throws \Exception
     */
    protected function requestNewToken(): string
    {
        $recent = GuestyAccessToken::where('createdAt', '>=', now()->subDay())->orderBy('createdAt')->get();

        if ($recent->count() >= self::MAX_TOKENS_PER_DAY) {
            throw new GuestyTokenLimitException(
                'Guesty token limit reached. Next token can be generated at ' . $recent->first()->createdAt->copy()->addDay()->toDateTimeString()
            );
        }

        $response = Http::asForm()->post($this->credential('auth_url', config('guesty.auth_url')), [
            'grant_type' => 'client_credentials',
            'scope' => 'open-api',
            'client_id' => $this->credential('client_id', config('guesty.client_id')),
            'client_secret' => $this->credential('client_secret', config('guesty.client_secret')),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Unable to authenticate with Guesty: ' . $response->body());
        }

        $data = $response->json();
        $expiresAt = now()->addSeconds($data['expires_in'] ?? 86400);

        GuestyAccessToken::create([
            'credentialHash' => $this->credentialHash(),
            'accessToken' => $data['access_token'],
            'expiresAt' => $expiresAt,
        ]);

        GuestyAccessToken::where('createdAt', '<', now()->subDays(7))->delete();

        Cache::put(self::TOKEN_CACHE_KEY, $data['access_token'], $expiresAt->copy()->subSeconds(self::EXPIRY_BUFFER));

        return $data['access_token'];
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
            $this->refreshToken();

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
            $this->refreshToken();

            $response = Http::withToken($this->getAccessToken())
                ->get($this->baseUrl() . $path, $query);
        }

        if (!$response->successful()) {
            throw new \Exception("Unable to fetch Guesty $path: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Authenticated write request (POST/PUT) with one retry on 401.
     *
     * @throws \Exception
     */
    protected function send(string $method, string $path, array $payload): array
    {
        $call = fn () => Http::withToken($this->getAccessToken())
            ->acceptJson()
            ->asJson()
            ->send($method, $this->baseUrl() . $path, ['json' => $payload]);

        $response = $call();

        if ($response->status() === 401) {
            $this->refreshToken();
            $response = $call();
        }

        if (!$response->successful()) {
            throw new \Exception("Guesty $method $path failed ({$response->status()}): " . $response->body());
        }

        return $response->json() ?? [];
    }

    /**
     * @throws \Exception
     */
    public function createListing(array $payload): array
    {
        return $this->send('POST', '/listings', $payload);
    }

    /**
     * @throws \Exception
     */
    public function updateListing(string $guestyId, array $payload): array
    {
        return $this->send('PUT', '/listings/' . $guestyId, $payload);
    }

    public function isConfigured(): bool
    {
        return (bool) $this->credential('client_id', config('guesty.client_id'))
            && (bool) $this->credential('client_secret', config('guesty.client_secret'));
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
