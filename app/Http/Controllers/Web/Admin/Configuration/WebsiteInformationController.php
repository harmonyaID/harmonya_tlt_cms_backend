<?php

namespace App\Http\Controllers\Web\Admin\Configuration;

use App\Algorithms\Configuration\WebsiteInformationAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\WebsiteInfoRequest;
use App\Models\Configuration\WebsiteInformation;
use App\Parser\Configuration\WebsiteInformationParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class WebsiteInformationController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_CONFIGURATION_VIEW);
                return $next($request);
            })->only(['get']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_CONFIGURATION_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_CONFIGURATION);
                return $next($request);
            })->only(['updateSuperadmin']);
        }
    }

    public function get(Request $request)
    {
        $webInfo = WebsiteInformation::first();
        return success(WebsiteInformationParser::brief($webInfo));
    }

    public function update($id, WebsiteInfoRequest $request)
    {
        $algo = new WebsiteInformationAlgo((int)$id);
        return $algo->update($request);
    }
}
