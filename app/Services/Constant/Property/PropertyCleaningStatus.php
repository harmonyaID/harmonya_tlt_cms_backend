<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseCodeName;

class PropertyCleaningStatus extends BaseCodeName
{
    const NOT_SET = 'not_set';
    const UNKNOWN = 'unknown';
    const DIRTY = 'dirty';
    const WAITING_FOR_INSPECTION = 'waiting_for_inspection';
    const CLEAN = 'clean';

    const OPTION = [
        self::NOT_SET,
        self::UNKNOWN,
        self::DIRTY,
        self::WAITING_FOR_INSPECTION,
        self::CLEAN,
    ];
}