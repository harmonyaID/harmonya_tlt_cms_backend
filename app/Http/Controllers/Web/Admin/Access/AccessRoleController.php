<?php

namespace App\Http\Controllers\Web\Admin\Access;

use App\Algorithms\Access\AccessRoleAlgo;
use App\Http\Controllers\Controller;
use App\Models\Access\AccessPermission;
use App\Models\Access\AccessRole;
use App\Parser\Access\AccessRoleParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class AccessRoleController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            // GET
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ACCESS_VIEW);
                return $next($request);
            })->only(['get']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ACCESS_UPDATE);
                return $next($request);
            })->only(['mappingPermission', 'saveMappingPermission']);

        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $roles = AccessRole::filter($request)->get();
        return success($roles);
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function mappingPermission($id, Request $request)
    {
        $role = AccessRole::with(['permissions'])->find($id);
        if (!$role) {
            errAccessRoleGet();
        }

        $permissions = AccessPermission::ofGroup($role->group)->filter($request)->get();

        return success(AccessRoleParser::mappingPermission($role, $permissions));
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function saveMappingPermission($id, Request $request)
    {
        $algo = new AccessRoleAlgo($id);
        return $algo->saveMappingPermission($request);
    }

}
