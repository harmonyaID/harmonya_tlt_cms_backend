<?php

namespace App\Parser\Access;

use App\Services\Constant\Access\AccessGroup;
use Logia\Core\Parser\BaseParser;

class AccessPermissionParser extends BaseParser
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
            'parent' => $data->parent?->only('id', 'display'),
            'group' => AccessGroup::codeName($data->group),
            'name' => $data->name,
            'display' => $data->display,
        ];
    }

    /**
     * @param $account
     * @param $permissions
     *
     * @return array|null
     */
    public static function mappingAccount($account, $permissions)
    {
        if (!$account) {
            return null;
        }

        $results = [];
        foreach ($permissions as $permission) {
            $results[] = $permission->only('id', 'name', 'display') + [
                    'assigned' => $account->permissions->where('permissionId', $permission->id)->count() > 0
                ];
        }

        return $results;
    }

}
