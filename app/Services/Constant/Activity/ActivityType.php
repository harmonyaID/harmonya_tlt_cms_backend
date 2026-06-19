<?php

namespace App\Services\Constant\Activity;

use App\Services\Constant\BaseCodeName;

class ActivityType extends BaseCodeName
{
    const ACCESS = 'access';
    const STAFF = 'staff';
    const OPTION = [
        self::ACCESS,
        self::STAFF,
    ];

}
