<?php

namespace App\Parser\Setting;

use Logia\Core\Parser\BaseParser;

class ApiConfigurationParser extends BaseParser
{
    /**
     * Credential keys that should always be masked in API responses
     * (never return the raw secret once it has been saved).
     */
    const SENSITIVE_KEYS = ['client_secret', 'secret', 'api_key', 'password', 'token', 'private_key'];

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'key' => $data->key,
            'module' => $data->module,
            'credentials' => self::maskCredentials($data->credentials ?? []),
            'isActive' => $data->isActive,
            'lastTestedAt' => optional($data->lastTestedAt)->format('d/m/Y H:i'),
            'lastTestSuccessful' => $data->lastTestSuccessful,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'key' => $data->key,
            'module' => $data->module,
            'isActive' => $data->isActive,
            'isConfigured' => !empty($data->credentials),
            'lastTestedAt' => optional($data->lastTestedAt)->format('d/m/Y H:i'),
            'lastTestSuccessful' => $data->lastTestSuccessful,
        ];
    }

    /**
     * Mask sensitive credential values, e.g. "sk_live_abcdef123456" -> "sk_l************3456".
     * Non-sensitive keys (like base_url, auth_url) are returned as-is.
     */
    private static function maskCredentials(array $credentials): array
    {
        $masked = [];

        foreach ($credentials as $key => $value) {
            $isSensitive = collect(self::SENSITIVE_KEYS)->contains(fn ($k) => str_contains(strtolower($key), $k));

            if ($isSensitive && is_string($value) && strlen($value) > 0) {
                $visible = 4;
                $masked[$key] = strlen($value) <= $visible
                    ? str_repeat('*', strlen($value))
                    : substr($value, 0, 2) . str_repeat('*', max(strlen($value) - $visible, 4)) . substr($value, -2);
            } else {
                $masked[$key] = $value;
            }
        }

        return $masked;
    }
}
