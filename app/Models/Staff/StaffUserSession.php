<?php

namespace App\Models\Staff;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffUserSession extends BaseModel
{
    protected $table = 'staff_user_sessions';
    protected $guarded = ['id'];

    protected $casts = [
        'lastActiveAt' => 'datetime',
        'expiresAt' => 'datetime',
        'revokedAt' => 'datetime',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];


    /** --- RELATIONSHIPS --- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(StaffUser::class, 'userId');
    }


    /** --- SCOPES --- */

    public function scopeActive($query)
    {
        return $query->whereNull('revokedAt')
            ->where(fn ($q) => $q->whereNull('expiresAt')->orWhere('expiresAt', '>', now()));
    }
}
