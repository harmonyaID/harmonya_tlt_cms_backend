<?php

namespace App\Models\Access;

use App\Models\BaseModel;

class AccessRolePermission extends BaseModel
{
    protected $table = 'access_role_permissions';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

}
