<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertyCleaningStatus extends BaseIDName
{
    const NOT_SET = 'Not set';
    const NOT_SET_ID = 1;
    const UNKNOWN = 'Unknown';
    const UNKNOWN_ID = 2;
    const DIRTY = 'Dirty';
    const DIRTY_ID = 3;
    const WAITING_FOR_INSPECTION = 'Waiting for inspection';
    const WAITING_FOR_INSPECTION_ID = 4;
    const CLEAN = 'Clean';
    const CLEAN_ID = 5;

    const OPTION = [
        self::NOT_SET_ID => self::NOT_SET,
        self::UNKNOWN_ID => self::UNKNOWN,
        self::DIRTY_ID => self::DIRTY,
        self::WAITING_FOR_INSPECTION_ID => self::WAITING_FOR_INSPECTION,
        self::CLEAN_ID => self::CLEAN,
    ];
}