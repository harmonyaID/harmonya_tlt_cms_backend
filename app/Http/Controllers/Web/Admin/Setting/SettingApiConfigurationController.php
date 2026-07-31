<?php

namespace App\Http\Controllers\Web\Admin\Setting;

use App\Algorithms\Setting\ApiConfigurationAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\ApiConfigurationRequest;
use App\Models\Setting\ApiConfiguration;
use App\Parser\Setting\ApiConfigurationParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class SettingApiConfigurationController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_API_CONFIGURATION_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_API_CONFIGURATION_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_API_CONFIGURATION_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_API_CONFIGURATION_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $configs = ApiConfiguration::filter($request)->getOrPaginate($request);
        return success(ApiConfigurationParser::briefs($configs), pagination: pagination($configs));
    }

    public function detail($id)
    {
        $config = ApiConfiguration::find($id);
        if (!$config) {
            errApiConfigurationGet();
        }

        return success(ApiConfigurationParser::first($config));
    }

    public function create(ApiConfigurationRequest $request)
    {
        $algo = new ApiConfigurationAlgo();
        return $algo->create($request);
    }

    public function update($id, ApiConfigurationRequest $request)
    {
        $algo = new ApiConfigurationAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new ApiConfigurationAlgo((int)$id);
        return $algo->delete();
    }
}
