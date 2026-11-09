<?php

namespace App\Services\Constant\Access;

use App\Services\Constant\BaseCodeName;

class AccessGroup extends BaseCodeName
{
    const STAFF = 'staff';
    const PARTNER = 'partner';

    const OPTION = [
        self::STAFF,
        self::PARTNER,
    ];
}
