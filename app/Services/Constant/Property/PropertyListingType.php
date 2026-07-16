<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseCodeName;

class PropertyListingType extends BaseCodeName
{
    const ENTIRE_HOME = 'entire_home';
    const PRIVATE_ROOM = 'private_room';
    const SHARED_ROOM = 'shared_room';

    const OPTION = [
        self::ENTIRE_HOME,
        self::PRIVATE_ROOM,
        self::SHARED_ROOM,
    ];
}