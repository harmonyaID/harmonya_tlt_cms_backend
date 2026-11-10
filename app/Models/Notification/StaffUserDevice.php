<?php

namespace App\Models\Notification;

use App\Models\BaseModel;

class StaffUserDevice extends BaseModel
{
    protected $table = 'staff_user_devices';
    protected $guarded = ['id'];

    protected $casts = [
        'lastUsedAt' => 'datetime',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];
}
