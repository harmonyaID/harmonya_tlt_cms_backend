<?php

namespace App\Models\Visitor;

use Illuminate\Database\Eloquent\Model;

class ContentVisitor extends Model
{
    protected $table = 'content_visitors';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null; 

    protected $casts = [
        self::CREATED_AT => 'datetime',
    ];

    public function contentable()
    {
        return $this->morphTo('contentable', 'contentableType', 'contentableId');
    }

    public static function recordVisit($contentable, string $ipAddress, ?string $userAgent = null): void
    {
        static::firstOrCreate(
            [
                'contentableType' => get_class($contentable),
                'contentableId' => $contentable->id,
                'ipAddress' => $ipAddress,
            ],
            [
                'userAgent' => $userAgent,
                'createdAt' => now(),
            ]
        );
    }
}
