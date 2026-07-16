<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseCodeName;

class PropertyUnitType extends BaseCodeName
{
    const SINGLE_UNIT = 'single_unit';
    const MULTI_UNIT = 'multi_unit';

    const OPTION = [
        self::SINGLE_UNIT,
        self::MULTI_UNIT,
    ];
}