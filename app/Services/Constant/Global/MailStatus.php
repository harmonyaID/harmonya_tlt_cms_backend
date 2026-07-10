<?php

namespace App\Services\Constant\Global;

use App\Services\Constant\BaseCodeName;
use App\Services\Constant\BaseIDName;

class MailStatus extends BaseIDName
{
    const MAIL_STATUS_PENDING = 'Pending';
    const MAIL_STATUS_PENDING_ID= 1;
    const MAIL_STATUS_FOLLOW_UP = 'Follow Up';
    const MAIL_STATUS_FOLLOW_UP_ID= 2;
    const MAIL_STATUS_CLOSED = 'Closed';
    const MAIL_STATUS_CLOSED_ID= 3;

    const OPTION = [
        self::MAIL_STATUS_PENDING_ID => self::MAIL_STATUS_PENDING,
        self::MAIL_STATUS_FOLLOW_UP_ID => self::MAIL_STATUS_FOLLOW_UP,
        self::MAIL_STATUS_CLOSED_ID => self::MAIL_STATUS_CLOSED,
    ];
}
