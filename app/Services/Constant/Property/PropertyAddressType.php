<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseCodeName;

class PropertyAddressType extends BaseCodeName
{
    const FULL = 'full';
    const PUBLISHED = 'published';

    const OPTION = [
        self::FULL,
        self::PUBLISHED,
    ];
}