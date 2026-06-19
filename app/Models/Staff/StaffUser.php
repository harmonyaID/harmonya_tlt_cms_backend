<?php

namespace App\Models\Staff;

use App\Models\HasAuthentication;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class StaffUser extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;
    use HasAuthentication;

    // Custom date times column
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $table = 'staff_users';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];


    /** --- RELATIONSHIPS --- */

    public function account(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staffId');
    }

    public function passwords(): HasMany
    {
        return $this->hasMany(StaffUserPassword::class, 'userId');
    }


    /** --- JWT FUNCTIONS --- */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => $this?->id,
            'email' => $this?->email,
            'url' => request()->getHttpHost() . '/' . request()->path()
        ];
    }

}
