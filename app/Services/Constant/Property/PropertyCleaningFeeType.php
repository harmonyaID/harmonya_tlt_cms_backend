<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertyCleaningFeeType extends BaseIDName
{
    const PER_STAY = 'Per stay';
    const PER_STAY_ID = 1;
    const PER_NIGHT = 'Per night';
    const PER_NIGHT_ID = 2;

    const OPTION = [
        self::PER_STAY_ID => self::PER_STAY,
        self::PER_NIGHT_ID => self::PER_NIGHT,
    ];
}