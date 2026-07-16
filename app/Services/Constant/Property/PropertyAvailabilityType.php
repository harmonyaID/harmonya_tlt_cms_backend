<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertyAvailabilityType extends BaseIDName
{
    const ALWAYS = 'Available always';
    const ALWAYS_ID = 1;
    const CUSTOM = 'Custom';
    const CUSTOM_ID = 2;

    const OPTION = [
        self::ALWAYS_ID => self::ALWAYS,
        self::CUSTOM_ID => self::CUSTOM,
    ];
}