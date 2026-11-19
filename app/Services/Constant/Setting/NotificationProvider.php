<?php

namespace App\Services\Constant\Setting;

use App\Services\Constant\BaseIDName;

class NotificationProvider extends BaseIDName
{
    const FIREBASE_ID = 1;
    const FIREBASE = 'Firebase';

    const POSTMARK_ID = 2;
    const POSTMARK = 'Postmark';

    const MAILTRAP_ID = 3;
    const MAILTRAP = 'Mailtrap';

    const OPTION = [
        self::FIREBASE_ID => self::FIREBASE,
        self::POSTMARK_ID => self::POSTMARK,
        self::MAILTRAP_ID => self::MAILTRAP,
    ];

    /** Providers that send email. */
    const MAIL_IDS = [
        self::POSTMARK_ID,
        self::MAILTRAP_ID,
    ];
}
