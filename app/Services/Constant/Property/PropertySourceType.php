<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertySourceType extends BaseIDName
{
    const GUESTY = 'Guesty';
    const GUESTY_ID = 1;
    const BOOKEASY = 'Bookeasy';
    const BOOKEASY_ID = 2;

    const OPTION = [
        self::GUESTY_ID => self::GUESTY,
        self::BOOKEASY_ID => self::BOOKEASY,
    ];
}