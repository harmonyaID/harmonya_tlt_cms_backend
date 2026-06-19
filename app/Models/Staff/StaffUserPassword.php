<?php

namespace App\Models\Staff;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffUserPassword extends BaseModel
{
    protected $table = 'staff_user_passwords';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];


    /** --- RELATIONSHIPS --- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(StaffUser::class, 'userId');
    }

}
