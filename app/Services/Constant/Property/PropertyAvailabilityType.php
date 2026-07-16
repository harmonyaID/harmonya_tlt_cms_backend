<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseCodeName;

class PropertyAvailabilityType extends BaseCodeName
{
    const ALWAYS = 'always';
    const CUSTOM = 'custom';

    const OPTION = [
        self::ALWAYS,
        self::CUSTOM,
    ];
}