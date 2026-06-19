<?php

namespace App\Models\Access\Traits;

use App\Models\Access\AccessPermission;
use App\Models\Access\AccessPermissionAccount;
use App\Models\Access\AccessRole;
use App\Models\Access\AccessRoleAccount;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/** --- FOR ACCOUNT (STAFF & PARTNER) ONLY ---  */
trait HasAccession
{
    /** --- RELATIONSHIPS --- */

    public function roles(): MorphMany
    {
        return $this->morphMany(AccessRoleAccount::class, 'account', 'accountType', 'accountId', 'id');
    }

    public function permissions(): MorphMany
    {
        return $this->morphMany(AccessPermissionAccount::class, 'account', 'accountType', 'accountId', 'id');
    }


    /** --- FUNCTIONS --- */

    public function hasRole($names)
    {
        if (is_string($names)) {
            $names = [$names];
        }

        $roles = AccessRole::whereIn('name', $names)->get();
        foreach ($roles as $role) {
            if ($this->roles->where('roleId', $role->id)->count() == 0) {
                return false;
            }
        }

        return true;
    }

    public function hasPermission($names)
    {
        if (is_string($names)) {
            $names = [$names];
        }

        $permissions = AccessPermission::whereIn('name', $names)->get();
        foreach ($permissions as $permission) {
            if ($this->permissions->where('permissionId', $permission->id)->count() == 0) {
                return false;
            }
        }

        return true;
    }

    public function assignToRole($role)
    {
        AccessRoleAccount::withTrashed()->updateOrCreate(
            [
                'accountId' => $this->id,
                'accountType' => $this::class,
                'group' => $role->group,
            ],
            [
                'roleId' => $role->id,
                self::DELETED_AT => null
            ]
        );
    }

    public function assignToPermissions($permissions)
    {
        $permissionIds = [];
        foreach ($permissions as $permission) {
            $assign = function ($permission) use (&$permissionIds) {
                AccessPermissionAccount::withTrashed()->updateOrCreate(
                    [
                        'accountId' => $this->id,
                        'accountType' => $this::class,
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
