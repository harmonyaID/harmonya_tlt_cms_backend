<?php

namespace App\Services\Notification;

use App\Models\Setting\SettingNotificationCredential;
use App\Services\Constant\Setting\FirebaseCredential;
use App\Services\Constant\Setting\NotificationProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Firebase Cloud Messaging (HTTP v1) with the service account saved in
 * Setting > Notification Credentials (provider Firebase).
 */
class FcmClient
{
    private ?array $credentials = null;

    public function isConfigured(): bool
    {
        return (bool) $this->credentials();
    }

    /**
     * @return bool|null true sent, false token is invalid (delete it), null failed / not configured
     */
    public function send(string $token, string $title, string $body, array $data = []): ?bool
    {
        $credentials = $this->credentials();
        $accessToken = $credentials ? $this->accessToken($credentials) : null;

        if (!$accessToken) {
            return null;
        }

        $response = Http::withToken($accessToken)->timeout(15)->post(
            "https://fcm.googleapis.com/v1/projects/{$credentials[FirebaseCredential::PROJECT_ID]}/messages:send",
            ['message' => [
                'token' => $token,
                'notification' => ['title' => $title, 'body' => $body],
                'data' => array_map('strval', $data),
            ]]
        );

        if ($response->successful()) {
            return true;
        }

        $status = $response->json('error.status');

        if ($response->status() === 404 || in_array($status, ['NOT_FOUND', 'UNREGISTERED', 'INVALID_ARGUMENT'], true)) {
            return false;
        }

        logger()->error('FCM send failed: ' . $response->body());

        return null;
    }

    private function credentials(): ?array
    {
        if ($this->credentials !== null) {
            return $this->credentials ?: null;
        }

        $row = SettingNotificationCredential::where('providerId', NotificationProvider::FIREBASE_ID)
            ->where('isActive', true)
            ->first();

        $c = $row?->credentials ?? [];

        $this->credentials = (!empty($c[FirebaseCredential::PROJECT_ID]) && !empty($c[FirebaseCredential::CLIENT_EMAIL]) && !empty($c[FirebaseCredential::PRIVATE_KEY]))
            ? $c
            : [];

        return $this->credentials ?: null;
    }

    private function accessToken(array $c): ?string
    {
        $email = $c[FirebaseCredential::CLIENT_EMAIL];

        return Cache::remember('fcm.access_token.' . md5($email), 3300, function () use ($c, $email) {
            $now = time();
            $claims = [
                'iss' => $email,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ];

            $b64 = fn ($v) => rtrim(strtr(base64_encode(is_array($v) ? json_encode($v) : $v), '+/', '-_'), '=');
            $unsigned = $b64(['alg' => 'RS256', 'typ' => 'JWT']) . '.' . $b64($claims);

            $key = str_replace('\n', "\n", $c[FirebaseCredential::PRIVATE_KEY]);

            if (!openssl_sign($unsigned, $signature, $key, OPENSSL_ALGO_SHA256)) {
                logger()->error('FCM: unable to sign the JWT, check the Firebase private key');
                return null;
            }

            $response = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $unsigned . '.' . $b64($signature),
            ]);

            if (!$response->successful()) {
                logger()->error('FCM: access token failed: ' . $response->body());
                return null;
            }

            return $response->json('access_token');
        });
    }
}
