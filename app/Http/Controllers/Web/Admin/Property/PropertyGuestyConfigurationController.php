<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyGuestyConfigurationRequest;
use App\Models\Setting\ApiConfiguration;
use App\Parser\Setting\ApiConfigurationParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Guesty\GuestyClient;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

    public function update(PropertyGuestyConfigurationRequest $request)
    {
        $config = ApiConfiguration::where('key', self::CONFIG_KEY)->first();

        $incoming = array_filter([
            'client_id'     => $request->clientId,
            'client_secret' => $request->clientSecret,
            'auth_url'      => $request->authUrl,
            'base_url'      => $request->baseUrl,
        ], fn($v) => $v !== null && $v !== '');

        $requiredKeys = ['client_id', 'client_secret', 'auth_url', 'base_url'];

        DB::transaction(function () use (&$config, $incoming, $request, $requiredKeys) {
            if (!$config) {
                $config = ApiConfiguration::create([
                    'name'        => 'Guesty Open API',
                    'key'         => self::CONFIG_KEY,
                    'module'      => 'property',
                    'credentials' => $incoming,
                    'isActive'    => $request->isActive,
                ] + created_by());

                return;
            }

            try {
                $existing = $config->credentials ?? [];
            } catch (DecryptException $e) {
                report($e);

                $missing = array_diff($requiredKeys, array_keys($incoming));
                if ($missing) {
                    throw ValidationException::withMessages([
                        'credentials' => 'Credentials tersimpan tidak bisa dibaca. Silakan isi ulang semua field: '
                            . implode(', ', $missing),
                    ]);
                }

                $existing = [];

                $config->setRawAttributes(
                    array_merge($config->getAttributes(), ['credentials' => null]),
                    true
                );
            }

            $config->update([
                'credentials' => array_merge($existing, $incoming),
                'isActive'    => $request->isActive,
            ]);
        });

        Cache::forget(GuestyClient::TOKEN_CACHE_KEY);

        return success(ApiConfigurationParser::first($config->fresh()));
    }

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
