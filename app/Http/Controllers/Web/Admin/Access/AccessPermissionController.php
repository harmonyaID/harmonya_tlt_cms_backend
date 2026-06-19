<?php

namespace App\Http\Controllers\Web\Admin\Access;

use App\Http\Controllers\Controller;
use App\Models\Access\AccessPermission;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class AccessPermissionController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            // GET
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ACCESS_VIEW);
                return $next($request);
            });

        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $permissions = AccessPermission::filter($request)->get();
        return success($permissions);
    }
}
