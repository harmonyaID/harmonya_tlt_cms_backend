<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertyListingType extends BaseIDName
{
    const ENTIRE_HOME = 'Entire home/apt';
    const ENTIRE_HOME_ID = 1;
    const PRIVATE_ROOM = 'Private room';
    const PRIVATE_ROOM_ID = 2;
    const SHARED_ROOM = 'Shared room';
    const SHARED_ROOM_ID = 3;

    const OPTION = [
        self::ENTIRE_HOME_ID => self::ENTIRE_HOME,
        self::PRIVATE_ROOM_ID => self::PRIVATE_ROOM,
        self::SHARED_ROOM_ID => self::SHARED_ROOM,
    ];
}