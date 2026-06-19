<?php

namespace App\Algorithms\Access;

use App\Models\Access\AccessPermission;
use App\Models\Access\AccessRole;
use App\Models\Staff\Staff;
use App\Parser\Access\AccessPermissionParser;
use App\Parser\Access\AccessRoleParser;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessAccountAlgo
{
    /**
     * @var bool
     */
    protected bool $fromPartner = false;

    /**
     * @param Staff $account
     * @param $group
     */
    public function __construct(protected Staff $account, protected $group)
    {
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function assignToRole(Request $request)
    {
        try {

            $role = AccessRole::ofGroup($this->group)->find($request->roleId);
            if (!$role) {
                errAccessRoleGet();
            }

            $permissions = AccessPermission::ofGroup($this->group)->whereIn('id', $role->permissions?->pluck('permissionId')->toArray())->get();

            DB::table(function () use ($role, $permissions, $request) {
                $this->account->assignToRole($role);
                $this->account->assignToPermissions($permissions);

                activity()->setCausedBy()
                    ->setReference($this->account)
                    ->setType($this->setActivityType())
                    ->setAction(ActivityAction::UPDATE)
                    ->log(sprintf("Update role to %s. Account: %s", $role->name, ($this->account->fullName ?: $this->account->name)));
            });

            $this->account->refresh();

            $roles = AccessRole::ofGroup($this->group)->get();
            $permissions = AccessPermission::ofGroup($this->group)->get();

            return success([
                'roles' => AccessRoleParser::mappingAccount($this->account, $roles),
                'permissions' => AccessPermissionParser::mappingAccount($this->account, $permissions)
            ]);

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function assignToPermissions(Request $request)
    {
        try {

            $permissions = AccessPermission::ofGroup($this->group)->whereIn('id', $request->permissionIds ?: [])->get();

            DB::table(function () use ($permissions, $request) {
                $this->account->assignToPermissions($permissions);

                activity()->setCausedBy()
                    ->setReference($this->account)
                    ->setType($this->setActivityType())
                    ->setAction(ActivityAction::UPDATE)
                    ->log(sprintf("Update permissions. Account: %s", ($this->account->fullName ?: $this->account->name)));
            });

            $this->account->refresh();

            $permissions = AccessPermission::ofGroup($this->group)->get();

            return success(AccessPermissionParser::mappingAccount($this->account, $permissions));

        } catch (\Exception $exception) {
            exception($exception);
        }
    }


    /** --- SETTERS --- */

    public function setFromPartner(bool $fromPartner = true)
    {
        $this->fromPartner = $fromPartner;
    }


    /** --- FUNCTIONS --- */

    private function setActivityType()
    {
        switch ($this->account::class) {
            case Staff::class:
                return ActivityType::STAFF;
            default:
                return ActivityType::ACCESS;
        }
    }

}
