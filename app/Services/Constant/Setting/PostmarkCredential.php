<?php

namespace App\Services\Constant\Setting;

class PostmarkCredential
{
    const SERVER_TOKEN = 'serverToken';
    const FROM_EMAIL = 'fromEmail';
    const FROM_NAME = 'fromName';
    /** Admin address(es) that receive the form notifications, comma separated. */
    const TO_EMAIL = 'toEmail';

    const OPTION = [
        self::SERVER_TOKEN => 'Server Token',
        self::FROM_EMAIL => 'From Email',
        self::FROM_NAME => 'From Name',
        self::TO_EMAIL => 'To Email (admin, comma separated)',
    ];
}