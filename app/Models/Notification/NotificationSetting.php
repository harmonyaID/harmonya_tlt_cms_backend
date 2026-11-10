<?php

namespace App\Models\Notification;

use App\Models\BaseModel;
use App\Services\Constant\Notification\NotificationType;

class NotificationSetting extends BaseModel
{
    protected $table = 'notification_settings';
    protected $guarded = ['id'];

    protected $casts = [
        'isActive' => 'boolean',
        'sendInApp' => 'boolean',
        'sendPush' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public static function forType(string $type): self
    {
        $setting = static::firstOrCreate(
            ['type' => $type],
            ['name' => NotificationType::OPTION[$type] ?? $type]
        );

        // a just created row only has the given attributes, not the column defaults
        return $setting->wasRecentlyCreated ? $setting->fresh() : $setting;
    }

    public static function ensureAll(): void
    {
        foreach (array_keys(NotificationType::OPTION) as $type) {
            static::forType($type);
        }
    }
}
