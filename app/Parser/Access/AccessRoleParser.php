<?php

namespace App\Parser\Access;

use App\Services\Constant\Access\AccessGroup;
use Logia\Core\Parser\BaseParser;

class AccessRoleParser extends BaseParser
{
    /**
     * @param $data
     *
     * @return array|null
     */
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'group' => AccessGroup::codeName($data->group),
            'name' => $data->name,
            'display' => $data->display,
        ];
    }

    /**
     * @param $role
     * @param $permissions
     *
     * @return array|null
     */
    public static function mappingPermission($role, $permissions)
    {
        if (!$role) {
            return null;
        }

        $results = [];
        foreach ($permissions as $permission) {
            $results[] = $permission->only('id', 'name', 'display') + [
                    'assigned' => $role->permissions->where('permissionId', $permission->id)->count() > 0
                ];
        }

        return $results;
    }

    /**
     * @param $account
     * @param $roles
     *
     * @return array|null
     */
    public static function mappingAccount($account, $roles)
    {
        if (!$account) {
            return null;
        }

        $results = [];
        foreach ($roles as $role) {
            $results[] = $role->only('id', 'name', 'display') + [
                    'assigned' => $account->roles->where('roleId', $role->id)->count() > 0
                ];
        }

        return $results;
    }

}
