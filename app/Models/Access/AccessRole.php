<?php

namespace App\Models\Access;

use App\Models\BaseModel;
use App\Parser\Access\AccessRoleParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessRole extends BaseModel
{
    protected $table = 'access_roles';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = AccessRoleParser::class;


    /** --- RELATIONSHIPS --- */

    public function permissions(): HasMany
    {
        return $this->hasMany(AccessRolePermission::class, 'roleId');
    }


    /** --- SCOPES --- */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if ($request->group != '') {
                $query->where('group', $request->group);
            }

            if ($this->hasSearch($request)) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('display', 'LIKE', "%$request->search%");
                });
            }
        });
    }

    public function scopeOfName($query, $name)
    {
        return $query->where('name', $name);
    }

    public function scopeOfGroup($query, $group)
    {
        return $query->where('group', $group);
    }


    /** --- FUNCTIONS --- */

    public function assignToPermissions($permissions)
    {
        $permissionIds = [];
        foreach ($permissions as $permission) {
            $assign = function ($permission) use (&$permissionIds) {
                AccessRolePermission::withTrashed()->updateOrCreate(
                    [
                        'roleId' => $this->id,
                        'permissionId' => $permission->id
                    ],
                    [
                        self::DELETED_AT => null
                    ]
                );

                $permissionIds[] = $permission->id;
            };

            $assignSub = function ($permission) use (&$assignSub, $assign) {
                $subPermissions = AccessPermission::where('parentId', $permission->id)->get();
                foreach ($subPermissions as $subPermission) {
                    $assign($subPermission);

                    if (stripos($subPermission->name, ".*") !== false) {
                        $assignSub($subPermission);
                    }
                }
            };

            $assign($permission);

            if (stripos($permission->name, ".*") !== false) {
                $assignSub($permission);
            }
        }

        $this->permissions()->whereNotIn('permissionId', $permissionIds)->delete();
    }

}
