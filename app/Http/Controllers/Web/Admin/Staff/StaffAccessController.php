<?php

namespace App\Http\Controllers\Web\Admin\Staff;

use App\Algorithms\Access\AccessAccountAlgo;
use App\Http\Controllers\Controller;
use App\Models\Access\AccessPermission;
use App\Models\Access\AccessRole;
use App\Models\Staff\Staff;
use App\Parser\Access\AccessPermissionParser;
use App\Parser\Access\AccessRoleParser;
use App\Services\Constant\Access\AccessGroup;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class StaffAccessController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ACCESS_VIEW);
                return $next($request);
            })->only(['getRole', 'getPermission']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ACCESS_UPDATE);
                return $next($request);
            })->only(['assignToRole', 'assignToPermissions']);

        }
    }


    /**
     * @param $id
     * @param Request $request
     *
     * @return void
     */
    public function getRole($id, Request $request)
    {
        $staff = Staff::with('roles')->find($id);
        if (!$staff) {
            errStaffGet();
        }

        $roles = AccessRole::ofGroup(AccessGroup::STAFF)->get();

        return success(AccessRoleParser::mappingAccount($staff, $roles));
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function assignToRole($id, Request $request)
    {
        $staff = Staff::with('roles')->find($id);
        if (!$staff) {
            errStaffGet();
        }

        $algo = new AccessAccountAlgo($staff, AccessGroup::STAFF);
        return $algo->assignToRole($request);
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPermission($id, Request $request)
    {
        $staff = Staff::with('permissions')->find($id);
        if (!$staff) {
            errStaffGet();
        }

        $permissions = AccessPermission::ofGroup(AccessGroup::STAFF)->get();

        return success(AccessPermissionParser::mappingAccount($staff, $permissions));
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function assignToPermissions($id, Request $request)
    {
        $staff = Staff::with('permissions')->find($id);
        if (!$staff) {
            errStaffGet();
        }

        $algo = new AccessAccountAlgo($staff, AccessGroup::STAFF);
        return $algo->assignToPermissions($request);
    }

}
