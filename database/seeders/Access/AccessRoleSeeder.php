<?php

namespace Database\Seeders\Access;

use App\Models\Access\AccessRole;
use App\Services\Constant\Access\AccessGroup;
use App\Services\Constant\Access\AccessRoleName;
use Illuminate\Database\Seeder;

class AccessRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->saveRole(AccessGroup::STAFF, AccessRoleName::getStaff());
    }

    private function saveRole($group, $roles)
    {
        foreach ($roles as $role) {
            if (AccessRole::ofName($role)->exists()) {
                continue;
            }

            AccessRole::create($role + [
                    'group' => $group,
                ]);
        }
    }
}
