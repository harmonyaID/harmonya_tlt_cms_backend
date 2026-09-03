<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertyUnitType extends BaseIDName
{
    const SINGLE_UNIT = 'Single Unit';
    const SINGLE_UNIT_ID = 1;
    const MULTI_UNIT = 'Multi Unit';
    const MULTI_UNIT_ID = 2;

    const OPTION = [
        self::SINGLE_UNIT_ID => self::SINGLE_UNIT,
        self::MULTI_UNIT_ID => self::MULTI_UNIT,
    ];
}