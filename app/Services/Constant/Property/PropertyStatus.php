<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseCodeName;

class PropertyStatus extends BaseCodeName
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const DRAFT = 'draft';

    const OPTION = [
        self::ACTIVE,
        self::INACTIVE,
        self::DRAFT,
    ];
}