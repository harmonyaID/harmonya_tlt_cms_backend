<?php

namespace App\Algorithms\Access;

use App\Models\Access\AccessPermission;
use App\Models\Access\AccessRole;
use App\Parser\Access\AccessRoleParser;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessRoleAlgo
{
    /**
     * @param AccessRole|int|null $role
     */
    public function __construct(protected AccessRole|int|null $role = null)
    {
        if (is_int($this->role)) {
            $this->role = AccessRole::find($this->role);
            if (!$this->role) {
                errAccessRoleGet();
            }
        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function saveMappingPermission(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {
                $permissions = AccessPermission::whereIn('id', $request->permissionIds ?: [])->get();

                $this->role->assignToPermissions($permissions);

                activity()->setCausedBy()
                    ->setReference($this->role)
                    ->setType(ActivityType::ACCESS)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update role and permission mapping. Role: " . $this->role->name);
            });


            $permissions = AccessPermission::ofGroup($this->role->group)->get();

            return success(AccessRoleParser::mappingPermission($this->role, $permissions));

        } catch (\Error $error) {
            exception($error);
        }
    }

}
