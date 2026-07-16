<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseCodeName;

class PropertyGuestySyncStatus extends BaseCodeName
{
    const PENDING = 'pending';
    const SYNCED = 'synced';
    const FAILED = 'failed';

    const OPTION = [
        self::PENDING,
        self::SYNCED,
        self::FAILED,
    ];
}