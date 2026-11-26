<?php

namespace App\Console\Commands;

use App\Services\Notification\MailProviderSettings;
use App\Services\Notification\NotificationMailer;
use Illuminate\Console\Command;
use Illuminate\Mail\Mailable;

class TestNotificationMailCommand extends Command
{
    protected $signature = 'mail:test-notification {--to= : Send to this address instead of MAIL_NOTIFICATION_TO}';

    protected $description = 'Send a test email with the current mail settings, to check that form notifications can be delivered';

    public function handle(NotificationMailer $mailer, MailProviderSettings $settings)
    {
        app('mail.manager'); // applies the provider from Settings > Notification Credentials

        $to = $this->option('to') ? [$this->option('to')] : $settings->recipients();
        $mailerConfig = config('mail.mailers.' . config('mail.default'), []);

        $this->line('Source : ' . ($settings->applied ?? '.env (no active Postmark / Mailtrap in Notification Credentials)'));
        $this->line('Mailer : ' . config('mail.default') . (isset($mailerConfig['host']) ? " ({$mailerConfig['host']}:{$mailerConfig['port']})" : ''));
        $this->line('From   : ' . ($mailerConfig['from']['address'] ?? config('mail.from.address')));
        $this->line('To     : ' . ($to ? implode(', ', $to) : '-'));

        if (!$to) {
            $this->error('No recipient: fill "To Email" in Notification Credentials or MAIL_NOTIFICATION_TO in .env. Form notifications are not sent.');
            return self::FAILURE;
        }

        $mail = (new Mailable())
            ->subject('Test notification - ' . config('app.name'))
            ->html('<p>This is a test email. Form notifications (contact forms, inquiries) will be sent with these settings.</p>');

        if ($error = $mailer->send($to, $mail)) {
            $this->error($error);
            return self::FAILURE;
        }

        $this->info('Sent. Check the inbox of ' . implode(', ', $to) . '.');

        return self::SUCCESS;
    }
}
