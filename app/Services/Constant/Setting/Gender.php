<?php

namespace App\Services\Constant\Setting;

use App\Services\Constant\BaseIDName;

class Gender extends BaseIDName
{
    const MALE_ID = 1;
    const MALE = 'Male';
    const FEMALE_ID = 2;
    const FEMALE = 'Female';
    const OTHER_ID = 3;
    const OTHER = 'Other';

    const OPTION = [
        self::MALE_ID => self::MALE,
        self::FEMALE_ID => self::FEMALE,
        self::OTHER_ID => self::OTHER,
    ];

}
