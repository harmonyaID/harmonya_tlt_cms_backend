<?php

namespace App\Services\Notification;

use App\Models\Setting\SettingNotificationCredential;
use App\Services\Constant\Setting\MailtrapCredential;
use App\Services\Constant\Setting\NotificationProvider;
use App\Services\Constant\Setting\PostmarkCredential;

/**
 * Sends every email of the app with the active mail provider set in
 * Settings > Notification Credentials (Postmark or Mailtrap), instead of the
 * MAIL_* values in .env. Both providers are used over SMTP.
 *
 * - No active Postmark / Mailtrap credential: .env is used, as before.
 * - Several active: the most recently updated one is used.
 * - MAIL_MAILER=log or array in .env (local / tests): .env always wins, so
 *   nothing is really sent from a development machine.
 */
class MailProviderSettings
{
    public const MAILER = 'provider';

    private const POSTMARK_HOST = 'smtp.postmarkapp.com';
    private const POSTMARK_PORT = 587;

    /** Provider name (e.g. "Postmark Production") applied, or null when .env is used. */
    public ?string $applied = null;

    public function apply(): void
    {
        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return;
        }

        $credential = $this->activeCredential();

        if (!$credential || !($mailer = $this->mailerConfig($credential->providerId, (array) $credential->credentials))) {
            return;
        }

        config([
            'mail.mailers.' . self::MAILER => $mailer,
            'mail.default' => self::MAILER,
        ]);

        $this->applied = NotificationProvider::display($credential->providerId) . ' (' . $credential->name . ')';
    }

    private function activeCredential(): ?SettingNotificationCredential
    {
        try {
            return SettingNotificationCredential::whereIn('providerId', NotificationProvider::MAIL_IDS)
                ->where('isActive', true)
                ->orderByDesc('updatedAt')
                ->first();
        } catch (\Throwable) {
            // e.g. database not migrated yet: keep the .env settings
            return null;
        }
    }

    private function mailerConfig(int $providerId, array $c): ?array
    {
        [$host, $port, $username, $password] = match ($providerId) {
            NotificationProvider::POSTMARK_ID => [
                self::POSTMARK_HOST,
                self::POSTMARK_PORT,
                $c[PostmarkCredential::SERVER_TOKEN] ?? null,
                $c[PostmarkCredential::SERVER_TOKEN] ?? null,
            ],
            NotificationProvider::MAILTRAP_ID => [
                ($c[MailtrapCredential::HOST] ?? null) ?: MailtrapCredential::DEFAULT_HOST,
                (int) (($c[MailtrapCredential::PORT] ?? null) ?: MailtrapCredential::DEFAULT_PORT),
                $c[MailtrapCredential::USERNAME] ?? null,
                $c[MailtrapCredential::PASSWORD] ?? null,
            ],
            default => [null, null, null, null],
        };

        if (!$host || !$username || !$password) {
            return null;
        }

        $fromEmail = ($c[PostmarkCredential::FROM_EMAIL] ?? null) ?: config('mail.from.address');
        $fromName = ($c[PostmarkCredential::FROM_NAME] ?? null) ?: config('mail.from.name');

        return [
            'transport' => 'smtp',
            'scheme' => $port === 465 ? 'smtps' : 'smtp',
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $password,
            'timeout' => 30,
            'from' => ['address' => $fromEmail, 'name' => $fromName],
        ];
    }
}
