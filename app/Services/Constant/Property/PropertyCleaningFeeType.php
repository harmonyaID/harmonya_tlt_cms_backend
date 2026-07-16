<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseCodeName;

class PropertyCleaningFeeType extends BaseCodeName
{
    const PER_STAY = 'per_stay';
    const PER_NIGHT = 'per_night';

    const OPTION = [
        self::PER_STAY,
        self::PER_NIGHT,
    ];
}