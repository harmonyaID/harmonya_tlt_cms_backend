<?php

namespace App\Http\Controllers\Web\Admin\Access;

use App\Http\Controllers\Controller;
use App\Services\Constant\Access\AccessGroup;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class AccessController extends Controller
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
    public function getGroup(Request $request)
    {
        return success(AccessGroup::get());
    }
}
