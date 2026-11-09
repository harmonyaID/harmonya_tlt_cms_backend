<?php

namespace App\Services\Constant\Setting;

use App\Services\Constant\BaseIDName;

class NotificationProvider extends BaseIDName
{
    const FIREBASE_ID = 1;
    const FIREBASE = 'Firebase';

    const POSTMARK_ID = 2;
    const POSTMARK = 'Postmark';

    const OPTION = [
        self::FIREBASE_ID => self::FIREBASE,
        self::POSTMARK_ID => self::POSTMARK,
    ];
}
