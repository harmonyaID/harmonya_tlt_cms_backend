<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertyStatus extends BaseIDName
{
    const ACTIVE = 'Active';
    const ACTIVE_ID = 1;
    const INACTIVE = 'Inactive';
    const INACTIVE_ID = 2;
    const DRAFT = 'Draft';
    const DRAFT_ID = 3;

    const OPTION = [
        self::ACTIVE_ID => self::ACTIVE,
        self::INACTIVE_ID => self::INACTIVE,
        self::DRAFT_ID => self::DRAFT,
    ];
}