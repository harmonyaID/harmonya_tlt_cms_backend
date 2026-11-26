<?php

namespace App\Services\Constant\Setting;

/**
 * Mailtrap SMTP credentials (Email Testing inbox or Email Sending).
 * Shown in Mailtrap under "SMTP Settings" / "Integration".
 */
class MailtrapCredential
{
    const HOST = 'host';
    const PORT = 'port';
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const FROM_EMAIL = 'fromEmail';
    const FROM_NAME = 'fromName';
    const TO_EMAIL = 'toEmail';

    /** Email Testing (sandbox) inbox; Email Sending uses live.smtp.mailtrap.io:587. */
    const DEFAULT_HOST = 'sandbox.smtp.mailtrap.io';
    const DEFAULT_PORT = 2525;

    const OPTION = [
        self::HOST => 'Host',
        self::PORT => 'Port',
        self::USERNAME => 'Username',
        self::PASSWORD => 'Password',
        self::FROM_EMAIL => 'From Email',
        self::FROM_NAME => 'From Name',
        self::TO_EMAIL => 'To Email (admin, comma separated)',
    ];
}
