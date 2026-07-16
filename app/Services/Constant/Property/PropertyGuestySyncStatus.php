<?php

namespace App\Services\Constant\Property;

use App\Services\Constant\BaseIDName;

class PropertyGuestySyncStatus extends BaseIDName
{
    const PENDING = 'Pending';
    const PENDING_ID = 1;
    const SYNCED = 'Synced';
    const SYNCED_ID = 2;
    const FAILED = 'Failed';
    const FAILED_ID = 3;

    const OPTION = [
        self::PENDING_ID => self::PENDING,
        self::SYNCED_ID => self::SYNCED,
        self::FAILED_ID => self::FAILED,
    ];
}