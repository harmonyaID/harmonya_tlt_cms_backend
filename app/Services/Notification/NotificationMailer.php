<?php

namespace App\Services\Notification;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

/**
 * Emails a form submission to the admin addresses: "To Email" of the active
 * mail credential (Settings > Notification Credentials), or MAIL_NOTIFICATION_TO.
 *
 * The mail is sent once the HTTP response has been returned, so the visitor
 * does not wait for the mail server and no queue worker is needed.
 */
class NotificationMailer
{
    public function toAdmin(Mailable $mail): void
    {
        $to = app(MailProviderSettings::class)->recipients();

        if (!$to) {
            logger()->warning('No recipient (To Email in Notification Credentials / MAIL_NOTIFICATION_TO), ' . class_basename($mail) . ' was not sent.');
            return;
        }

        app()->terminating(fn () => $this->send($to, $mail));
    }

    /**
     * Send right away. Returns null on success, or the error message.
     */
    public function send(array $to, Mailable $mail): ?string
    {
        try {
            Mail::to($to)->send($mail);

            return null;
        } catch (\Throwable $e) {
            $error = 'Failed to send ' . class_basename($mail) . ' with mailer "' . config('mail.default') . '": ' . $e->getMessage();
            logger()->error($error);

            return $error;
        }
    }
}
