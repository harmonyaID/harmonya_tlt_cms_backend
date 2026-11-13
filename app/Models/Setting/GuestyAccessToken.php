<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class GuestyAccessToken extends Model
{
    protected $table = 'guesty_access_tokens';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;

    protected $casts = [
        'accessToken' => 'encrypted',
        'expiresAt' => 'datetime',
        self::CREATED_AT => 'datetime',
    ];
}
