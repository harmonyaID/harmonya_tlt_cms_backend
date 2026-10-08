<?php

namespace App\Services\Constant\Setting;

class PostmarkCredential
{
    const SERVER_TOKEN = 'serverToken';
    const FROM_EMAIL = 'fromEmail';
    const FROM_NAME = 'fromName';

    const OPTION = [
        self::SERVER_TOKEN => 'Server Token',
        self::FROM_EMAIL => 'From Email',
        self::FROM_NAME => 'From Name',
    ];
}