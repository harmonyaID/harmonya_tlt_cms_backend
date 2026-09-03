<?php

namespace App\Http\Controllers\Web\Admin\System;

use App\Http\Controllers\Controller;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\System\SystemInformationService;

class SystemInformationController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_SYSTEM_INFORMATION_VIEW);
                return $next($request);
            })->only(['get']);
        }
    }

    /**
     * @param SystemInformationService $service
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(SystemInformationService $service)
    {
        return success($service->get());
    }
}
