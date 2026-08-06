<?php

namespace App\Models\Acf;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentAcf extends Model
{
    use SoftDeletes;

    protected $table = 'contentacf';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = [
        'contentableId',
        'contentableType',
        'key',
        'label',
        'value',
        'order',
    ];

    protected $casts = [
        'value' => 'array',
        'order' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public function contentable()
    {
        return $this->morphTo('contentable', 'contentableType', 'contentableId');
    }
}
