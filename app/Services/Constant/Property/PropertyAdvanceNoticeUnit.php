<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseCodeName;

class PropertyAdvanceNoticeUnit extends BaseCodeName
{
    const DAY = 'day';
    const HOUR = 'hour';

    const OPTION = [
        self::DAY,
        self::HOUR,
    ];
}