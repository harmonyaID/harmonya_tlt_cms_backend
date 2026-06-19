<?php

namespace Database\Seeders\Access;

use App\Models\Access\AccessPermission;
use App\Services\Constant\Access\AccessGroup;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->savePermission(AccessGroup::STAFF, AccessPermissionName::getStaff());
    }


    /** --- FUNCTIONS --- */

    private function savePermission($group, $permissions)
    {
        foreach ($permissions as $permission) {
            if (AccessPermission::ofName($permission)->exists()) {
                continue;
            }

            $parent = $this->setParent($permission['name'], $permissions);

            AccessPermission::create($permission + [
                    'group' => $group,
                    'parentId' => $parent?->id,
                ]);
        }
    }

    private function setParent($name, $permissions)
    {
        $names = explode('.', $name);
        array_pop($names);

        $parentName = implode('.', $names) . '.*';
        $parentPermission = collect($permissions)->where('name', $parentName)->first();
        if (!$parentPermission) {
            return null;
        }

        return AccessPermission::ofName($parentName)->first();
    }

}
