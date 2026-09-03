<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertyAddressType extends BaseIDName
{
    const FULL = 'Full';
    const FULL_ID = 1;
    const PUBLISHED = 'Published';
    const PUBLISHED_ID = 2;

    const OPTION = [
        self::FULL_ID => self::FULL,
        self::PUBLISHED_ID => self::PUBLISHED,
    ];
}