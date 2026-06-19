<?php

namespace App\Models\Access;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccessRoleAccount extends BaseModel
{
    protected $table = 'access_role_accounts';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];


    /** --- RELATIONSHIPS --- */

    public function permission(): BelongsTo
    {
        return $this->belongsTo(AccessRole::class, 'parentId');
    }

    public function account(): MorphTo
    {
        return $this->morphTo('account', 'accountType', 'accountId', 'id');
    }

}
