<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyGuestyConfigurationRequest;
use App\Models\Setting\ApiConfiguration;
use App\Parser\Setting\ApiConfigurationParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Guesty\GuestyClient;
use Illuminate\Support\Facades\DB;

class PropertyGuestyConfigurationController extends Controller
{
    const CONFIG_KEY = 'guesty';

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VIEW);
                return $next($request);
            })->only(['get']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_UPDATE);
                return $next($request);
            })->only(['update', 'test']);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get()
    {
        $config = ApiConfiguration::where('key', self::CONFIG_KEY)->first();

        if (!$config) {
            return success([
                'key' => self::CONFIG_KEY,
                'isConfigured' => false,
                'isActive' => false,
                'credentials' => [],
            ]);
        }

        return success(ApiConfigurationParser::first($config));
    }

    /**
     * @param PropertyGuestyConfigurationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(PropertyGuestyConfigurationRequest $request)
    {
        $config = ApiConfiguration::where('key', self::CONFIG_KEY)->first();

        $incoming = array_filter([
            'client_id' => $request->clientId,
            'client_secret' => $request->clientSecret,
            'auth_url' => $request->authUrl,
            'base_url' => $request->baseUrl,
        ], fn ($v) => $v !== null && $v !== '');

        DB::transaction(function () use (&$config, $incoming, $request) {
            if (!$config) {
                $config = ApiConfiguration::create([
                    'name' => 'Guesty Open API',
                    'key' => self::CONFIG_KEY,
                    'module' => 'property',
                    'credentials' => $incoming,
                    'isActive' => $request->isActive,
                ] + created_by());
            } else {
                $merged = array_merge($config->credentials ?? [], $incoming);
                $config->update([
                    'credentials' => $merged,
                    'isActive' => $request->isActive,
                ]);
            }
        });

        // credentials rotated - drop any cached access token so the new ones take effect immediately
        \Illuminate\Support\Facades\Cache::forget(GuestyClient::TOKEN_CACHE_KEY);

        return success(ApiConfigurationParser::first($config->fresh()));
    }

    /**
     * Try to actually authenticate with Guesty using the saved credentials,
     * so the admin gets immediate feedback instead of finding out during an import.
     *
     * @param GuestyClient $client
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function test(GuestyClient $client)
    {
        $config = ApiConfiguration::where('key', self::CONFIG_KEY)->first();
        if (!$config) {
            errApiConfigurationGet('Guesty has not been configured yet');
        }

        \Illuminate\Support\Facades\Cache::forget(GuestyClient::TOKEN_CACHE_KEY);

        try {
            $client->getAccessToken();

            $config->update(['lastTestedAt' => now(), 'lastTestSuccessful' => true]);

            return success(['message' => 'Successfully authenticated with Guesty']);
        } catch (\Throwable $e) {
            $config->update(['lastTestedAt' => now(), 'lastTestSuccessful' => false]);

            error(422, 'Unable to authenticate with Guesty', $e->getMessage());
        }
    }
}
