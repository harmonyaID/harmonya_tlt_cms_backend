<?php

namespace App\Models\Notification;

use App\Models\BaseModel;

class StaffNotification extends BaseModel
{
    protected $table = 'staff_notifications';
    protected $guarded = ['id'];

    protected $casts = [
        'data' => 'array',
        'readAt' => 'datetime',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];
}
