<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertyAdvanceNoticeUnit extends BaseIDName
{
    const DAY = 'Day';
    const DAY_ID = 1;
    const HOUR = 'Hour';
    const HOUR_ID = 2;

    const OPTION = [
        self::DAY_ID => self::DAY,
        self::HOUR_ID => self::HOUR,
    ];
}